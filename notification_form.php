<?php
require 'config.php'; // เรียกใช้ไฟล์ config.php เพื่อเชื่อมต่อฐานข้อมูล

// ฟังก์ชันตรวจสอบว่าสตริงเริ่มต้นด้วยคำที่กำหนดหรือไม่
function startsWith($string, $startString) {
    return strpos($string, $startString) === 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$conn) {
        die("การเชื่อมต่อฐานข้อมูลล้มเหลว");
    }

    // ดึงข้อมูลจากฟอร์ม
    $reporter_name = trim($_POST['reporter_name'] ?? '');
    $damaged_item = trim($_POST['damaged_item'] ?? '');
    $location_type = $_POST['location_type'] ?? '';
    $room_number = trim($_POST['room_number'] ?? '');
    $other_location = trim($_POST['other_location'] ?? '');
    $problem_details = trim($_POST['problem_details'] ?? '');
    
    // กำหนดค่าตำแหน่งที่ตั้ง
    $location = ($location_type === 'other') ? $other_location : ($location_type . ' - ' . $room_number);
    
    // ตรวจสอบค่าที่จำเป็นต้องไม่ว่างเปล่า
    if (empty($reporter_name) || empty($damaged_item) || empty($location) || empty($problem_details)) {
        die("กรุณากรอกข้อมูลให้ครบทุกช่องที่จำเป็น");
    }
    
    // การอัปโหลดรูปภาพ
    $image_paths = [];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $max_file_size = 5 * 1024 * 1024;
    $upload_dir = 'uploads/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($_FILES['problem_image']['name'][0])) {
        if (count($_FILES['problem_image']['name']) < 2) {
            die("ต้องอัปโหลดอย่างน้อย 2 รูปภาพ");
        }

        foreach ($_FILES['problem_image']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['problem_image']['name'][$key];
            $file_size = $_FILES['problem_image']['size'][$key];
            $file_tmp = $_FILES['problem_image']['tmp_name'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_extensions)) {
                die("ไฟล์ $file_name มีนามสกุลต้องเป็น JPG, PNG หรือ GIF เท่านั้น");
            }

            if ($file_size > $max_file_size) {
                die("ไฟล์ $file_name มีขนาดใหญ่เกินไป (เกิน 5MB)");
            }

            $new_file_name = uniqid('img_') . '.' . $file_ext;
            $file_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $file_path)) {
                $image_paths[] = $file_path;
            }
        }
    }

    // บันทึกข้อมูลลงฐานข้อมูล
    try {
        $stmt = $conn->prepare("INSERT INTO maintenance_requests (reporter_name, damaged_item, location, problem_details, image_path, notification_date) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$reporter_name, $damaged_item, $location, $problem_details, implode(',', $image_paths)]);
        echo "บันทึกข้อมูลสำเร็จ!";
    } catch (PDOException $e) {
        die("เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Notification Form</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap">
    <style>
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box; /* Ensure padding doesn't affect width */
            display: block; /* ทำให้อยู่กึ่งกลางด้วย margin */
            margin-left: auto;
            margin-right: auto;
        }

        select {
            appearance: none; /* ลบลูกศร default */
            background-image: url('data:image/svg+xml;utf8,<svg fill="%232f4f4f" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>'); /* เพิ่มลูกศรแบบ SVG */
            background-repeat: no-repeat;
            background-position: right 8px center; /* จัดตำแหน่งลูกศร */
            padding-right: 30px; /* เพิ่มพื้นที่สำหรับลูกศร */
        }

        .hidden {
            display: none; /* ซ่อนฟิลด์เพิ่มเติม */
        }

        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
            margin-bottom: 10px;
        }

        .file-input-container input[type="file"] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }

        .file-input-label {
            display: block;
            padding: 10px;
            background-color: #81c784; /* Light green */
            color: white;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .file-input-label:hover {
            background-color: #66bb6a; /* Slightly darker green */
            transform: scale(1.05);
        }

        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .image-preview img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .image-preview img:hover {
            transform: scale(1.1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <header>
    <?php include('menu.php'); ?>
        <h1>แบบฟอร์มแจ้งการซ่อมบำรุง</h1>
    </header>
    <form method="POST" action="process_form.php" enctype="multipart/form-data">
        <label for="reporter_name">ชื่อผู้รายงาน:</label>
        <input type="text" id="reporter_name" name="reporter_name" required><br>

        <label for="damaged_item">รายการที่แจ้ง:</label>
        <input type="text" id="damaged_item" name="damaged_item" required><br>

        <label for="location_type">ที่ตั้ง:</label>
        <select id="location_type" name="location_type" required onchange="toggleLocationFields()">
            <option value="">เลือกที่ตั้ง</option>
            <option value="อาคาร 2">อาคาร 2</option>
            <option value="อาคาร 3">อาคาร 3</option>
            <option value="อาคาร 4">อาคาร 4</option>
            <option value="อาคาร 5">อาคาร 5</option>
            <option value="อาคาร 6">อาคาร 6</option>
            <option value="อาคาร 7">อาคาร 7</option>
            <option value="อาคาร 8">อาคาร 8</option>
            <option value="อาคาร 9">อาคาร 9</option>
            <option value="other">อื่น ๆ</option>
        </select><br>

            <label for="room_number">ห้อง:</label>
            <input type="text" id="room_number" name="room_number"><br>

            <label for="other_location">สถานที่ตั้ง:</label>
            <input type="text" id="other_location" name="other_location"><br>

        <label for="problem_details">รายละเอียดปัญหา:</label>
        <textarea id="problem_details" name="problem_details" required></textarea><br>

        <label for="problem_image">Problem Image (อัปโหลดอย่างน้อย 2 รูป):</label>
        <div class="file-input-container">
            <input type="file" id="problem_image" name="problem_image[]" multiple accept="image/*" required onchange="previewImages(event)">
            <label for="problem_image" class="file-input-label">เลือกไฟล์รูปภาพ</label>
        </div>
        <div class="image-preview" id="image-preview"></div><br>

        <button type="submit">บันทึก</button>
    </form>

    <script>
        function previewImages(event) {
            const imagePreview = document.getElementById("image-preview");
            imagePreview.innerHTML = ""; // ล้างรูปภาพเก่า

            const files = event.target.files;
            if (!files || files.length < 2) {
                alert("กรุณาอัปโหลดอย่างน้อย 2 รูป");
                event.target.value = ""; // ล้างค่า input
                return;
            }

            Array.from(files).forEach((file) => {
                const reader = new FileReader();

                reader.onload = function (e) {
                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.classList.add("fade-in");
                    imagePreview.appendChild(img);
                };

                reader.readAsDataURL(file);
            });
        }
    </script>
    <footer>
        <div class="footer-content bg-primary">
            <img src="https://www.maesai.ac.th/web/wp-content/uploads/2024/06/images.png" width="auto" height="60">
            <p class="text-white">Repair System &copy; Mae Sai Prasitsart School 2025. All rights reserved.</p>
        </div>
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const button = document.querySelector("button[type='submit']");
    const imageInput = document.getElementById("problem_image");
    const reporterName = document.getElementById("reporter_name");
    const damagedItem = document.getElementById("damaged_item");

    function isEmpty() {
        return reporterName.value.trim() === "" || damagedItem.value.trim() === "";
    }

    function moveButton() {
        const maxX = window.innerWidth - button.offsetWidth - 20;
        const maxY = window.innerHeight - button.offsetHeight - 20;

        const randomX = Math.random() * maxX;
        const randomY = Math.random() * maxY;

        button.style.position = "absolute";
        button.style.left = randomX + "px";
        button.style.top = randomY + "px";
    }

    button.addEventListener("mouseover", function () {
        if (isEmpty()) {
            moveButton();
        }
    });

    reporterName.addEventListener("input", function () {
        if (!isEmpty()) {
            button.style.position = "static";
        }
    });

    damagedItem.addEventListener("input", function () {
        if (!isEmpty()) {
            button.style.position = "static";
        }
    });
});
    </script>
</body>
</html>