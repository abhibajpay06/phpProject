<?php
   session_start();
   include("include/function.php");
   $conn = connection();
   date_default_timezone_set("Asia/Kolkata");
   
   
   
   // SET LOCATION RANGE FOR MARKING ATTENDANCE
   function getDistancePHP($lat1, $lon1, $lat2, $lon2) {
       $earthRadius = 6371000; // meters
   
       $dLat = deg2rad($lat2 - $lat1);
       $dLon = deg2rad($lon2 - $lon1);
   
       $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
   
       $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
       return $earthRadius * $c;
   }
   
   
   
   $name = $_SESSION['name'];
   $id= $_SESSION['id'];
   
   //==================================================
   //PUNCH IN & PUNCH OUT - START
   //==================================================
   
      
      // Check if user is logged in
      if (!isset($_SESSION['email'])) {
         header("Location: index");
         exit;
      }
      
      /* ---------------- USER DETAILS ---------------- */
      $name = $_SESSION['name'];            // taken from session
      $registration_no = $_SESSION['registration_no'];            // taken from session
      $course = $_SESSION['course'];            // taken from session
      $today = date("Y-m-d");
      
      
      
      
      /*========BIRTHDAY POPUP===============*/
   $today_md = date('m-d');
   $today_ymd = date('Y-m-d');
   
   // fetch birthdays
   $birthdayQuery = mysqli_query($conn, "SELECT * FROM user_login WHERE DATE_FORMAT(dob, '%m-%d') = '$today_md'");
   
   $birthdayNames = [];
   $isMyBirthday = false;
   
   while ($row = mysqli_fetch_assoc($birthdayQuery)) {
       $birthdayNames[] = $row['name'];
       if ($row['registration_no'] == $registration_no) {
           $isMyBirthday = true;
       }
   }
   
   $hasBirthdayToday = count($birthdayNames) > 0;
   
   // session key (once per day)
   $birthdaySessionKey = 'birthday_popup_' . $today_ymd;
   
   $showBirthdayPopup = $hasBirthdayToday && empty($_SESSION[$birthdaySessionKey]);
   
   
   
      
      /* ---------------- CHECK TODAY'S RECORD ---------------- */
      $attendance = mysqli_query($conn,
         "SELECT * FROM attendance 
          WHERE registration_no='$registration_no' AND date='$today'"
      );
      
      $record = mysqli_fetch_array($attendance);
      
      
      /* ---------------- MARK ABSENT IF MISSED PUNCH PREVIOUS DAY ---------------- */
    
   
   // Get last attendance date
   $lastAttendanceQuery = mysqli_query($conn,
       "SELECT date FROM attendance 
        WHERE registration_no='$registration_no'
        ORDER BY date DESC LIMIT 1"
   );
   
   $lastAttendance = mysqli_fetch_array($lastAttendanceQuery);
   
   if ($lastAttendance) {
       $lastDate = $lastAttendance['date'];
       $start = strtotime("+1 day", strtotime($lastDate));
   } else {
       // First time user → start from today only
       $start = strtotime($today);
   }
   
   $end = strtotime("-1 day", strtotime($today));
   
   while ($start <= $end) {
       // Skip Sundays
       if (date('N', $start) != 7) {
           $absentDate = date("Y-m-d", $start);
   
           // Check if record already exists
           $check = mysqli_query($conn,
               "SELECT id FROM attendance 
                WHERE registration_no='$registration_no' 
                AND date='$absentDate'"
           );
   
           if (mysqli_num_rows($check) == 0) {
               mysqli_query($conn,
                   "INSERT INTO attendance (name, registration_no, date, status)
                    VALUES ('$name', '$registration_no', '$absentDate', 'Absent')"
               );
           }
       }
       $start = strtotime("+1 day", $start);
   }
   
      
      /* ---------------- HANDLE PUNCH ---------------- */
     if (isset($_POST['punch'])) {
   
       // 1️- Get user coordinates from form
       $user_lat = $_POST['user_lat'] ?? '';
       $user_lng = $_POST['user_lng'] ?? '';
   
       // 2️- Office fixed location
   	$office_lat = 30.29077047662863;
   	$office_lng = 78.0429656604998;
	
	
   
   
       // 3️- Calculate REAL distance (SERVER SIDE)
       $realDistance = getDistancePHP(
           $user_lat, $user_lng,
           $office_lat, $office_lng
       );
   
       // 4️- BLOCK if outside office
       if ($realDistance > 50) {
           $_SESSION['error'] = "Attendance allowed only inside office location";
           header("Location: home");
           exit;
       }
   
       // ===============================
       // PUNCH LOGIC
       // ===============================
   
       if (!$record) {
           // Punch IN
           $punch_in = date("H:i:s");
           mysqli_query($conn,
               "INSERT INTO attendance 
               (name, registration_no, date, punch_in, status, latitude, longitude, distance)
               VALUES ('$name', '$registration_no', '$today', '$punch_in', 'Present', '$user_lat', '$user_lng', '$realDistance')"
           );
       }
       else if ($record['punch_in'] != "" && $record['punch_out'] == "") {
           // Punch OUT
           $punch_out = date("H:i:s");
           mysqli_query($conn,
               "UPDATE attendance 
                SET punch_out='$punch_out'
                WHERE id=".$record['id']
           );
       }
   
       header("Location: home");
       exit;
   }
   
   
      
      /* Reload updated data */
      $attendance = mysqli_query($conn,
         "SELECT * FROM attendance 
          WHERE registration_no='$registration_no' AND date='$today'"
      );
      $record = mysqli_fetch_array($attendance);
   
   //==================================================
   //PUNCH IN & PUNCH OUT - END
   //==================================================
   
   
   //==================================================
   //MONTHLY ATTENDANCE START
   //==================================================
   
   
   // Current month & year
   $month = date('m');
   $year  = date('Y');
   
   // 1. Count PRESENT DAYS (attendance table entries)
   $presentQuery = mysqli_query($conn,
       "SELECT COUNT(*) AS present_days
        FROM attendance
        WHERE registration_no='$registration_no'
        AND status='Present'
        AND MONTH(date)='$month'
        AND YEAR(date)='$year'"
   );
   
   $presentData  = mysqli_fetch_array($presentQuery);
   $present_days = $presentData['present_days'];
   
   
   // 2. Calculate TOTAL WORKING DAYS (exclude all Sundays)
   $start_date = "$year-$month-01";
   $end_date   = date("Y-m-t", strtotime($start_date));
   
   $working_days = 0;
   $current = strtotime($start_date);
   $last    = strtotime($end_date);
   
   while ($current <= $last) {
       if (date('N', $current) != 7) { // 7 = Sunday
           $working_days++;
       }
       $current = strtotime("+1 day", $current);
   }
   
   
   // 3. ABSENT DAYS = WORKING DAYS - PRESENT DAYS
   $absentQuery = mysqli_query($conn,
       "SELECT COUNT(*) AS absent_days
        FROM attendance
        WHERE registration_no='$registration_no'
        AND status='Absent'
        AND MONTH(date)='$month'
        AND YEAR(date)='$year'"
   );
   
   $absentData  = mysqli_fetch_array($absentQuery);
   $absent_days = $absentData['absent_days'];
   
   
   
   //===========================
   //MONTHLY ATTENDANCE - END
   //===========================
   
   
   
   
   ?>
