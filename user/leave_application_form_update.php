<?php
session_start();

include("include/function.php");
$conn = connection();

// Check if user is logged in
   if (!isset($_SESSION['email'])) {
      header("Location: index");
      exit;
   }

$sql_leave_application = updateLeaveApplicationForm();

if(isset($_POST['submit'])){
	$course = sanitizeInput($conn, $_POST['course']);
	$name = sanitizeInput($conn, $_POST['name']);
	$leave_purpose = sanitizeInput($conn, $_POST['leave_purpose']);
	$leave_from = sanitizeInput($conn, $_POST['leave_from']);
	$leave_to = sanitizeInput($conn, $_POST['leave_to']);
	
	$update_query = "UPDATE `leave_application` SET course = $course, name = $name, leave_purpose = $leave_purpose, leave_from = $leave_from, leave_to = $leave_to WHERE `id` = '$id'";
	$update_leave_application_form = mysqli_query($conn, $update_query);
}
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

    <title>Update Leave Application Form</title>

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

							  <!-- Basic Layout -->
							  <div class="row">
								<div class="col-xl">
								  <div class="card mb-4">
									<div class="card-header d-flex justify-content-between align-items-center">
									  <h5 class="mb-0">Leave Application Form</h5>
									  
									</div>
									<div class="card-body">
									  <form>
									  
									  <!--Dinamically from db table-->
										<div class="mb-3">
										  <label class="form-label">Course</label>
										  <input type="text" class="form-control" name="course" />
										</div>
										
										<div class="mb-3">
										  <label class="form-label" for="basic-default-fullname">Full Name</label>
										  <input type="text" class="form-control" id="basic-default-fullname" name="name" />
										</div>
										
										<div class="mb-3">
										  <label class="form-label">Leave Purpuse</label>
										  <select class="form-control" name="leave_purpose">
											<option class="disabled">--Choose--</option>
											<option value="Medical Leave">Medical Leave</option>
											<option value="Emergency Leave">Emergency Leave</option>
											<option value="Casual leave">Casual Leave</option>
										  </select>
										</div>
										<div class="mb-3">
										  <label class="form-label">From</label>
										  <input type="date" class="form-control" name="leave_from" />
										</div>
										<div class="mb-3">
										  <label class="form-label">To</label>
										  <input type="date" class="form-control" name="leave_from" />
										</div>										
										
										
										
										
										<button type="submit" class="btn btn-primary" name="submit">Submit</button>
									  </form>
									</div>
								  </div>
								</div>
							</div>
						</div>
			
				 <!-- Footer -->
					<?php include("include/footer.php"); ?>
					<!-- / Footer -->

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
  </body>
</html>
