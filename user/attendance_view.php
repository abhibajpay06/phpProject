<?php
   session_start();
   
   include("include/function.php");
   $conn = connection();
   
   if (!isset($_SESSION['id'])) {
       header("Location: index");
       exit;
   }
   
   $registration_no = $_SESSION['registration_no'];
   /* ---------------- MONTHLY ATTENDANCE CALENDAR ---------------- */
   
   $month = isset($_GET['month']) ? $_GET['month'] : date("m");
   $year  = isset($_GET['year']) ? $_GET['year'] : date("Y");
   
   // First and last date of month
   $firstDay   = "$year-$month-01";
   $totalDays  = date("t", strtotime($firstDay));
   $startDay   = date("N", strtotime($firstDay)); // 1=Mon, 7=Sun
   
   // Fetch attendance for selected month
   $attendanceData = [];
   $attRes = mysqli_query($conn,
       "SELECT * FROM attendance 
        WHERE registration_no='$registration_no'
        AND MONTH(date)='$month' AND YEAR(date)='$year'"
   );
   
   while ($row = mysqli_fetch_assoc($attRes)) {
       $attendanceData[$row['date']] = $row['status'];
   }
   
   ?>
<!DOCTYPE html>
<!-- beautify ignore:start -->
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
      <title>View Attendance</title>
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
      <link rel="stylesheet" href="assets/css/demo.css" />
      <!-- Vendors CSS -->
      <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
      <!-- Page CSS -->
      <!-- Helpers -->
      <script src="assets/vendor/js/helpers.js"></script>
      <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
      <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
      <script src="assets/js/config.js"></script>
      <style>
         .calendar {
         display: grid;
         grid-template-columns: repeat(7, 1fr);
         gap: 8px;
         margin-top: 20px;
         }
         .day-header {
         text-align: center;
         font-weight: bold;
         }
         .day-box {
         padding: 15px;
         text-align: center;
         border-radius: 10px;
         background: #f2f2f2;
         min-height: 70px;
         }
         .present {
         background: #c8f7c5 !important;
         color: #2d7a2d;
         font-weight: bold;
         }
         .absent {
         background: #ffcccc !important;
         color: #b30000;
         font-weight: bold;
         }
         .sunday {
         background: #d9d9d9 !important;
         color: #555;
         }
         .today {
         border: 2px solid #000;
         }
         .not-marked {
         background: #cfe2ff !important; /* light blue */
         color: #003d99;
         font-weight: bold;
         }
         /* ===============================
         DEFAULT (DESKTOP & TABLET)
         ================================ */
         .calendar {
         display: grid;
         grid-template-columns: repeat(7, 1fr);
         gap: 8px;
         margin-top: 20px;
         }
         .day-header {
         text-align: center;
         font-weight: bold;
         font-size: 14px;
         }
         .day-box {
         padding: 15px;
         text-align: center;
         border-radius: 10px;
         background: #f2f2f2;
         min-height: 70px;
         font-size: 14px;
         }
         /* ===============================
         MOBILE (≤ 768px)
         ================================ */
         @media (max-width: 768px) {
         .calendar {
         gap: 5px;
         }
         .day-header {
         font-size: 12px;
         }
         .day-box {
         padding: 8px;
         min-height: 55px;
         font-size: 12px;
         }
         .day-box strong {
         font-size: 13px;
         display: block;
         }
         .day-box small {
         font-size: 11px;
         }
         }
         /* ===============================
         Horizontal scroll for usability
         ================================ */
         @media (max-width: 480px) {
         .calendar-wrapper {
         overflow-x: auto;
         }
         .calendar {
         min-width: 480px; /* prevents cramping */
         }
         .day-header {
         font-size: 8px;
         }
         .day-box {
         padding: 3px;
         min-height: 40px;
         border-radius: 6px;
         }
         }
      </style>
   </head>
   <body>
      <!-- Layout wrapper -->
      <div class="layout-wrapper layout-content-navbar">
         <div class="layout-container">
            <!-- Menu -->
            <?php
               include("include/sidebar.php");
               ?>
            <!-- / Menu -->
            <!-- Layout container -->
            <div class="layout-page">
               <!-- Navbar -->
               <?php include("include/header.php"); ?>
               <!-- / Navbar -->
               <!-- Content wrapper -->
               <div class="content-wrapper">
                  <!-- Content -->
                  <div class="container-xxl flex-grow-1 container-p-y">
                     <!--/ Table -->
                     <div class="card">
                        <h5 class="card-header">Attendance</h5>
                        <h2 class="text-center">Monthly Attendance Calendar</h2>
                        <!-- Month Selector -->
                        <form method="GET" class="text-center">
                           <select name="month">
                              <?php for ($m=1; $m<=12; $m++): ?>
                              <option value="<?= sprintf('%02d',$m) ?>" <?= ($m==$month?'selected':'') ?>>
                                 <?= date("F", strtotime("2025-$m-01")) ?>
                              </option>
                              <?php endfor; ?>
                           </select>
                           <select name="year">
                              <?php for ($y = date("Y") - 2; $y <= date("Y"); $y++): ?>
                              <option value="<?= $y ?>" <?= ($y==$year?'selected':'') ?>>
                                 <?= $y ?>
                              </option>
                              <?php endfor; ?>
                           </select>
                           <button type="submit">View</button>
                        </form>
                        <br>
                        <!-- Calendar Grid -->
                        <div class="calendar-wrapper">
                           <div class="calendar">
                              <!-- Week Header -->
                              <div class="day-header">Mon</div>
                              <div class="day-header">Tue</div>
                              <div class="day-header">Wed</div>
                              <div class="day-header">Thu</div>
                              <div class="day-header">Fri</div>
                              <div class="day-header">Sat</div>
                              <div class="day-header">Sun</div>
                              <!-- Blank boxes before month start -->
                              <?php for ($i=1; $i<$startDay; $i++): ?>
                              <div class="day-box"></div>
                              <?php endfor; ?>
                              <!-- Calendar Days -->
                              <?php
                                 for ($day=1; $day<=$totalDays; $day++):
                                 $dateStr = "$year-$month-".sprintf('%02d', $day);
                                 
                                 $isSunday = (date("N", strtotime($dateStr)) == 7);
                                 
                                 // Attendance status from DB
                                 $status = $attendanceData[$dateStr] ?? null;
                                 
                                 $class = "day-box ";
                                 
                                 if ($isSunday) {
                                 // Sunday always grey
                                 $class .= "sunday";
                                 $label = "Sunday";
                                 }
                                 elseif ($status === "Present") {
                                 $class .= "present";
                                 $label = "Present";
                                 }
                                 elseif ($status === "Absent") {
                                 $class .= "absent";
                                 $label = "Absent";
                                 }
                                 else {
                                 // No record / not punched / future
                                 $class .= "not-marked";
                                 $label = "Not Marked";
                                 }
                                 
                                 // Highlight today
                                 if ($dateStr == date("Y-m-d")) {
                                 $class .= " today";
                                 }
                                 
                                 ?>
                              <div class="<?= $class ?>">
                                 <strong><?= $day ?></strong><br>
                                 <small><?= $label ?></small>
                              </div>
                              <?php endfor; ?>
                           </div>
                        </div>
                     </div>
                     <!--/ Table -->
                  </div>
                  <!-- / Content -->
                  <!-- Footer -->
                  <?php include("include/footer.php"); ?>
                  <!-- / Footer -->
               </div>
               <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
         </div>
         <!-- Overlay -->
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
      <!-- Main JS -->
      <script src="assets/js/main.js"></script>
      <!-- Page JS -->
      <!-- Place this tag in your head or just before your close body tag. -->
      <script async defer src="https://buttons.github.io/buttons.js"></script>
   </body>
</html>