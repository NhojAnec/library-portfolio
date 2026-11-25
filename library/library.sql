-- Database for the Online Library Management System in this folder.
-- Rebuilt from the queries in the PHP code, with test data.
--
-- Import: phpMyAdmin > Import > choose this file
--     or: C:\xampp\mysql\bin\mysql.exe -u root < library.sql
--
-- Test logins (passwords are stored as MD5, the same way the site checks them):
--   Admin   (admin/index.php):  username admin               password Test@123
--   Student (index.php):        email    student@test.local  password Test@123

DROP DATABASE IF EXISTS library;
CREATE DATABASE library CHARACTER SET utf8 COLLATE utf8_general_ci;
USE library;

CREATE TABLE admin (
  id int(11) NOT NULL AUTO_INCREMENT,
  FullName varchar(100) DEFAULT NULL,
  AdminEmail varchar(120) DEFAULT NULL,
  UserName varchar(100) NOT NULL,
  Password varchar(100) NOT NULL,
  updationDate timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id)
);

CREATE TABLE tblauthors (
  id int(11) NOT NULL AUTO_INCREMENT,
  AuthorName varchar(159) DEFAULT NULL,
  creationDate timestamp NULL DEFAULT current_timestamp(),
  UpdationDate timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (id)
);

CREATE TABLE tblcategory (
  id int(11) NOT NULL AUTO_INCREMENT,
  CategoryName varchar(150) DEFAULT NULL,
  Status int(1) DEFAULT NULL,
  CreationDate timestamp NULL DEFAULT current_timestamp(),
  UpdationDate timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (id)
);

CREATE TABLE tblbooks (
  id int(11) NOT NULL AUTO_INCREMENT,
  BookName varchar(255) DEFAULT NULL,
  CatId int(11) DEFAULT NULL,
  AuthorId int(11) DEFAULT NULL,
  ISBNNumber varchar(50) DEFAULT NULL,
  BookPrice decimal(10,2) DEFAULT NULL,
  Quantity int(11) NOT NULL DEFAULT 1,
  RegDate timestamp NULL DEFAULT current_timestamp(),
  UpdationDate timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (id)
);

CREATE TABLE tblstudents (
  id int(11) NOT NULL AUTO_INCREMENT,
  StudentId varchar(100) DEFAULT NULL,
  FullName varchar(120) DEFAULT NULL,
  EmailId varchar(120) DEFAULT NULL,
  MobileNumber char(11) DEFAULT NULL,
  Password varchar(120) DEFAULT NULL,
  Status int(1) DEFAULT NULL,
  RegDate timestamp NULL DEFAULT current_timestamp(),
  UpdationDate timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY StudentId (StudentId)
);

CREATE TABLE tblissuedbookdetails (
  id int(11) NOT NULL AUTO_INCREMENT,
  BookId int(11) DEFAULT NULL,
  StudentID varchar(150) DEFAULT NULL,
  IssuesDate timestamp NULL DEFAULT current_timestamp(),
  ReturnDate timestamp NULL DEFAULT NULL,
  RetrunStatus int(1) DEFAULT 0,
  fine int(11) DEFAULT NULL,
  PRIMARY KEY (id)
);

-- Test data
INSERT INTO admin (FullName, AdminEmail, UserName, Password) VALUES
('Library Admin', 'admin@test.local', 'admin', MD5('Test@123'));

INSERT INTO tblcategory (CategoryName, Status) VALUES
('Programming', 1), ('Science', 1), ('Fiction', 1), ('History', 0);

INSERT INTO tblauthors (AuthorName) VALUES
('Guido van Rossum'), ('Stephen Hawking'), ('George Orwell'), ('Yuval Noah Harari');

INSERT INTO tblbooks (BookName, CatId, AuthorId, ISBNNumber, BookPrice, Quantity) VALUES
('Python Tutorial', 1, 1, '9780000000011', 450.00, 5),
('A Brief History of Time', 2, 2, '9780553380163', 390.00, 3),
('1984', 3, 3, '9780451524935', 320.00, 4),
('Animal Farm', 3, 3, '9780451526342', 250.00, 2),
('Sapiens', 4, 4, '9780062316097', 520.00, 1);

INSERT INTO tblstudents (StudentId, FullName, EmailId, MobileNumber, Password, Status) VALUES
('SID010', 'Test Student', 'student@test.local', '0812345678', MD5('Test@123'), 1),
('SID011', 'Somchai Example', 'somchai@test.local', '0898765432', MD5('Test@123'), 1),
('SID012', 'Blocked Example', 'blocked@test.local', '0800000000', MD5('Test@123'), 0);

INSERT INTO tblissuedbookdetails (BookId, StudentID, IssuesDate, ReturnDate, RetrunStatus, fine) VALUES
(1, 'SID010', '2026-11-10 10:15:32', NULL, 0, NULL),
(3, 'SID010', '2026-10-28 14:02:10', '2026-11-05 09:30:00', 1, 0),
(2, 'SID011', '2026-11-18 11:45:00', NULL, 0, NULL);
