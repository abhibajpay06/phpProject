<?php
session_start();
    date_default_timezone_set('Asia/Kolkata');
include("include/function.php");
$conn = connection();

$id = $_SESSION['id'];
$user_docs = mysqli_query($conn, "SELECT * FROM `user_login` WHERE id='$id'");
$profile = mysqli_fetch_array($user_docs);
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

    <title>Leave Application Form</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.webp" />

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
									  <h5 class="mb-0">Documents</h5>
									</div>
									<!--DOCUMENTS WILL DISPLAY HERE-->
									<div class="ms-3">
									<?php
										$docs = $profile['documents']; // comma-separated file names

										if (!empty($docs)) {

											$docArray = explode(",", $docs);

											echo "<h4>Uploaded Documents</h4>";

											foreach ($docArray as $file) {

												$file = trim($file);

												echo "
												<div>
													<a href='../admin/uploads/$file' download>$file</a>
												</div>";
											}

										} else {
											echo "No documents uploaded.";
										}
									?>
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
