<?php
require 'config.php';

// Fetch in progress requests
$stmt = $conn->query("SELECT * FROM maintenance_requests WHERE status = 'in_progress'");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>In Progress Requests</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<style>
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
    background-color:rgb(255, 255, 255); /* Very light yellow-green */
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
</style>
<body>
    <?php include('menu.php'); ?>
    <h1>In Progress Requests</h1>
    <table width="100%" border="1" align="center" cellpadding="10" cellspacing="10" class="table table-bordered table-hover">
        <tr>
            <th>Reporter Name</th>
            <th>รายการที่แจ้ง</th>
            <th>Location</th>
            <th>Problem Details</th>
            <th>Image</th>
            <th>วันที่แจ้ง</th>
            <th>สถานะ</th>
            <th>รายละเอียดการดำเนินการ</th>
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
        </tr>
        <?php endforeach; ?>
    </table>
    <footer>
        <div class="footer-content bg-primary">
            <img src="https://www.maesai.ac.th/web/wp-content/uploads/2024/06/images.png" width="auto" height="60">
            <p class="text-white">Repair System &copy; Mae Sai Prasitsart School 2025. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>