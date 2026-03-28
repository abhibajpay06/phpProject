<?php
include("include/function.php");
$conn = connection();

if(isset($_POST['student_course'])){

    $student_course = mysqli_real_escape_string($conn, $_POST['student_course']);

    $sql = mysqli_query($conn, "SELECT * FROM courses WHERE course_name='$student_course'");
    $data = mysqli_fetch_assoc($sql);

    // Return JSON
    echo json_encode([
        "duration" => $data['course_duration'],
        "fee"      => $data['course_fee']
    ]);
}
?>
