<?php
session_start();
include("include/function.php");
$conn = connection();
date_default_timezone_set("Asia/Kolkata");


if (!isset($_SESSION['id'])) {
    header("Location: index");
    exit;
}

$user_id = $_SESSION['id'];

// Fetch login history
$query = mysqli_query($conn, "SELECT * FROM `user_login_logs` WHERE user_id = '$user_id' ORDER BY id DESC");
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

    <title>Login History</title>

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

              
              <!--/ Table -->
              <div class="card">
                <h5 class="card-header">Login History</h5>
                <div class="card-body table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Date & Time</th>
                <th>IP Address</th>
                <th>Device</th>
                <th>Browser</th>
                <th>OS</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 1;
            while ($row = mysqli_fetch_assoc($query)) {
            ?>
                <tr>
                    <td><?= $count++; ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($row['login_time'])); ?></td>
                    <td><?= $row['ip_address']; ?></td>
                    <td><?= $row['device_type']; ?></td>
                    <td><?= $row['browser']; ?></td>
                    <td><?= $row['operating_system']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
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
