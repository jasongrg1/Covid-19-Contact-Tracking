<?php
session_start();
if(!isset($_SESSION["name"])) {
    header("Location: index.php");
    exit();
}else {
    $name = $_SESSION["name"];
}

$q = htmlentities(intval($_GET['q']));

require "auth.php";
$conn = mysqli_connect(hostname, username, password, database, port);
if (!$conn) {
  die;
}
$sql = "DELETE from visits WHERE id = ?";

$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "i", $q);

if (mysqli_stmt_execute($stmt)===false){
  die;
}

mysqli_stmt_free_result($stmt);
mysqli_close($conn);
?>
