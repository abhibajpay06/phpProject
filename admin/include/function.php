<?php
//ADMIN PANEL


//======================
//Setup connection file
//======================

function connection(){
	$conn = mysqli_connect('localhost','root','','infonix');
	return $conn;
}


//=====================
//Sanitize Input fields
//======================

function sanitizeInput($conn, $data) {
    if($data === null) return '';
    return trim(mysqli_real_escape_string($conn, $data));
}



// =========================
// SIMPLE ALERT FUNCTION
// =========================
function showAlert($message, $redirect = null) {
    echo "<script>alert('$message');";
    if ($redirect) {
        echo "window.location.href = '$redirect';";
    }
    echo "</script>";
}



//==============
//Admin Login
//==============
function adminLogin($conn, $email, $password){
$admin_login = mysqli_query($conn, "SELECT * FROM `admin_login` WHERE `email`='$email' && `password` = '$password'");

if($admin_login){
	showAlert('Logged In successfully', 'home');
}else{
	showAlert('Something went wrong', 'index');
}
return $admin_login;
}

//==============
//insert courses
//==============

function insertCourses($conn, $course_name, $course_duration, $course_fee){
		
	$add_course = "INSERT INTO `courses` (`course_name`, `course_duration`, `course_fee`) VALUES ('$course_name', '$course_duration', '$course_fee')";
	if($add_course_query = mysqli_query($conn, $add_course)){
		showAlert('Course added successfully', 'courses_view');
	}else{
				showAlert('Something went wrong, Try again!', 'courses');
	}
	return $add_course_query;
}
	
//==============
//View courses
//==============
function viewCourses(){
	$conn = connection();
	$results = [];
	$view_courses = mysqli_query($conn, "SELECT * FROM `courses` ORDER BY id DESC");
	$results[] = $view_courses;
	
	return $view_courses;
}


//================
//Update courses
//================

function updateCourses($id){
	$conn = connection();
	$view_courses_data = mysqli_query($conn, "SELECT * FROM `courses` WHERE `id` = '$id'");
	$fetch = mysqli_fetch_array($view_courses_data);
	
	return $fetch;
}


//================
//Delete courses
//================

function deleteCourses($id){
	$conn = connection();
	if($delete_courses = mysqli_query($conn, "DELETE FROM `courses` WHERE `id` = '$id'")){
		showAlert('Deleted Successfully','courses_view');
	}else{
		showAlert('Something went wrong, Try again!','courses_view');
	}
	return $delete_courses;
}


//========================
// VIEW ALL USERS TO ADMIN
//========================

function viewAllUsers(){
	$conn = connection();
	$results = [];
	$view_all_users = mysqli_query($conn, "SELECT * FROM `user_login` ORDER BY id DESC");
	$results[] = $view_all_users;
	
	return $view_all_users;
}


//================
//Delete courses
//================

function deleteUser($id){
	$conn = connection();
	if($delete_user = mysqli_query($conn, "DELETE FROM `user_login` WHERE `id` = '$id'")){
		showAlert('Deleted Successfully','view_all_users');
	}else{
		showAlert('Something went wrong, Try again!','view_all_users');
	}
	return $delete_user;
}




//====================================
// MAIL FOR ENABLE/DISABLE USER ACCOUNT
//====================================
function sendAccountStatusMail($email, $name, $action) {
    $to = $email;
    $subject = "Account Status: $action";
    $message = "
        <html>
        <head><title>Account $action</title></head>
        <body style='font-family: Arial, sans-serif;'>
            <p>Hi, $name</p>
            <p>Your user accocunt is <strong> $action </strong></p>
			<a href='https://samaandekho.com/infonix/user/'>Click Here to Login</a>
            <br>
            <p>Best regards,<br>Infonix Service Technology</p>
        </body>
        </html>";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Infonix <info@noreply>" . "\r\n";

    mail($to, $subject, $message, $headers);
}


//========================
// ENABLE USER ACCOUNT BY ADMIN
//========================

function enableUser($id){
		$conn = connection();
		$enable_users = mysqli_query($conn, "UPDATE `user_login` SET action = 'Activated' WHERE `id` = '$id'");
		
		if($enable_users){
			$view_enable_user = mysqli_query($conn, "SELECT * FROM `user_login` WHERE `id` = '$id'");
			
		if($view_enable_user && mysqli_num_rows($view_enable_user)==1){
			$fetch_user = mysqli_fetch_array($view_enable_user);
			$name = $fetch_user['name'];
			$email = $fetch_user['email'];
			$action = $fetch_user['action'];
			
			//send mail if account activates
			sendAccountStatusMail($email, $name, $action);

		}			
		showAlert('User Account Activated','view_all_users');
		
		}else{
			showAlert('Something went wrong, Try again!','view_all_users');
		}	
}

//========================
// DISABLE USER ACCOUNT BY ADMIN
//========================

