<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Maintenance System</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap">
</head>
<body>
    <header>
        <?php include('menu.php'); ?>
        <h1>Welcome to the School Maintenance System</h1>
        <div class="button-container">
            <p><a href="https://maesai.ac.th"><img src="logo2.png" alt="School Logo" width="auto" height="150"></a></p>
        </div>
    </header>
    <main>
        <div class="button-container">
            <a href="notification_form.php"><button>แบบฟอร์มแจ้งการซ่อมบำรุง</button></a>
            <a href="in_progress_public.php"><button>ดูคำขอที่กำลังดำเนินการอยู่</button></a>
            <a href="completed_public.php"><button>ดูคำขอที่เสร็จสมบูรณ์</button></a>
        </div>
    </main>
    <?php include('site-content.php'); ?>
    <footer>
        <div class="footer-content bg-primary">
            <img src="https://www.maesai.ac.th/web/wp-content/uploads/2024/06/images.png" width="auto" height="60">
            <p class="text-white">Repair System &copy; Mae Sai Prasitsart School 2025. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>