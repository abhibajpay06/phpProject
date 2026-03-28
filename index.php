<!DOCTYPE html>
<html lang="en">
   <head>
      <!-- ========== META & TITLE ========== -->
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <title>Infonix</title>
      <!-- ========== BOOTSTRAP & ICONS ========== -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
      <!-- ========== CUSTOM CSS ========== -->
      <link href="user/assets/css/landingpage.css" rel="stylesheet">
   </head>
   <body>
      <!-- ========== BACKGROUND ANIMATION ========== -->
      <div class="animated-bg"></div>
      <!-- ========== HEADER SECTION ========== -->
      <header class="text-center mt-4">
         <div class="title">
            <h3 class="fw-bold">Infonix Services Pvt Ltd</h3>
            <p class="h5 text-light">🔐 Choose your access type</p>
         </div>
      </header>
      <!-- ========== MAIN CONTENT ========== -->
      <main>
         <section class="card-wrapper d-flex justify-content-center align-items-center flex-wrap gap-5 py-4">
            <!-- ===== ADMIN CARD ===== -->
            <a href="admin" class="circle-card admin-card text-center text-decoration-none">
               <div class="ring"></div>
               <i class="bi bi-shield-lock-fill"></i>
               <h3 class="fw-semibold">Admin</h3>
               <p>Management Access</p>
               <span class="btn-login">Login</span>
            </a>
            <!-- ===== USER CARD ===== -->
            <a href="user" class="circle-card user-card text-center text-decoration-none">
               <div class="ring"></div>
               <i class="bi bi-person-circle"></i>
               <h3 class="fw-semibold">User</h3>
               <p>Member Dashboard</p>
               <span class="btn-login">Login</span>
            </a>
         </section>
      </main>
      <!-- ========== FOOTER SECTION ========== -->
      <footer class="text-center mt-5 mb-3 text-secondary small">
         &copy; <span id="year"></span> Infonix Services Pvt Ltd. All rights reserved.
      </footer>
      <!-- ========== BOOTSTRAP JS ========== -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
      <!-- ========== CUSTOM SCRIPT ========== -->
      <script>
         // Auto-update current year in footer
         document.getElementById("year").textContent = new Date().getFullYear();
      </script>
   </body>
</html>