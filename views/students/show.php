<?php
include_once './../../vendor/autoload.php';

use Project\Controllers\Student;

$student = new Student();

$studentInfo = $student->details($_GET['id']);

// print_r($studentInfo);

?>

<a href="./index.php">List</a>
<h1>Student Info</h1>
<p>Student ID: <?= $studentInfo['student_id'] ?></p>
<p>Name: <?= $studentInfo['name'] ?></p>
<p>
    Picture: <img height="100px" src="./../../assets/uploads/<?= $studentInfo['picture'] ?>" alt="Profile Picture">
</p>