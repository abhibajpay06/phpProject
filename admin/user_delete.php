<?php
include("include/function.php");

$conn = connection();
$id = $_GET['id'];
deleteUser($id);

?>