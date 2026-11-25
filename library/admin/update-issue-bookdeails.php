<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{
$rid=intval($_GET['rid']);
if(isset($_POST['update']))
{
    $studentid=$_POST['studentid'];
    $bookid=$_POST['bookid'];
    $issuedate=$_POST['issuedate'];
    $returndate=$_POST['returndate'];
    $fine=$_POST['fine'];

$sql="update tblissuedbookdetails set StudentId=:studentid,BookId=:bookid,IssuesDate=:issuedate,ReturnDate=:returndate,fine=:fine,RetrunStatus=:returnstatus where id=:rid";
$query = $dbh->prepare($sql);
$query->bindParam(':studentid',$studentid,PDO::PARAM_STR);
$query->bindParam(':bookid',$bookid,PDO::PARAM_STR);
$query->bindParam(':issuedate',$issuedate,PDO::PARAM_STR);
$query->bindParam(':returndate',$returndate,PDO::PARAM_STR);
$query->bindParam(':fine',$fine,PDO::PARAM_STR);
$query->bindParam(':rid',$rid,PDO::PARAM_STR);
$query->bindParam(':returnstatus',$returnstatus,PDO::PARAM_INT);
$query->execute();

if ($returnstatus == 1) {
    $updateBook = "UPDATE tblbooks SET Quantity = Quantity + 1 WHERE id = :bookid";
}
else {
    if ($returndate != '0000-00-00 00:00:00' && $returndate != '0' && !empty($returndate) && $returndate != null) {
        $returnstatus = 1;
    } else {
        $returnstatus = 0;
    }
    $updateBook = "UPDATE tblbooks SET Quantity = Quantity - 1 WHERE id = :bookid";
}
$bookQuery = $dbh->prepare($updateBook);
$bookQuery->bindParam(':bookid', $bookid, PDO::PARAM_INT);
$bookQuery->execute();

$_SESSION['msg']="Issued book details updated successfully";
header('location:manage-history.php');
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Update Issued Book Details</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
<script src="assets/js/jquery-1.10.2.js"></script>
<script>
// function for get student name
function getstudent() {
$("#loaderIcon").show();
jQuery.ajax({
url: "get_student.php",
data:'studentid='+$("#studentid").val(),
type: "POST",
success:function(data){
$("#get_student_name").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}

//function for book details
function getbook() {
$("#loaderIcon").show();
jQuery.ajax({
url: "get_book.php",
data:'bookid='+$("#bookid").val(),
type: "POST",
success:function(data){
$("#get_book_name").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}

</script>
<style type="text/css">
  .others{
    color:red;
}

</style>
</head>
<body>
    <?php include('includes/header.php');?>
    <div class="content-wrapper">
        <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Update Issued Book Details</h4>
            </div>
</div>
<div class="row">
<div class="col-md-10 col-sm-6 col-xs-12 col-md-offset-1"">
<div class="panel panel-info">
<div class="panel-heading">
Update Issued Book Details
</div>
<div class="panel-body">
<form role="form" method="post">
<?php
$rid=intval($_GET['rid']);
$sql = "SELECT tblstudents.StudentId,tblstudents.FullName,tblbooks.id as bookid,tblbooks.BookName,tblissuedbookdetails.IssuesDate,tblissuedbookdetails.ReturnDate,tblissuedbookdetails.fine,tblissuedbookdetails.id as rid,tblissuedbookdetails.RetrunStatus from  tblissuedbookdetails join tblstudents on tblstudents.StudentId=tblissuedbookdetails.StudentId join tblbooks on tblbooks.id=tblissuedbookdetails.BookId where tblissuedbookdetails.id=:rid";
$query = $dbh -> prepare($sql);
$query->bindParam(':rid',$rid,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{
               ?>

<div class="form-group">
<label>Student Id :<span style="color:red;">*</span></label>
<input class="form-control" type="text" name="studentid" id="studentid" value="<?php echo htmlentities($result->StudentId);?>" required autocomplete="off" onBlur="getstudent()" readonly />
</div>

<div class="form-group">
<span id="get_student_name" style="font-size:16px;"></span>
</div>

<div class="form-group">
<label>Book Id :<span style="color:red;">*</span></label>
<input class="form-control" type="text" name="bookid" id="bookid" value="<?php echo htmlentities($result->bookid);?>" required autocomplete="off" onBlur="getbook()" readonly/>
</div>

<div class="form-group">
<span id="get_book_name" style="font-size:16px;"></span>
</div>

<div class="form-group">
<label>Issue Date :<span style="color:red;">*</span></label>
<input class="form-control" type="text" name="issuedate" value="<?php echo htmlentities($result->IssuesDate);?>" required readonly />
</div>

<div class="form-group">
    <label>Return Date and Time (YYYY-MM-DD HH:MM:SS)<span style="color:red;">*</span></label>
    <input class="form-control" type="text" name="returndate" autocomplete="off" placeholder="YYYY-MM-DD HH:MM:SS" value="<?php echo htmlentities($result->ReturnDate);?>" required />
</div>

<div class="form-group">
<label>Fine (in USD) :</label>
<input class="form-control" type="text" name="fine" id="fine" value="<?php echo htmlentities($result->fine);?>" autocomplete="off" />
</div>
<?php }} ?>

<button type="submit" name="update" id="submit" class="btn btn-info">Update </button>

                                    </form>
                            </div>
                        </div>
                            </div>

        </div>

    </div>
    </div>
     <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>