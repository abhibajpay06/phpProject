<?php
session_start();
include("include/function.php");
$conn = connection();
if (!isset($_SESSION['id'])) {
    header("Location: index");
    exit;
}
$id = $_GET['id'];
$fetch = updateCourses($id);

if(isset($_POST['submit'])){
	$course_name = sanitizeInput($conn, $_POST['course_name']);
	$course_duration = sanitizeInput($conn, $_POST['course_duration']);
	$course_fee = sanitizeInput($conn, $_POST['course_fee']);
	
	if($update_courses = mysqli_query($conn, "UPDATE `courses` SET course_name = '$course_name', course_duration = '$course_duration', course_fee = '$course_fee' WHERE `id` = '$id'")){
		showAlert('Updated successfully','courses_view');
	}else{
		showAlert('Something went wrong, Try again!','courses_view');
	}
}

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

    <title>Manage Users</title>

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
                <h5 class="card-header">All Users</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table">
                    <thead>
                      <tr class="text-nowrap">
                        <th>Registration No.</th>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Email</th>
                        <th>Parent's No.</th>
                        <th>Upload Documents</th>
                        <th>Update</th>
                        <th>Delete</th>
                        <th>Account Status</th>
                        <th>Account Action</th>
                      </tr>
                    </thead>
                    <tbody>
					<?php foreach($row as $fetch){?>
                      <tr>
                        <td><?= $fetch['registration_no'];?></td>
                        <td><?= $fetch['name'];?></td>
                        <td><?= $fetch['number'];?></td>
                        <td><?= $fetch['email'];?></td>
                        <td><?= $fetch['parents_number'];?></td>
                        <td>
							<form action="view_all_users.php?id=<?= $fetch['id'];?>" method="post" enctype="multipart/form-data">
								<input type="file" name="documents[]" multiple>
								<input type="submit" name="submit" value="Upload">
							</form>
						</td>
                        <td><a href = "user_update.php?id=<?= $fetch['id']?>">Update</a></td>
                        <td><a href = "user_delete.php?id=<?= $fetch['id']?>">Delete</a></td>
                        <td><?= $fetch['action']?></a></td>
						<td><a href="enable_user_account.php?id=<?= $fetch['id'];?>" class="btn btn-success">Enable</a>
							<a href="disable_user_account.php?id=<?= $fetch['id'];?>" class="btn btn-danger">Disable</a>
						</td>
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

    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>