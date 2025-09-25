<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reporter_name = $_POST['reporter_name'];
    $damaged_item = $_POST['damaged_item'];
    
    // ตรวจสอบและรวมค่าของ location
    $location = isset($_POST['location_type']) ? $_POST['location_type'] : '';
    if ($location === 'other') {
        $location = isset($_POST['other_location']) ? $_POST['other_location'] : '';
    } elseif (!empty($_POST['room_number'])) {
        $location .= " ห้อง " . $_POST['room_number'];
    }

    $problem_details = $_POST['problem_details'];
    $notification_date = date('Y-m-d'); // ตั้งค่าวันที่อัตโนมัติ

    // จัดการอัปโหลดหลายรูปภาพ
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $image_paths = [];
    if (!empty($_FILES['problem_image']['name'][0])) {
        foreach ($_FILES['problem_image']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['problem_image']['error'][$key] === UPLOAD_ERR_OK) {
                $file_name = uniqid() . "_" . basename($_FILES['problem_image']['name'][$key]);
                $file_path = $upload_dir . $file_name;
                move_uploaded_file($tmp_name, $file_path);
                $image_paths[] = $file_path;
            }
        }
    }

    $image_path = implode(',', $image_paths); // แปลง array เป็น string เพื่อบันทึกลงฐานข้อมูล

    // Insert ลงฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO maintenance_requests (reporter_name, damaged_item, location, problem_details, image_path, notification_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$reporter_name, $damaged_item, $location, $problem_details, $image_path, $notification_date]);

    // Redirect ไปยังแบบสอบถามความพึงพอใจ
    header("Location: satisfaction_survey.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap">
    <?php include('menu.php'); ?>
    <title>แจ้งซ่อมเสร็จสมบูรณ์</title>
</head>
<body>
    <script>
        alert("แจ้งซ่อมสำเร็จแล้ว! กำลังนำทางไปยังหน้าแรก...");
        window.location.href = "index.php";
    </script>

    <footer>
        <div class="footer-content bg-primary">
            <img src="https://www.maesai.ac.th/web/wp-content/uploads/2024/06/images.png" width="auto" height="60">
            <p class="text-white">Repair System &copy; Mae Sai Prasitsart School 2025. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
