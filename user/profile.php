<?php
   session_start();
   include("include/function.php");
   $conn = connection();
   
   if (!isset($_SESSION['email'])) {
       header("Location: index");
       exit;
   }
   
   $profile = userProfile();
   
   if (isset($_POST['submit'])) {
   
       $email   = sanitizeInput($conn, $_POST['email']);
       $number  = sanitizeInput($conn, $_POST['number']);
       $address = sanitizeInput($conn, $_POST['address']);
       $dob     = sanitizeInput($conn, $_POST['dob']);
   
       $domain   = sanitizeInput($conn, $_POST['domain']);
       $github    = sanitizeInput($conn, $_POST['github']);
       $linkedin   = sanitizeInput($conn, $_POST['linkedin']);
       $instagram = sanitizeInput($conn, $_POST['instagram']);
       $facebook  = sanitizeInput($conn, $_POST['facebook']);
   
       // ================= PROFILE PIC =================
		if (!empty($_FILES['new_profile_pic']['name'])) {

			$profile_pic = $_FILES['new_profile_pic'];
			$tempname = $profile_pic['tmp_name'];

			// Get extension safely
			$ext = strtolower(pathinfo($profile_pic['name'], PATHINFO_EXTENSION));

			// Generate random filename
			$imageName = uniqid('', true) . "." . $ext;

			// Final upload path
			$folder = "img/" . $imageName;

			// Upload file
			move_uploaded_file($tempname, $folder);

			// Delete old image (ONLY if new uploaded)
			if (!empty($profile['profile_pic']) && file_exists($profile['profile_pic'])) {
				unlink($profile['profile_pic']);
			}

		} else {
			// No new image selected → keep old image
			$folder = $profile['profile_pic'];
		}

   
       $emailSession = $_SESSION['email'];
   
       $query = "UPDATE user_login SET
                   email='$email',
                   number='$number',
                   address='$address',
                   dob='$dob',
                   profile_pic='$folder',
                   domain='$domain',
                   github='$github',
                   linkedin='$linkedin',
                   instagram='$instagram',
                   facebook='$facebook'
                 WHERE email='$emailSession'";
   
      if(mysqli_query($conn, $query)){
   
       showAlert('Saved Successfully','profile');
	   
       exit;
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
   data-assets-path="assets/"
   data-template="vertical-menu-template-free"
   >
   <head>
      <meta charset="utf-8" />
      <meta
         name="viewport"
         content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
         />
      <title>Profile</title>
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
         body{
         background: #f7f7ff;
         margin-top:20px;
         }
         .card {
         position: relative;
         display: flex;
         flex-direction: column;
         min-width: 0;
         word-wrap: break-word;
         background-color: #fff;
         background-clip: border-box;
         border: 0 solid transparent;
         border-radius: .25rem;
         margin-bottom: 1.5rem;
         box-shadow: 0 2px 6px 0 rgb(218 218 253 / 65%), 0 2px 6px 0 rgb(206 206 238 / 54%);
         }
         .me-2 {
         margin-right: .5rem!important;
         }
         #profile-img {
         width: 100px;
         height: 100px;
         border-radius: 50%;
         object-fit: cover;   
         border: 1px solid #0d6efd;
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
               <!--PROFILE-->
               <form method="POST" enctype="multipart/form-data">
                  <div class="container">
                     <div class="main-body">
                        <div class="row">
                           <!-- LEFT CARD -->
                           <div class="col-lg-4">
                              <div class="card">
                                 <div class="card-body text-center">
                                    <img src="<?= $profile['profile_pic']; ?>" class="rounded-circle p-1 bg-primary" id="profile-img" width="110">
                                    <input type="file" name="new_profile_pic" class="form-control mt-3">
                                    <h4 class="mt-3"><?= $profile['name']; ?></h4>
                                    <p class="text-secondary"><?= $profile['registration_no']; ?></p>
                                    <hr>
                                    <!-- SOCIAL LINKS -->
                                    <div class="mb-2">
                                       <label><strong>Domain</strong></label>
                                       <input type="text" name="domain" class="form-control" placeholder="https://www.google.com/"
                                          value="<?= $profile['domain']; ?>">
                                    </div>
                                    <div class="mb-2">
                                       <label><strong>GitHub</strong></label>
                                       <input type="text" name="github" class="form-control" placeholder="https://www.github.com/"
                                          value="<?= $profile['github']; ?>">
                                    </div>
                                    <div class="mb-2">
                                       <label><strong>linkedin</strong></label>
                                       <input type="text" name="linkedin" class="form-control" placeholder="https://www.linkedin.com/"
                                          value="<?= $profile['linkedin']; ?>">
                                    </div>
                                    <div class="mb-2">
                                       <label><strong>Instagram</strong></label>
                                       <input type="text" name="instagram" class="form-control" placeholder="https://www.instagram.com/"
                                          value="<?= $profile['instagram']; ?>">
                                    </div>
                                    <div class="mb-2">
                                       <label><strong>Facebook</strong></label>
                                       <input type="text" name="facebook" class="form-control" placeholder="https://www.facebook.com/"
                                          value="<?= $profile['facebook']; ?>">
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!-- RIGHT CARD -->
                           <div class="col-lg-8">
                              <div class="card">
                                 <div class="card-body">
                                    <div class="row mb-3">
                                       <div class="col-sm-3">Course</div>
                                       <div class="col-sm-9">
                                          <input type="text" class="form-control" value="<?= $profile['course']; ?>" readonly>
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-sm-3">Date Of Joining</div>
                                       <div class="col-sm-9">
                                          <input type="text" class="form-control" value="<?= $profile['created_at']; ?>" readonly>
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-sm-3">Full Name</div>
                                       <div class="col-sm-9">
                                          <input type="text" class="form-control" value="<?= $profile['name']; ?>" readonly>
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-sm-3">Registration No.</div>
                                       <div class="col-sm-9">
                                          <input type="text" class="form-control" value="<?= $profile['registration_no']; ?>" readonly>
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-sm-3">Date of Birth</div>
                                       <div class="col-sm-9">
                                          <input type="text" class="form-control" name="dob" value="<?= $profile['dob']; ?>" readonly>
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-sm-3">Email</div>
                                       <div class="col-sm-9">
                                          <input type="email" class="form-control" name="email" value="<?= $profile['email']; ?>">
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-sm-3">Mobile No.</div>
                                       <div class="col-sm-9">
                                          <input type="text" class="form-control" name="number" value="<?= $profile['number']; ?>">
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-sm-3">Address</div>
                                       <div class="col-sm-9">
                                          <input type="text" class="form-control" name="address" value="<?= $profile['address']; ?>">
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-sm-3">Father's Name</div>
                                       <div class="col-sm-9">
                                          <input type="text" class="form-control" value="<?= $profile['fathers_name']; ?>" readonly>
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-sm-3">Mother's Name</div>
                                       <div class="col-sm-9">
                                          <input type="text" class="form-control" value="<?= $profile['mothers_name']; ?>" readonly>
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                       <div class="col-sm-3">Parent's Number</div>
                                       <div class="col-sm-9">
                                          <input type="text" class="form-control" value="<?= $profile['parents_number']; ?>" readonly>
                                       </div>
                                    </div>
                                    <!-- SAVE BUTTON -->
                                    <div class="row">
                                       <div class="col-sm-3"></div>
                                       <div class="col-sm-9">
                                          <button type="submit" name="submit" class="btn btn-primary px-4">
                                          Save Changes
                                          </button>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
               <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
         </div>
         <!-- Overlay -->
      </div>
      <!-- / Layout wrapper -->
      <!-- Core JS -->
      <!-- build:js assets/vendor/js/core.js -->
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