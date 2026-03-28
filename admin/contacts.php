<?php
session_start();
include("include/function.php");
$conn = connection();

if (!isset($_SESSION['id'])) {
    header("Location: index");
    exit;
}

if(isset($_POST['submit'])){
    $name     = sanitizeInput($conn, $_POST['name']);
    $number = sanitizeInput($conn, $_POST['number']);
    $email = sanitizeInput($conn, $_POST['email']);
    $college      = sanitizeInput($conn, $_POST['college']);
    $course      = sanitizeInput($conn, $_POST['course']);

    insertContacts($conn, $name, $number, $email, $college, $course);
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

  <title>Add Contact</title>

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
              <!--      <span class="app-brand-text demo text-body fw-bolder">Infonix</span>	-->
                  </a>
                </div>

                <h4 class="mb-2">Add New Contact</h4>

                <form method="POST">
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" placeholder="Enter Name" />
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" class="form-control" name="number" placeholder="1234567890" />
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" placeholder="Enter Email Address" />
                  </div>
				  
				   <div class="mb-3">
                    <label class="form-label">College/University</label>
                    <input type="text" class="form-control" name="college" placeholder="Enter College/University Name" />
                  </div>
				  
				   <div class="mb-3">
                    <label class="form-label">Course</label>
                    <input type="text" class="form-control" name="course" placeholder="Enter Course Name" />
                  </div>

                  <div class="mb-3">
                    <button class="btn btn-primary d-grid w-100" type="submit" name="submit">Submit</button>
                  </div>
				  <div class="mb-3">
                    <button class="btn btn-primary d-grid w-100" type="file" name="import">Import Contacts</button>
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
