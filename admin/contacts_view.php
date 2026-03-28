<?php
session_start();

include("include/function.php");
$conn = connection();
$id = $_SESSION['id'];

if (!isset($_SESSION['id'])) {
    header("Location: index");
    exit;
}

$row = viewContacts();


?>

<!DOCTYPE html>


<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../user/assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>All Contacts</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../user/assets/img/favicon/faviconn.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../user/assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../user/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../user/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../user/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../user/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../user/assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../user/assets/js/config.js"></script>
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
                <h5 class="card-header">Contacts</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table">
				  <form method="post" class="m-5">
					<input type="search" name="search" value=""/>
					<input type="submit" name="submit" value="search"/>
				  </form>
                    <thead>
                      <tr class="text-nowrap">
                        <th>Name</th>
                        <th>Mobile Number</th>
                        <th>Email</th>
                        <th>College/University</th>
                        <th>Course</th>
                        <th>Update</th>
                        <th>Delete</th>
                      </tr>
                    </thead>
                    <tbody>
					<?php foreach($row as $fetch){?>
                      <tr>
                        <td><?= $fetch['name'];?></td>
                        <td><a href="tel:<?= $fetch['number'];?>"><?= $fetch['number'];?></a></td>
                        <td><?= $fetch['email'];?></td>
                        <td><?= $fetch['college'];?></td>
                        <td><?= $fetch['course'];?></td>
                        <td><a href = "contacts_update.php?id=<?= $fetch['id']?>">Update</a></td>
                        <td><a href = "contacts_delete.php?id=<?= $fetch['id']?>">Delete</a></td>
                      </tr>
					 <?php } ?>
                    </tbody>
                  </table>
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
    <!-- build:js ../user/assets/vendor/js/core.js -->
    <script src="../user/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../user/assets/vendor/libs/popper/popper.js"></script>
    <script src="../user/assets/vendor/js/bootstrap.js"></script>
    <script src="../user/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../user/assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="../user/assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>