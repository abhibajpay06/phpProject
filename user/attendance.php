<?php
   session_start();
   
   include("include/function.php");
   $conn = connection();
   date_default_timezone_set("Asia/Kolkata");
   
   // Check if user is logged in
   if (!isset($_SESSION['email'])) {
      header("Location: index");
      exit;
   }
   
   /* ---------------- USER DETAILS ---------------- */
   $name = $_SESSION['name'];            // taken from session
   $registration_no = $_SESSION['registration_no'];            // taken from session
   $today = date("Y-m-d");
   $status = 'Present';
   
   /* ---------------- CHECK TODAY'S RECORD ---------------- */
   $attendance = mysqli_query($conn,
      "SELECT * FROM attendance 
       WHERE registration_no='$registration_no' AND date='$today'"
   );
   
   $record = mysqli_fetch_assoc($attendance);
   
   /* ---------------- HANDLE PUNCH ---------------- */
   if (isset($_POST['punch'])) {
   
       if (!$record) {
           // First punch (IN)
           $punch_in = date("H:i:s");
           mysqli_query($conn,
               "INSERT INTO attendance (name, registration_no, date, punch_in, status)
                VALUES ('$name', '$registration_no', '$today', '$punch_in', '$status')"
           );
       } 
       else if ($record['punch_in'] != "" && $record['punch_out'] == "") {
           // Second punch (OUT)
           $punch_out = date("H:i:s");
           mysqli_query($conn,
               "UPDATE attendance 
                SET punch_out='$punch_out'
                WHERE id=".$record['id']
           );
       }
   
       header("Location: ".$_SERVER['PHP_SELF']);
       exit;
   }
   
   /* Reload updated data */
   $attendance = mysqli_query($conn,
      "SELECT * FROM attendance 
       WHERE registration_no='$registration_no' AND date='$today'"
   );
   $record = mysqli_fetch_assoc($attendance);
   ?>
<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default">
   <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Attendance</title>
      <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.webp" />
      <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />
      <link rel="stylesheet" href="assets/vendor/css/core.css" />
      <link rel="stylesheet" href="assets/vendor/css/theme-default.css" />
      <link rel="stylesheet" href="assets/css/demo.css" />
      <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
      <script src="assets/vendor/js/helpers.js"></script>
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
      </style>
   </head>
   <body>
      <div class="layout-wrapper layout-content-navbar">
         <div class="layout-container">
            <?php include("include/sidebar.php"); ?>
            <div class="layout-page">
               <?php include("include/header.php"); ?>
               <div class="content-wrapper">
                  <div class="container-xxl flex-grow-1 container-p-y">
                     <div class="container">
                        <h1>Punch<i class="fas fa-user-clock"></i>time</h1>
                        <div class="row col-12">
                           <div id="buttons" class="col-6">
                              <form method="POST">
                                 <div id="now">
                                    <h3>Date: <?= date("d-m-y") ?></h3>
                                    <br>
                                    <span><?= date("h:i:s A") ?></span>
                                 </div>
                                 <button name="punch" id="punch" class="btn">
                                    <div id="punch-in" class="btn btn-success">Punch In</div>
                                    <div id="punch-out" class="btn btn-danger">Punch Out</div>
                                    <div id="bye" class="btn btn-primary">Completed</div>
                                 </button>
                              </form>
                              <table border="1" width="300">
                                 <tr>
                                    <th>Name</th>
                                    <td><?= $name ?></td>
                                 </tr>
                                 <tr>
                                    <th>Reg ID</th>
                                    <td><?= $registration_no ?></td>
                                 </tr>
                                 <tr>
                                    <th>Punch In</th>
                                    <td><?= $record['punch_in'] ?? '' ?></td>
                                 </tr>
                                 <tr>
                                    <th>Punch Out</th>
                                    <td><?= $record['punch_out'] ?? '' ?></td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <script src="assets/vendor/libs/jquery/jquery.js"></script>
      <script src="assets/vendor/js/bootstrap.js"></script>
      <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
      <script src="assets/vendor/js/menu.js"></script>
      <script src="assets/js/main.js"></script>
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
   </body>
</html>