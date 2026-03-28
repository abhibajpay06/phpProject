<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<?php
   session_start();
   include("include/function.php");
   $conn = connection();
   
   if (!isset($_SESSION['id'])) {
       header("Location: index");
       exit;
   }
   
   ?>
   <?php
   // COUNT UNREAD MSG TO SHOW ON SIDEBAR
				$my_id = $_SESSION['id'];

				$chat_sql = mysqli_query($conn,"SELECT COUNT(*) AS unread FROM messages WHERE receiver_id = '$my_id' AND is_read = '0'");

				$row = mysqli_fetch_assoc($chat_sql);
				$unread_count = $row['unread'];
				?>
<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Chat</title>
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
         .chat-wrapper{width:100%;height:calc(100vh - 120px);display:flex;border:1px solid #ddd;border-radius:10px;overflow:hidden}
         .chat-users{width:280px;background:#fafafa;border-right:1px solid #ddd;display:flex;flex-direction:column}
         .sidebar-users{flex:1;overflow-y:auto;padding:10px}
         .user-item{display:flex;align-items:center;padding:12px;margin-bottom:8px;border-radius:8px;cursor:pointer;background:#fff;border:1px solid #e5e5e5}
         .user-item.active{background:#dcdcff}
         .chat-box{flex:1;background:#f7f7fb;display:flex;flex-direction:column}
         .messages-area{flex:1;padding:20px;overflow-y:auto}
         .message-row{display:flex;margin-bottom:10px}
         .message-row.me{justify-content:flex-end}
         .message-row.other{justify-content:flex-start}
         .msg{max-width:60%;padding:10px 14px;border-radius:12px;font-size:14px}
         .msg.me{background:#696cff;color:#fff}
         .msg.other{background:#ececff}
         .chat-input-area{display:flex;gap:10px;padding:12px;background:#fff;border-top:1px solid #ddd}
         @media(max-width:768px){
         .chat-users{position:absolute;inset:0;z-index:10}
         .chat-box{display:none}
         .chat-wrapper.show-chat .chat-box{display:flex}
         .chat-wrapper.show-chat .chat-users{display:none}
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
               <div class="container-xxl container-p-y">
                  <div class="chat-wrapper">
                     <!-- USERS -->
                     <div class="chat-users">
                        <input type="text" id="searchUser" class="form-control m-2" placeholder="Search user">
                        <div id="userList" class="sidebar-users">Loading...</div>
                     </div>
                     <!-- CHAT -->
                     <div class="chat-box">
                        <div id="chat-box" class="messages-area"></div>
                        <div class="chat-input-area">
                           <img id="imgPreview" style="display:none;max-height:120px;border-radius:8px">
                           <input type="text" id="message" class="form-control" placeholder="Type message">
                           <input type="file" id="fileInput" hidden>
                           <button id="attachBtn" class="btn btn-secondary">📎</button>
                           <button id="sendBtn" class="btn btn-primary">Send</button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
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
      <script>
$(document).ready(function () {

    let currentChatUser = null;
    let currentChatType = null;
    let autoScroll = false;
    let isUserAtBottom = true;

    /* ================= SCROLL DETECTION ================= */
    $("#chat-box").on("scroll", function () {
        const el = this;
        isUserAtBottom =
            el.scrollTop + el.clientHeight >= el.scrollHeight - 20;
    });
	
	/* ================= SIDEBAR UNREAD COUNT ================= */
	function updateSidebarUnread() {
		$.ajax({
			url: "get_unread_count.php",
			dataType: "json",
			success: function (res) {

				if (res.count > 0) {
					$("#chatUnreadBadge")
						.text(res.count)
						.show();
				} else {
					$("#chatUnreadBadge").hide();
				}
			}
		});
	}

	// initial load
	updateSidebarUnread();

	// update every 2 seconds
	setInterval(updateSidebarUnread, 2000);


    /* ================= LOAD USERS ================= */
   function loadUsers() {
    $.ajax({
        url: "load_users.php",
        success: function (data) {
            $("#userList").html(data);

            //  KEEP SELECTED USER HIGHLIGHTED
            if (currentChatUser && currentChatType) {
                $(".user-item").each(function () {
                    if (
                        $(this).data("userid") == currentChatUser &&
                        $(this).data("usertype") == currentChatType
                    ) {
                        $(this).addClass("active");
                    }
                });
            }
        }
    });
}

    loadUsers();
    setInterval(loadUsers, 2000);

    /* ================= CLICK USER ================= */
    $(document).on("click", ".user-item", function () {

        currentChatUser = $(this).data("userid");
        currentChatType = $(this).data("usertype");

        if (!currentChatUser || !currentChatType) return;

        $(".user-item").removeClass("active");
        $(this).addClass("active");

        $(".chat-wrapper").addClass("show-chat");

        autoScroll = true;
        loadMessages();
    });

    /* ================= LOAD MESSAGES ================= */
    function loadMessages() {
        if (!currentChatUser) return;

        $.ajax({
            url: "load_messages.php",
            type: "POST",
            data: {
                receiver_id: currentChatUser,
                receiver_type: currentChatType
            },
            success: function (data) {

                const chatBox = $("#chat-box");
                const oldHeight = chatBox[0].scrollHeight;

                chatBox.html(data);

                const newHeight = chatBox[0].scrollHeight;

                if (autoScroll || isUserAtBottom) {
                    chatBox.scrollTop(newHeight);
                }

                autoScroll = false;
            }
        });
    }

    setInterval(loadMessages, 1500);

    /* ================= SEND MESSAGE ================= */
    function sendMessage() {

        if (!currentChatUser) {
            alert("Select a user first");
            return;
        }

        const msg = $("#message").val().trim();
        const file = $("#fileInput")[0].files[0];

        if (msg === "" && !file) return;

        const form = new FormData();
        form.append("message", msg);
        form.append("receiver_id", currentChatUser);
        form.append("receiver_type", currentChatType);
        if (file) form.append("file", file);

        $.ajax({
            url: "send_message.php",
            type: "POST",
            data: form,
            processData: false,
            contentType: false,
            dataType: "json",   //  REQUIRED
            success: function (res) {

                if (res.status !== "success") {
                    alert(res.msg || "Message failed");
                    return;
                }

                $("#message").val("");
                $("#fileInput").val("");
                $("#imgPreview").hide().attr("src", "");

                autoScroll = true;
                loadMessages();
                loadUsers(); //  reorder list instantly
            }
        });
    }

    /* ================= EVENTS ================= */
    $("#sendBtn").on("click", sendMessage);
    $("#attachBtn").on("click", () => $("#fileInput").click());

    $("#fileInput").on("change", function () {
        const file = this.files[0];
        if (!file) return;

        const r = new FileReader();
        r.onload = e => $("#imgPreview").attr("src", e.target.result).show();
        r.readAsDataURL(file);
    });

    $("#message").on("keypress", function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    /* ================= SEARCH ================= */
    $("#searchUser").on("keyup", function () {
        const v = $(this).val().toLowerCase();
        $(".user-item").each(function () {
            $(this).toggle($(this).text().toLowerCase().includes(v));
        });
    });

});
</script>

   </body>
</html>