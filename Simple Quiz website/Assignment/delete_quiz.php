<?php
session_start();
include 'db_conn.php';

$quiz_id = base64_decode($_GET['quiz_id']);
$sql = "DELETE FROM quizzes WHERE quiz_id = '{$quiz_id}'";
$result = mysqli_query($conn, $sql);

mysqli_close($conn);
header("Location: lecturer_dashboard.php");
exit();
?>