function disableUser($id){
		$conn = connection();
		$disable_users = mysqli_query($conn, "UPDATE `user_login` SET action = 'Disabled' WHERE `id` = '$id'");
		
		if($disable_users){
			$view_disable_user = mysqli_query($conn, "SELECT * FROM `user_login` WHERE `id` = '$id'");
			
		if($view_disable_user && mysqli_num_rows($view_disable_user)==1){
			$fetch_user = mysqli_fetch_array($view_disable_user);
			$name = $fetch_user['name'];
			$email = $fetch_user['email'];
			$action = $fetch_user['action'];
			
			//send mail if account activates
			sendAccountStatusMail($email, $name, $action);

		}			
		showAlert('User Account Disabled','view_all_users');
		
		}else{
			showAlert('Something went wrong, Try again!','view_all_users');
		}	
}

//==========================
// VIEW LEAVE APPLICATION
//==========================
function viewLeaveApplicationForm(){
	$conn = connection();
	$view_leave_application_form = mysqli_query($conn, "SELECT * FROM `leave_application` ORDER BY id DESC");
	
	$results = [];
	while($fetch = mysqli_fetch_array($view_leave_application_form)){
	$results[] = $fetch;
	}
	return $results;
	
}


//=================================
// EMAIL ON LEAVE APPROVAL/REJECTION
//=================================

function sendLeaveStatusEmail($email, $name, $leave_from, $leave_to, $total_days, $status) {
    $to = $email;
    $subject = "Leave Request: $status";
    $message = "
        <html>
        <head><title>Leave $status</title></head>
        <body style='font-family: Arial, sans-serif;'>
            <p>Hi, $name</p>
            <p>Your Leave Application for<strong>$leave_from</strong> to <strong>$leave_to</strong> of total <strong>$total_days days</strong> has been <strong>$status</strong>.</p>
            <p></p>
            <br>
            <p>Best regards,<br>Infonix Service Technology</p>
        </body>
        </html>";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Infonix <info@noreply>" . "\r\n";

    mail($to, $subject, $message, $headers);
}

//==========================
// APPROVE LEAVE APPLICATION
//==========================
function approveLeaveApplication($id){
    $conn = connection();
    $approve_leave = mysqli_query($conn, "UPDATE `leave_application` SET status='Approved' WHERE id='$id'");

     if ($approve_leave) {
        // Fetch user email, name, leave dates for sending mail
        $approved_leave = mysqli_query($conn, "SELECT * FROM `leave_application` WHERE id = '$id' LIMIT 1");
        
        if ($approved_leave && mysqli_num_rows($approved_leave) == 1) {
            $row = mysqli_fetch_array($approved_leave);
            $email = $row['email'];
            $name = $row['name'];
            $leave_from = $row['leave_from'];
            $leave_to = $row['leave_to'];
            $total_days = $row['total_days'];
            $status = $row['status'];

            // Send confirmation mail
            sendLeaveStatusEmail($email, $name, $leave_from, $leave_to, $total_days, $status);
        }

        showAlert('Status updated and email sent successfully!', 'leave_application_request');
    } else {
        showAlert('Something went wrong. Please try again.', 'leave_application_request');
    }

}	
//==========================
// REJECT LEAVE APPLICATION
//==========================
function rejectLeaveApplication($id){
    $conn = connection();
    $reject_leave = mysqli_query($conn, "UPDATE leave_application SET status='Rejected' WHERE id='$id'");

	if ($reject_leave) {
        // Fetch user email, name, leave dates for sending mail
        $rejected_leave = mysqli_query($conn, "SELECT * FROM `leave_application` WHERE id = '$id' LIMIT 1");
        
     if ($rejected_leave && mysqli_num_rows($rejected_leave) == 1) {
            $row = mysqli_fetch_array($rejected_leave);
            $email = $row['email'];
            $name = $row['name'];
            $leave_from = $row['leave_from'];
            $leave_to = $row['leave_to'];
            $total_days = $row['total_days'];
            $status = $row['status'];

            // Send confirmation mail
            sendLeaveStatusEmail($email, $name, $leave_from, $leave_to, $total_days, $status);
        }

        showAlert('Status updated and email sent successfully!', 'leave_application_request');
    } else {
        showAlert('Something went wrong. Please try again.', 'leave_application_request');
    }
}

//==============
//insert courses
//==============

function insertContacts($conn, $name, $number, $email, $college, $course){
		
	$add_contact = "INSERT INTO `contacts` (`name`, `number`, `email`, `college`, `course`) VALUES ('$name', '$number', '$email', '$college', '$course')";
	if($add_contact_query = mysqli_query($conn, $add_contact)){
		showAlert('Contact added successfully', 'contacts_view');
	}else{
				showAlert('Something went wrong, Try again!', 'contacts_view');
	}
	return $add_contact_query;
}

//==============
//View courses
//==============
function viewContacts(){
	$conn = connection();
	$results = [];
	$view_contacts = mysqli_query($conn, "SELECT * FROM `contacts` ORDER BY id DESC");
	$results[] = $view_contacts;
	return $view_contacts;
}

?>

