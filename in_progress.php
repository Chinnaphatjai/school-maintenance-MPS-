<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch in progress requests
$stmt = $conn->query("SELECT * FROM maintenance_requests WHERE status = 'in_progress'");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>In Progress Requests</title>
    <?php include('menu.php'); ?>
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fbe7; /* Light pastel green */
            padding: 20px;
        }
        h1 {
            color: #2e7d32; /* Dark green */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
            background-color: #ffffff; /* White table background */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }
        table th, table td {
            border: 1px solid #e0e0e0;
            padding: 12px;
        }
        table th {
            background-color: #81c784; /* Light green */
            color: white;
            text-transform: uppercase;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background-color: #f9fbe7; /* Very light yellow-green */
        }
        table tr:hover {
            background-color: #e8f5e9; /* Light pastel green */
        }
        table td img {
            display: block;
            max-width: 100px;
            height: auto;
            margin: 0 auto;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        a {
            text-decoration: none;
            color: #2e7d32; /* Dark green */
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
    <body>
    <h1>Dashboard</h1>
    <a href="logout.php">Logout</a>
    <nav>
    <ul>
        <li><a href="dashboard.php">All Requests</a></li>
        <li><a href="in_progress.php">In Progress Requests</a></li>
        <li><a href="completed.php">Completed Requests</a></li>
        <li><a href="monthly_summary.php">Monthly Summary</a></li>
    </ul>
</nav>
</head>
<body>
    <h1>In Progress Requests</h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <table border="1">
        <div id="imageModal" class="modal" onclick="closeModal()">
            <span class="close">&times;</span>
            <img class="modal-content" id="modalImg">
        </div>
        <tr>
            <th>Reporter Name</th>
            <th>รายการที่แจ้ง</th>
            <th>Location</th>
            <th>Problem Details</th>
            <th>Image</th>
            <th>วันที่แจ้ง</th>
            <th>สถานะ</th>
            <th>รายละเอียดการดำเนินการ</th>
            <th>การดำเนินการ</th>
        </tr>
        <?php foreach ($requests as $request): ?>
        <tr>
            <td><?= htmlspecialchars($request['reporter_name']) ?></td>
            <td><?= htmlspecialchars($request['damaged_item']) ?></td>
            <td><?= htmlspecialchars($request['location']) ?></td>
            <td><?= htmlspecialchars($request['problem_details']) ?></td>
            <td>
                <?php 
                    if ($request['image_path']) {
                        $image_paths = explode(',', $request['image_path']); // แยกพาธรูปภาพ
                        foreach ($image_paths as $image) {
                            $image = htmlspecialchars(trim($image)); // ป้องกันโค้ดอันตราย
                            echo '<img src="' . $image . '" width="100" style="margin:5px; cursor:pointer;" onclick="openModal(\'' . $image . '\')">';
                        }
                    } else {
                        echo 'No Image';
                    }
                ?>
            </td>
            <td><?= htmlspecialchars($request['notification_date']) ?></td>
            <td><?= htmlspecialchars($request['status']) ?></td>
            <td><?= htmlspecialchars($request['action_details']) ?></td>
            <td>
                <form action="update_status.php" method="POST">
                    <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                    <select name="status">
                        <option value="pending" <?= $request['status'] === 'รอดำเนินการตรวจสอบ' ? 'selected' : '' ?>>รอดำเนินการตรวจสอบ</option>
                        <option value="in_progress" <?= $request['status'] === 'ตรวจสอบเรียบร้อยอยู่ระหว่างดำเนินการ' ? 'selected' : '' ?>>ตรวจสอบเรียบร้อยอยู่ระหว่างดำเนินการ</option>
                        <option value="completed" <?= $request['status'] === 'สำเร็จ' ? 'selected' : '' ?>>สำเร็จ</option>
                    </select>
                    <textarea name="action_details" placeholder="รายละเอียดการดำเนินการ"><?= htmlspecialchars($request['action_details']) ?></textarea>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <footer>
        <div class="footer-content bg-primary">
        <p><a href="https://maesai.ac.th"><img src="https://www.maesai.ac.th/web/wp-content/uploads/2024/06/images.png" width="auto" height="60"></a></p>
            <p class="text-white">Repair System &copy; Mae Sai Prasitsart School 2025. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>