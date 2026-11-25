<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');
if(strlen($_SESSION['stdid'])==0)
{
    header('location:index.php');
}
else{

    // ดึงข้อมูลหนังสือจาก $_GET ถ้ามี
    if(isset($_GET['bookid'])) {
        $bookid = intval($_GET['bookid']);
        $sql = "SELECT tblbooks.BookName,tblcategory.CategoryName,tblauthors.AuthorName,tblbooks.ISBNNumber,tblbooks.Quantity AS BookQuantity,tblbooks.id as bookid
                FROM  tblbooks
                JOIN tblcategory ON tblcategory.id=tblbooks.CatId
                JOIN tblauthors ON tblauthors.id=tblbooks.AuthorId
                WHERE tblbooks.id = :bookid";
        $query = $dbh -> prepare($sql);
        $query->bindParam(':bookid', $bookid, PDO::PARAM_INT);
        $query->execute();
        $book = $query->fetch(PDO::FETCH_OBJ);
        //  var_dump($book); // ตรวจสอบข้อมูลหนังสือ

        if(!$book){
            // กรณีไม่พบหนังสือด้วย ID ที่ส่งมา
            $_SESSION['error'] = "ไม่พบข้อมูลหนังสือ";
            header('location:view-books.php'); // หรือหน้าแสดงหนังสืออื่นๆ
            exit();
        }
    } else {
        // กรณีไม่มี bookid ส่งมา
        $_SESSION['error'] = "ไม่ได้ระบุหนังสือที่ต้องการจอง";
        header('location:view-books.php'); // หรือหน้าแสดงหนังสืออื่นๆ
        exit();
    }

    if(isset($_POST['book'])) {
        $bookId = $_POST['bookid'];
        $pickupDateTime = $_POST['pickup_datetime'];

        // ตรวจสอบรูปแบบของ $pickupDateTime (YYYY-MM-DD HH:MM:SS)
        if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $pickupDateTime)) {
            $_SESSION['error'] = "รูปแบบวันที่และเวลาไม่ถูกต้อง (YYYY-MM-DD HH:MM:SS)";
            header('location: booking.php?bookid=' . $bookId); // กลับไปหน้า booking พร้อมแจ้ง error
            exit();
        }

        $studentId = $_SESSION['stdid'];
        $bookingDate = date('Y-m-d H:i:s');
        $issueDate = $pickupDateTime; // ใช้ค่าที่ผู้ใช้กรอก
        $returnDate = null; // กำหนดค่า ReturnDate เป็น null
        $returnStatus = 1; // ยังไม่คืน

        // เริ่ม Transaction เพื่อให้การจองและการลดจำนวนหนังสือเป็นชุดเดียวกัน
        $dbh->beginTransaction();
        try {
            // บันทึกลงใน tblissuedbookdetails โดยตรง
            $sqlIssue = "INSERT INTO tblissuedbookdetails(BookId, StudentId, IssuesDate, ReturnDate, RetrunStatus)
                        VALUES(:bookid, :studentid, :issuedate, :returndate, :returnstatus)";
            $queryIssue = $dbh->prepare($sqlIssue);
            $queryIssue->bindParam(':bookid', $bookId, PDO::PARAM_INT);
            $queryIssue->bindParam(':studentid', $studentId, PDO::PARAM_STR);
            $queryIssue->bindParam(':issuedate', $issueDate, PDO::PARAM_STR);
            $queryIssue->bindParam(':returndate', $returnDate, PDO::PARAM_NULL); // Bind เป็น NULL สำหรับวันที่คืนเริ่มต้น
            $queryIssue->bindParam(':returnstatus', $returnStatus, PDO::PARAM_INT);
            $queryIssue->execute();

            $lastInsertIdIssue = $dbh->lastInsertId();

            if($lastInsertIdIssue > 0) {
                // ลดจำนวนหนังสือใน tblbooks
                $updateSql = "UPDATE tblbooks SET Quantity = Quantity - 1 WHERE id = :bookid AND Quantity > 0";
                $updateQuery = $dbh->prepare($updateSql);
                $updateQuery->bindParam(':bookid', $bookId, PDO::PARAM_INT);
                $updateQuery->execute();

                // ตรวจสอบว่าการลดจำนวนหนังสือสำเร็จหรือไม่
                if ($updateQuery->rowCount() > 0) {
                    $dbh->commit(); // ทำการบันทึก Transaction ทั้งหมด
                    $_SESSION['msg'] = "Booked successfully.";
                    header('location:issued-books.php');
                } else {
                    $dbh->rollBack(); // ยกเลิก Transaction หากลดจำนวนหนังสือไม่สำเร็จ (อาจจะไม่มีหนังสือเหลือ)
                    $_SESSION['error'] = "ไม่สามารถจองได้ เนื่องจากจำนวนหนังสือไม่เพียงพอ.";
                    header('location:booking.php?bookid=' . $bookId);
                }
            } else {
                $dbh->rollBack(); // ยกเลิก Transaction หาก Insert ข้อมูลการจองไม่สำเร็จ
                $_SESSION['error'] = "Error, please try again.";
                header('location:issued-books.php');
            }
        } catch (Exception $e) {
            $dbh->rollBack(); // ยกเลิก Transaction หากเกิด Exception
            $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            header('location:issued-books.php');
        }
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Book a Book</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <style>
        .datepicker{
            z-index: 1151 !important;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php');?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Booking</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Book Information
                        </div>
                        <div class="panel-body">
                            <?php if(isset($_SESSION['error'])) { ?>
                            <div class="alert alert-danger">
                                <?php echo $_SESSION['error']; ?>
                            </div>
                            <?php unset($_SESSION['error']); } ?>
                            <?php if(isset($_SESSION['msg'])) { ?>
                            <div class="alert alert-success">
                                <?php echo $_SESSION['msg']; ?>
                            </div>
                            <?php unset($_SESSION['msg']); } ?>
                            <form role="form" method="post">
                                <input type="hidden" name="bookid" value="<?php echo htmlentities($book->bookid); ?>">
                                <div class="form-group">
                                    <label>Book Name:</label>
                                    <input class="form-control" type="text" value="<?php echo htmlentities($book->BookName); ?>" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Category:</label>
                                    <input class="form-control" type="text" value="<?php echo htmlentities($book->CategoryName); ?>" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Author:</label>
                                    <input class="form-control" type="text" value="<?php echo htmlentities($book->AuthorName); ?>" readonly />
                                </div>
                                <div class="form-group">
                                    <label>ISBN:</label>
                                    <input class="form-control" type="text" value="<?php echo htmlentities($book->ISBNNumber); ?>" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Quantity:</label>
                                    <input class="form-control" type="text" value="<?php echo htmlentities($book->BookQuantity); ?>" readonly />
                                </div>
                                <div class="form-group">
                                    <label>Pickup Date and Time (YYYY-MM-DD HH:MM:SS)<span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" name="pickup_datetime" autocomplete="off" placeholder="YYYY-MM-DD HH:MM:SS" required />
                                </div>
                                <button type="submit" name="book" class="btn btn-info">Confirm Booking</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                startDate: new Date() // ไม่ให้เลือกวันที่ย้อนหลัง
            });
            $('.timepicker').timepicker({
                timeFormat: 'HH:mm',
                interval: 30, // กำหนดช่วงเวลา (เช่น ทุก 30 นาที)
                minTime: '09:00',
                maxTime: '17:00',
                defaultTime: '09:00',
                startTime: '09:00',
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });
        });
    </script>
</body>
</html>
<?php } ?>