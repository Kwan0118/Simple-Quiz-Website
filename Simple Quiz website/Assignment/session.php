<?php
session_start();
if (!isset($_SESSION['id'])) {
    // Redirect to login page if session is not set
    echo "<script>window.location.href='index.php';</script>";
    exit();
}
?>
