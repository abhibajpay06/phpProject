<?php

//USER Panel

//======================
//setup connection file
//======================

function connection(){
	$conn = mysqli_connect('localhost','root','','infonix');
	return $conn;
}

//=====================
//Sanitize Input fields
//=====================

function sanitizeInput($conn, $data) {
    if($data === null) return '';
    return trim(mysqli_real_escape_string($conn, $data));
}

// ======================
// SIMPLE ALERT FUNCTION
// ======================
function showAlert($message, $redirect = null) {
    echo "<script>alert('$message');";
    if ($redirect) {
        echo "window.location.href = '$redirect';";
    }
    echo "</script>";
}

//=============================
//USER REGISTRATION
//=============================	
function userRegistration($conn, $name, $registration_no, $course, $c_duration, $c_fee, $folder, $dob, $number, $email, $gender, $fathers_name, $mothers_name, $parents_number, $address, $password, $created_at){
	$insert_registration_form = "INSERT INTO `user_login` (`name`, `registration_no`, `course`, `c_duration`, `c_fee`, `profile_pic`, `dob`, `number`, `email`, `gender`, `fathers_name`, `mothers_name`, `parents_number`, `address`, `password`, `created_at`) VALUES ('$name', '$registration_no', '$course', '$c_duration', '$c_fee', '$folder', '$dob', '$number', '$email', '$gender', '$fathers_name', '$mothers_name', '$parents_number', '$address','$password', '$created_at')";
	if($registration_query = mysqli_query($conn, $insert_registration_form)){
		showAlert('You are successfully registered', 'index');
	}else{
		showAlert('Something went wrong, Try again!', 'register');
	}
	return $registration_query;
}

// =========================
// Login/Index
// =========================
function userLogin($conn, $email, $action){
$login_user= mysqli_query($conn, "SELECT * FROM `user_login` WHERE `email`='$email' && `action` = 'Activated'");
	
return $login_user;
}


// =========================
// TRACK USER
// =========================

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function getDeviceType() {
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (preg_match('/mobile|android|iphone/', $agent)) return 'Mobile';
    if (preg_match('/tablet|ipad/', $agent)) return 'Tablet';
    return 'Desktop';
}

function getOS() {
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($agent, 'Windows') !== false) return 'Windows';
    if (strpos($agent, 'Android') !== false) return 'Android';
    if (strpos($agent, 'iPhone') !== false || strpos($agent, 'iPad') !== false) return 'iOS';
    if (strpos($agent, 'Mac') !== false) return 'Mac';
    if (strpos($agent, 'Linux') !== false) return 'Linux';
    return 'Unknown';
}

function getBrowser() {
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($agent, 'Chrome') !== false) return 'Chrome';
    if (strpos($agent, 'Firefox') !== false) return 'Firefox';
    if (strpos($agent, 'Safari') !== false) return 'Safari';
    if (strpos($agent, 'Edge') !== false) return 'Edge';
    return 'Unknown';
}

//======================
//LEAVE APPLICATION FORM
//======================

function leaveApplicationForm($conn, $course, $name, $email, $registration_no, $leave_purpose, $leave_from, $leave_to, $total_days, $status){
	$insert_query = "INSERT INTO `leave_application` (`course`, `name`, `email`, `registration_no`, `leave_purpose`, `leave_from`, `leave_to`, `total_days`, `status`) VALUES('$course', '$name', '$email', '$registration_no', '$leave_purpose', '$leave_from', '$leave_to', '$total_days', '$status')";
	if($run = mysqli_query($conn, $insert_query)){
		showAlert('Your Application is Submitted Successfully', 'leave_application_form_view');
	}else{
		showAlert('Something went wrong, Try again!', 'leave_application_form_view');
	}
	return $run;
}

//===========================
//VIEW LEAVE APPLICATION FORM
//===========================

function viewLeaveApplicationForm(){
	$registration_no = $_SESSION['registration_no'];
	$conn = connection();
	$view_leave_application_form = mysqli_query($conn, "SELECT * FROM `leave_application` WHERE `registration_no`='$registration_no' ORDER BY id DESC");
	
	$results = [];
	while($fetch = mysqli_fetch_array($view_leave_application_form)){
	$results[] = $fetch;
	}
	return $results;
}

//=============================
//UPDATE LEAVE APPLICATION FORM
//=============================

function updateLeaveApplicationForm(){
	$select_leave_application = mysqli_query($conn, "SELECT * FROM `leave_application` WHERE `id`='$id'");
	return $select_leave_application;
}

//===========================
//VIEW ATTENDANCE
//===========================

function viewAttendance($id){
	$conn = connection();
	$view_attendance = mysqli_query($conn, "SELECT * FROM `attendance` where id = '$id'");
	
	$results = [];
	while($fetch = mysqli_fetch_array($view_leave_application_form)){
	$results[] = $fetch;
	}
	return $results;
	
}

//================
//STUDENT PROFILE
//================

function userProfile(){
	$conn = connection();
	$id = $_SESSION['id'];
	$view_user_profile = mysqli_query($conn, "SELECT * FROM `user_login` WHERE `id` = '$id'");
	$fetch_data = mysqli_fetch_array($view_user_profile);
	
	return $fetch_data;
}

// =========================
// LOGOUT
// =========================
function logoutUser() {
    session_unset();
    session_destroy();
    showAlert('Logout Successfully', 'index');
    exit;
}






































?>