<!DOCTYPE html>
<html
   lang="en"
   class="light-style layout-menu-fixed"
   dir="ltr"
   data-theme="theme-default"
   data-assets-path="assets/"
   data-template="vertical-menu-template-free"
   >
   <head>
      <meta charset="utf-8" />
      <meta
         name="viewport"
         content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
         />
      <title>Dashboard</title>
      <meta name="description" content="" />
      <!-- Favicon -->
      <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.webp" />
      <!-- Fonts -->
      <link rel="preconnect" href="https://fonts.googleapis.com" />
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
      <link
         href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
         rel="stylesheet"
         />
      <!-- Icons. Uncomment required icon fonts -->
      <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />
      <!-- Core CSS -->
      <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
      <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
      <!-- <link rel="stylesheet" href="assets/css/demo.css" /> -->
      <!-- Vendors CSS -->
      <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
      <!--  <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />	-->
      <!-- Page CSS -->
      <!-- Helpers -->
      <script src="assets/vendor/js/helpers.js"></script>
      <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
      <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
      <script src="assets/js/config.js"></script>
      <style>
         h1 {
         text-align: center;
         }
         button {
         margin: 5px;
         }
         #punch-out, #bye {
         display: none;
         }
         .btn-default {
         color: #bcbcbc;
         }
         .fa-undo, .fa-redo {
         margin-right: 10px;
         }
         .card-height{
         height: 150px;
         align-items: center;
         }
      </style>
   </head>
   <body>
      <!-- Layout wrapper -->
      <div class="layout-wrapper layout-content-navbar">
         <div class="layout-container">
            <?php include("include/sidebar.php"); ?>
            <!-- Layout container -->
            <div class="layout-page">
               <?php include("include/header.php");?>
               <!-- Content wrapper -->
               <div class="content-wrapper">
                  <!-- Content -->
                  <div class="container-xxl flex-grow-1 container-p-y">
                     <div class="row">
                        <div class="col-md-12 col-lg-12 mb-4 order-0">
                           <div class="card">
                              <div class="d-flex align-items-end row">
                                 <div class="col-md-6">
                                    <div class="card-body">
                                       <h5 class="card-title text-primary">Attendance : <?= date("d-m-y") ?></h5>
                                       <p class="mb-4">
                                          Kindly mark your<span class="fw-bold">Attendance </span> here.
                                       </p>
                                       <div class="container">
                                          <div class="row col-12">
                                             <div id="buttons" class="col-4">
                                                <form method="POST" onsubmit="return validateLocation();">
                                                   <input type="hidden" name="user_lat" id="user_lat">
                                                   <input type="hidden" name="user_lng" id="user_lng">
                                                   <input type="hidden" name="distance" id="distance">
                                                   <button type="submit" name="punch" id="punch-in" class="btn btn-success">
                                                   Punch In
                                                   </button>
                                                   <button type="submit" name="punch" id="punch-out" class="btn btn-danger" style="display:none">
                                                   Punch Out
                                                   </button>
                                                   <button type="button" id="bye" class="btn btn-secondary" style="display:none">
                                                   Shift Over
                                                   </button>
                                                   <div id="locationMsg" style="margin-top:10px;"></div>
                                                   
                                                </form>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
							<!-- <div class="col-md-4">
                                    <div class="card-body">
                                       <div class="container">
                                          <div class="row col-12">
                                             <h6>Punch In Time: <?= $record['punch_in'];?></h6>
											 <h6>Punch Out Time:<?= $record['punch_out'];?></h6>
                                          </div>
                                       </div>
                                    </div>
                                 </div>	-->

                                 <div class="col-md-6 text-center text-sm-left">
                                    <div class="card-body pb-0 px-0 px-md-4">
                                       <img
                                          src="assets/img/illustrations/man-with-laptop-light.png"
                                          height="140"
                                          alt="View Badge User"
                                          data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                          data-app-light-img="illustrations/man-with-laptop-light.png"
                                          />
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-3 col-md-3 mb-4">
                           <div class="card card-height">
                              <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                                 <div class="avatar mb-2">
                                    <i class="bx bx-calendar text-primary fs-2"></i>
                                 </div>
                                 <span class="fw-semibold d-block mb-1">Total working days</span>
                                 <h3 class="card-title mb-2">
                                    <td><?= $working_days ?></td>
                                 </h3>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-3 col-md-3 mb-4">
                           <div class="card card-height">
                              <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                                 <div class="avatar mb-2">
                                    <i class="bx bx-check-circle text-primary fs-2"></i>
                                 </div>
                                 <span>Total Present</span>
                                 <h3 class="card-title text-nowrap mb-1"><?= $present_days ?></h3>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-3 col-md-3 mb-4">
                           <div class="card card-height h-100">
                              <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                                 <div class="avatar mb-2">
                                    <i class="bx bx-x-circle text-primary fs-2"></i>
                                 </div>
                                 <span class="d-block mb-1">Total Absents</span>
                                 <h3 class="card-title text-nowrap mb-0"><?= $absent_days ?></h3>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-3 col-md-3 mb-4">
                           <div class="card card-height">
                              <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                                 <div class="avatar mb-2">
                                    <i class="bx bx-calendar-minus text-primary fs-2"></i>
                                 </div>
                                 <?php
                                    $leave_application = mysqli_query($conn, "SELECT * FROM `leave_application` WHERE registration_no='$registration_no' ORDER BY id DESC");
                                    $leaveApplication = mysqli_fetch_array($leave_application);
                                    ?>
                                 <span class="fw-semibold d-block mb-1">Latest Leave</span>
                                 <h3 class="card-title mb-2">
                                    <?php
                                       if(mysqli_num_rows($leave_application)>0){
                                       echo $leaveApplication['leave_from'];
                                       }else{
                                       	echo "<h6>Leave not taken</h6>";
                                       }
                                       ?>
                                 </h3>
                              </div>
                           </div>
                        </div>
                        <!--NEW SECTION-->
                        <div class="col-md-12 col-lg-12 mb-4 order-0">
                           <div class="card">
                              <div class="d-flex align-items-end row">
                                 <div class="col-sm-7">
                                    <div class="card-body">
                                       <h5 class="card-title text-primary">Profile</h5>
                                       <div class="container">
                                          <div class="row col-12">
                                             <div id="buttons" class="col-6">
                                                <table class="table">
                                                   <tr>
                                                      <th>Name</th>
                                                      <td><?= $name ?></td>
                                                   </tr>
                                                   <tr>
                                                      <th>Registration No.</th>
                                                      <td><?= $registration_no ?></td>
                                                   </tr>
                                                   <tr>
                                                      <th>Course</th>
                                                      <td><?= $course ?></td>
                                                   </tr>
                                                </table>
                                                <button type="submit" name="view" class="btn btn-info align-right">
                                                <a href="profile">View More</a>
                                                </button>
                                                <input type="hidden" value="<?= $name ?>">
                                                <input type="hidden" value="<?= $registration_no ?>">
                                                <input type="hidden" value="<?= $record['punch_in'] ?? '' ?>">
                                                <input type="hidden" value="<?= $record['punch_out'] ?? '' ?>">
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-sm-5 text-center text-sm-left">
                                    <div class="card-body pb-0 px-0 px-md-4">
                                       <img
                                          src="assets/img/illustrations/girl-doing-yoga-light.png"
                                          height="140"
                                          alt="View Badge User"
                                          data-app-dark-img="illustrations/girl-doing-yoga-light.png"
                                          data-app-light-img="illustrations/girl-doing-yoga-light.png"
                                          />
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <!-- Total Revenue -->
                        <!--    <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
                           <div class="card">
                             <div class="row row-bordered g-0">
                               <div class="col-md-8">
                                 <h5 class="card-header m-0 me-2 pb-3">Total Revenue</h5>
                                 <div id="totalRevenueChart" class="px-2"></div>
                               </div>
                               <div class="col-md-4">
                                 <div class="card-body">
                                   <div class="text-center">
                                     <div class="dropdown">
                                       <button
                                         class="btn btn-sm btn-outline-primary dropdown-toggle"
                                         type="button"
                                         id="growthReportId"
                                         data-bs-toggle="dropdown"
                                         aria-haspopup="true"
                                         aria-expanded="false"
                                       >
                                         2022
                                       </button>
                                       <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">
                                         <a class="dropdown-item" href="javascript:void(0);">2021</a>
                                         <a class="dropdown-item" href="javascript:void(0);">2020</a>
                                         <a class="dropdown-item" href="javascript:void(0);">2019</a>
                                       </div>
                                     </div>
                                   </div>
                                 </div>
                                 <div id="growthChart"></div>
                                 <div class="text-center fw-semibold pt-3 mb-2">62% Company Growth</div>
                           
                                 <div class="d-flex px-xxl-4 px-lg-2 p-4 gap-xxl-3 gap-lg-1 gap-3 justify-content-between">
                                   <div class="d-flex">
                                     <div class="me-2">
                                       <span class="badge bg-label-primary p-2"><i class="bx bx-dollar text-primary"></i></span>
                                     </div>
                                     <div class="d-flex flex-column">
                                       <small>2022</small>
                                       <h6 class="mb-0">$32.5k</h6>
                                     </div>
                                   </div>
                                   <div class="d-flex">
                                     <div class="me-2">
                                       <span class="badge bg-label-info p-2"><i class="bx bx-wallet text-info"></i></span>
                                     </div>
                                     <div class="d-flex flex-column">
                                       <small>2021</small>
                                       <h6 class="mb-0">$41.2k</h6>
                                     </div>
                                   </div>
                                 </div>
                               </div>
                             </div>
                           </div>
                           </div>		-->
                        <!-- Transactions -->
                        <!--            <div class="col-md-6 col-lg-4 order-2 mb-4">
                           <div class="card h-100">
                             <div class="card-header d-flex align-items-center justify-content-between">
                               <h5 class="card-title m-0 me-2">Transactions</h5>
                               <div class="dropdown">
                                 <button
                                   class="btn p-0"
                                   type="button"
                                   id="transactionID"
                                   data-bs-toggle="dropdown"
                                   aria-haspopup="true"
                                   aria-expanded="false"
                                 >
                                   <i class="bx bx-dots-vertical-rounded"></i>
                                 </button>
                                 <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                   <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                   <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                   <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                                 </div>
                               </div>
                             </div>
                             <div class="card-body">
                               <ul class="p-0 m-0">
                                 <li class="d-flex mb-4 pb-1">
                                   <div class="avatar flex-shrink-0 me-3">
                                     <img src="assets/img/icons/unicons/paypal.png" alt="User" class="rounded" />
                                   </div>
                                   <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                     <div class="me-2">
                                       <small class="text-muted d-block mb-1">Paypal</small>
                                       <h6 class="mb-0">Send money</h6>
                                     </div>
                                     <div class="user-progress d-flex align-items-center gap-1">
                                       <h6 class="mb-0">+82.6</h6>
                                       <span class="text-muted">USD</span>
                                     </div>
                                   </div>
                                 </li>
                                 <li class="d-flex mb-4 pb-1">
                                   <div class="avatar flex-shrink-0 me-3">
                                     <img src="assets/img/icons/unicons/wallet.png" alt="User" class="rounded" />
                                   </div>
                                   <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                     <div class="me-2">
                                       <small class="text-muted d-block mb-1">Wallet</small>
                                       <h6 class="mb-0">Mac'D</h6>
                                     </div>                            <div class="user-progress d-flex align-items-center gap-1">
                                       <h6 class="mb-0">+270.69</h6>
                                       <span class="text-muted">USD</span>
                                     </div>
                                   </div>
                                 </li>
                                 <li class="d-flex mb-4 pb-1">
                                   <div class="avatar flex-shrink-0 me-3">
                                     <img src="assets/img/icons/unicons/chart.png" alt="User" class="rounded" />
                                   </div>
                                   <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                     <div class="me-2">
                                       <small class="text-muted d-block mb-1">Transfer</small>
                                       <h6 class="mb-0">Refund</h6>
                                     </div>
                                     <div class="user-progress d-flex align-items-center gap-1">
                                       <h6 class="mb-0">+637.91</h6>
                                       <span class="text-muted">USD</span>
                                     </div>
                                   </div>
                                 </li>
                                 <li class="d-flex mb-4 pb-1">
                                   <div class="avatar flex-shrink-0 me-3">
                                     <img src="assets/img/icons/unicons/cc-success.png" alt="User" class="rounded" />
                                   </div>
                                   <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                     <div class="me-2">
                                       <small class="text-muted d-block mb-1">Credit Card</small>
                                       <h6 class="mb-0">Ordered Food</h6>
                                     </div>
                                     <div class="user-progress d-flex align-items-center gap-1">
                                       <h6 class="mb-0">-838.71</h6>
                                       <span class="text-muted">USD</span>
                                     </div>
                                   </div>
                                 </li>
                                 <li class="d-flex mb-4 pb-1">
                                   <div class="avatar flex-shrink-0 me-3">
                                     <img src="assets/img/icons/unicons/wallet.png" alt="User" class="rounded" />
                                   </div>
                                   <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                     <div class="me-2">
                                       <small class="text-muted d-block mb-1">Wallet</small>
                                       <h6 class="mb-0">Starbucks</h6>
                                     </div>
                                     <div class="user-progress d-flex align-items-center gap-1">
                                       <h6 class="mb-0">+203.33</h6>
                                       <span class="text-muted">USD</span>
                                     </div>
                                   </div>
                                 </li>
                                 <li class="d-flex">
                                   <div class="avatar flex-shrink-0 me-3">
                                     <img src="assets/img/icons/unicons/cc-warning.png" alt="User" class="rounded" />
                                   </div>
                                   <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                     <div class="me-2">
                                       <small class="text-muted d-block mb-1">Mastercard</small>
                                       <h6 class="mb-0">Ordered Food</h6>
                                     </div>
                                     <div class="user-progress d-flex align-items-center gap-1">
                                       <h6 class="mb-0">-92.45</h6>
                                       <span class="text-muted">USD</span>
                                     </div>
                                   </div>
                                 </li>
                               </ul>
                             </div>
                           </div>
                           </div>		-->
                        <!--/ Transactions -->
                     </div>
                  </div>
                  <!-- / Content -->
                  <?php include("include/footer.php"); ?>
                  <div class="content-backdrop fade"></div>
               </div>
               <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
         </div>
         <!-- Overlay -->
         <div class="layout-overlay layout-menu-toggle"></div>
      </div>
      <!-- / Layout wrapper -->
      <!-- Core JS -->
      <!-- build:js assets/vendor/js/core.js -->
      <script src="assets/vendor/libs/jquery/jquery.js"></script>
      <script src="assets/vendor/libs/popper/popper.js"></script>
      <script src="assets/vendor/js/bootstrap.js"></script>
      <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
      <script src="assets/vendor/js/menu.js"></script>
      <!-- endbuild -->
      <!-- Vendors JS -->
      <script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>
      <!-- Main JS -->
      <script src="assets/js/main.js"></script>
      <!-- Page JS -->
      <script src="assets/js/dashboards-analytics.js"></script>
      <!-- Place this tag in your head or just before your close body tag. -->
      <script async defer src="https://buttons.github.io/buttons.js"></script>
      <script>
         let punchIn  = "<?= $record['punch_in'] ?>";
         let punchOut = "<?= $record['punch_out'] ?>";
         
         if (!punchIn) {
         	// Show Punch In
         	document.getElementById("punch-in").style.display = "block";
         }
         else if (punchIn && !punchOut) {
         	// Show Punch Out
         	document.getElementById("punch-in").style.display = "none";
         	document.getElementById("punch-out").style.display = "block";
         }
         else {
         	// Completed
         	document.getElementById("punch-in").style.display = "none";
         	document.getElementById("punch-out").style.display = "none";
         	document.getElementById("bye").style.display = "block";
         }
      </script>
      <script>
         const OFFICE_LAT = 30.29077047662863;
         const OFFICE_LNG = 78.0429656604998;
         
         const ALLOWED_RADIUS = 50; // meters
         
         
         function getDistance(lat1, lon1, lat2, lon2) {
             const R = 6371000;
             const dLat = (lat2 - lat1) * Math.PI / 180;
             const dLon = (lon2 - lon1) * Math.PI / 180;
         
             const a =
                 Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                 Math.cos(lat1 * Math.PI / 180) *
                 Math.cos(lat2 * Math.PI / 180) *
                 Math.sin(dLon / 2) * Math.sin(dLon / 2);
         
             const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
             return R * c;
         }
         
         function checkLocation() {
             if (!navigator.geolocation) {
                 alert("Geolocation not supported");
                 return;
             }
         
             navigator.geolocation.getCurrentPosition(
                 function (position) {
                     const lat = position.coords.latitude;
                     const lng = position.coords.longitude;
         
                     const distance = getDistance(lat, lng, OFFICE_LAT, OFFICE_LNG);
         
                     document.getElementById("user_lat").value = lat;
                     document.getElementById("user_lng").value = lng;
                     document.getElementById("distance").value = distance.toFixed(2);
         
                     if (distance <= ALLOWED_RADIUS) {
                         document.getElementById("punch").disabled = false;
                         document.getElementById("locationMsg").innerHTML =
                             "<span style='color:green'>Inside office location</span>";
                     } else {
                         document.getElementById("punch").disabled = true;
                         document.getElementById("locationMsg").innerHTML =
                             "<span style='color:red'>Outside office location</span>";
                     }
                 },
                 function () {
                     alert("Location permission required to mark attendance");
                 },
                 { enableHighAccuracy: true }
             );
         }
         
         function validateLocation() {
             const distance = document.getElementById("distance").value;
             if (distance === "" || distance > ALLOWED_RADIUS) {
                 alert("You are not inside office location");
                 return false;
             }
             return true;
         }
         
         window.onload = checkLocation;
      </script>
      <?php if ($showBirthdayPopup): ?>
      <div id="birthdayPopup" style="
         position:fixed;
         inset:0;
         background:rgba(0,0,0,0.6);
         z-index:9999;
         display:flex;
         align-items:center;
         justify-content:center;">
         <div style="
            background:#fff;
            padding:30px;
            width:400px;
            border-radius:12px;
            text-align:center;">
            <?php if ($isMyBirthday): ?>
            <h3>🎉 Happy Birthday <?= htmlspecialchars($name) ?> 🎂</h3>
            <p>Wishing you a wonderful year ahead!</p>
            <?php else: ?>
            <h3>🎂 Birthday Alert!</h3>
            <p>Today is the birthday of:</p>
            <strong><?= implode(', ', $birthdayNames) ?></strong>
            <?php endif; ?>
            <button onclick="closeBirthdayPopup()" class="btn btn-primary mt-3">
            OK 🎉
            </button>
         </div>
      </div>
      <?php endif; ?>
      <canvas id="confettiCanvas" style="
         position:fixed;
         bottom:0;
         left:0;
         width:100%;
         height:100%;
         pointer-events:none;
         z-index:10000;
         display:none;"></canvas>
      <script>
         function closeBirthdayPopup() {
             document.getElementById('birthdayPopup').style.display = 'none';
             startConfetti();
         
             // mark popup shown
             fetch('mark_birthday_popup.php');
         }
         
         function startConfetti() {
             const canvas = document.getElementById("confettiCanvas");
             const ctx = canvas.getContext("2d");
         
             canvas.width = window.innerWidth;
             canvas.height = window.innerHeight;
             canvas.style.display = "block";
         
             let confetti = [];
             const colors = ["#ff4757","#1e90ff","#2ed573","#ffa502","#e84393"];
         
             for (let i = 0; i < 150; i++) {
                 confetti.push({
                     x: Math.random() * canvas.width,
                     y: canvas.height + Math.random() * 200,
                     r: Math.random() * 6 + 4,
                     d: Math.random() * 150,
                     color: colors[Math.floor(Math.random()*colors.length)],
                     tilt: Math.random() * 10 - 5,
                     speed: Math.random() * 3 + 2
                 });
             }
         
             function draw() {
                 ctx.clearRect(0,0,canvas.width,canvas.height);
                 confetti.forEach(c => {
                     ctx.beginPath();
                     ctx.arc(c.x, c.y, c.r, 0, Math.PI*2);
                     ctx.fillStyle = c.color;
                     ctx.fill();
                 });
                 update();
             }
         
             function update() {
                 confetti.forEach(c => {
                     c.y -= c.speed;
                     c.x += Math.sin(c.d);
                 });
             }
         
             let interval = setInterval(draw, 20);
             setTimeout(() => {
                 clearInterval(interval);
                 canvas.style.display = "none";
             }, 5000);
         }
      </script>
   </body>
</html>