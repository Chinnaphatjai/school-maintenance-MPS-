<?php
session_start();
require 'config.php';

$stmt = $conn->query("SELECT * FROM satisfaction_surveys");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// คำนวณค่าเฉลี่ย
$num_responses = count($requests);
$summary = array_fill(1, 14, 0);

foreach ($requests as $request) {
    for ($i = 1; $i <= 14; $i++) {
        $summary[$i] += $request["es_id$i"];
    }
}

if ($num_responses > 0) {
    foreach ($summary as $key => $value) {
        $summary[$key] = round($value / $num_responses, 2);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานผลความพึงพอใจ</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap">
</head>
<body>
    <header>
        <?php include('menu.php'); ?>
        <h1>รายงานผลความพึงพอใจ</h1>
        <div class="button-container">
            <p><a href="https://maesai.ac.th"><img src="logo2.png" alt="School Logo" height="150"></a></p>
        </div>
    </header>
    
    <main>
        <form id="formqsys" name="formqsys" method="post" action="satisfaction_survey.php">
            <table width="70%" border="1" align="center" cellpadding="0" cellspacing="0" class="table table-bordered table-hover">
                <tr>
                    <td align="center"><strong>5<br>มากที่สุด</strong></td>
                    <td align="center"><strong>4<br>มาก</strong></td>
                    <td align="center"><strong>3<br>ปานกลาง</strong></td>
                    <td align="center"><strong>2<br>น้อย</strong></td>
                    <td align="center"><strong>1<br>น้อยที่สุด</strong></td>
                </tr>
            </table>
        </form>
        
        <h2>รายละเอียดการตอบแบบสอบถาม</h2>
        <table width="70%" border="1" align="center" cellpadding="0" cellspacing="0" class="table table-bordered table-hover">
            <tr>
                <th>ชื่อ</th>
                <th>นามสกุล</th>
                <?php for ($i = 1; $i <= 14; $i++) echo "<th>หัวข้อ $i</th>"; ?>
                <th>ข้อเสนอแนะ</th>
            </tr>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?= htmlspecialchars($request['first_name']) ?></td>
                    <td><?= htmlspecialchars($request['last_name']) ?></td>
                    <?php for ($i = 1; $i <= 14; $i++) echo "<td>" . htmlspecialchars($request["es_id$i"]) . "</td>"; ?>
                    <td><?= htmlspecialchars($request['es_complain']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        
        <h2>สรุปผลแบบประเมิน (ค่าเฉลี่ย)</h2>
        <table width="70%" border="1" align="center" cellpadding="0" cellspacing="0" class="table table-bordered table-hover">
            <tr>
                <?php for ($i = 1; $i <= 14; $i++) echo "<th>หัวข้อ $i</th>"; ?>
            </tr>
            <tr>
                <?php foreach ($summary as $avg) echo "<td>$avg</td>"; ?>
            </tr>
        </table>

        <!-- ปุ่มดาวน์โหลด -->
        <div class="download-buttons">
            <a href="export_excel.php" class="download-button">📥 ดาวน์โหลดเป็น Excel</a>
            <a href="export_word.php" class="download-button">📥 ดาวน์โหลดเป็น Word</a>
        </div>
    </main>

    <?php include('footer.php'); ?>
</body>
</html>