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
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../user/assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
  />

  <title>Update Course</title>

  <link rel="icon" type="image/x-icon" href="../user/assets/img/favicon/faviconn.ico" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
    rel="stylesheet"
  />

  <link rel="stylesheet" href="../user/assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="../user/assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../user/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../user/assets/css/demo.css" />

  <link rel="stylesheet" href="../user/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../user/assets/vendor/css/pages/page-auth.css" />

  <script src="../user/assets/vendor/js/helpers.js"></script>
  <script src="../user/assets/js/config.js"></script>
</head>

<body>

  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <!-- Sidebar -->
      <?php include("include/sidebar.php"); ?>
      <!-- /Sidebar -->

      <!-- Layout container -->
      <div class="layout-page">

        <!-- Navbar -->
        <?php include("include/header.php"); ?>
        <!-- /Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Card -->
            <div class="card">
              <div class="card-body">

                <div class="app-brand justify-content-center mb-4">
                  <a href="index" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">
                      <!-- your SVG logo remains unchanged -->
                    </span>
                    <span class="app-brand-text demo text-body fw-bolder">Infonix</span>
                  </a>
                </div>

                <h4 class="mb-2">Update Course</h4>

                <form method="POST">
                  <div class="mb-3">
                    <label class="form-label">Course Name</label>
                    <input type="text" class="form-control" name="course_name" value="<?= $fetch['course_name'];?>" />
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Course Duration</label>
                    <input type="text" class="form-control" name="course_duration" value="<?= $fetch['course_duration'];?>" />
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Course Fee</label>
                    <input type="text" class="form-control" name="course_fee" value="<?= $fetch['course_fee'];?>" />
                  </div>

                  <div class="mb-3">
                    <button class="btn btn-primary d-grid w-100" type="submit" name="submit">Submit</button>
                  </div>
                </form>

              </div>
            </div>
            <!-- /Card -->

          </div>
        </div>
        <!-- /Content wrapper -->

      </div>
      <!-- /Layout page -->

    </div>
    <!-- /Layout container -->

  </div>
  <!-- /Layout wrapper -->

  <!-- Core JS -->
  <script src="../user/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../user/assets/vendor/libs/popper/popper.js"></script>
  <script src="../user/assets/vendor/js/bootstrap.js"></script>
  <script src="../user/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../user/assets/vendor/js/menu.js"></script>

  <script src="../user/assets/js/main.js"></script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>

</body>
</html>
