<?php
require 'config.php';

// Fetch monthly summary data
$monthlySql = "
    SELECT 
        DATE_FORMAT(notification_date, '%Y-%m') AS month,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed
    FROM maintenance_requests
    GROUP BY DATE_FORMAT(notification_date, '%Y-%m')
    ORDER BY month
";
$monthlyStmt = $conn->query($monthlySql);
$monthlyData = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch yearly summary data
$yearlySql = "
    SELECT 
        DATE_FORMAT(notification_date, '%Y') AS year,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed
    FROM maintenance_requests
    GROUP BY DATE_FORMAT(notification_date, '%Y')
    ORDER BY year
";
$yearlyStmt = $conn->query($yearlySql);
$yearlyData = $yearlyStmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for Chart.js
$labels = [];
$pendingData = [];
$inProgressData = [];
$completedData = [];

foreach ($monthlyData as $row) {
    $labels[] = $row['month'];
    $pendingData[] = $row['pending'];
    $inProgressData[] = $row['in_progress'];
    $completedData[] = $row['completed'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly and Yearly Summary</title>
    <?php include('menu.php'); ?>
    <link rel="stylesheet" href="styles/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fbe7; /* Light pastel green */
            padding: 20px;
        }
        h1 {
            color: #2e7d32; /* Dark green */
        }
        .chart-container {
            width: 80%;
            margin: 0 auto;
            background-color: #ffffff; /* White background */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
        a {
            text-decoration: none;
            color: #2e7d32; /* Dark green */
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Monthly and Yearly Summary</h1>

    <!-- Monthly Summary Table -->
    <h2>Monthly Summary</h2>
    <table border="1">
        <tr>
            <th>Month</th>
            <th>Pending</th>
            <th>In Progress</th>
            <th>Completed</th>
        </tr>
        <?php foreach ($monthlyData as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['month']) ?></td>
            <td><?= htmlspecialchars($row['pending']) ?></td>
            <td><?= htmlspecialchars($row['in_progress']) ?></td>
            <td><?= htmlspecialchars($row['completed']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Yearly Summary Table -->
    <h2>Yearly Summary</h2>
    <table border="1">
        <tr>
            <th>Year</th>
            <th>Pending</th>
            <th>In Progress</th>
            <th>Completed</th>
        </tr>
        <?php foreach ($yearlyData as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['year']) ?></td>
            <td><?= htmlspecialchars($row['pending']) ?></td>
            <td><?= htmlspecialchars($row['in_progress']) ?></td>
            <td><?= htmlspecialchars($row['completed']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Monthly Summary Chart -->
    <div class="chart-container">
        <canvas id="monthlySummaryChart"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('monthlySummaryChart').getContext('2d');
        const monthlySummaryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [
                    {
                        label: 'Pending',
                        data: <?= json_encode($pendingData) ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)', // Red
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'In Progress',
                        data: <?= json_encode($inProgressData) ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)', // Blue
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Completed',
                        data: <?= json_encode($completedData) ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)', // Green
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Requests'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Maintenance Requests Summary'
                    }
                }
            }
        });
    </script>
    <footer>
        <div class="footer-content bg-primary">
            <img src="https://www.maesai.ac.th/web/wp-content/uploads/2024/06/images.png" width="auto" height="60">
            <p class="text-white">Repair System &copy; Mae Sai Prasitsart School 2025. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>