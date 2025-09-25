<?php
session_start();
require 'config.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// ดึงข้อมูลจากฐานข้อมูล
$stmt = $conn->query("SELECT * FROM satisfaction_surveys");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ตั้งค่าหัวตาราง
$headers = ['ชื่อ', 'นามสกุล'];
for ($i = 1; $i <= 14; $i++) {
    $headers[] = "หัวข้อ $i";
}
$headers[] = "ข้อเสนอแนะ";

$sheet->fromArray($headers, null, 'A1');

// ใส่ข้อมูล
$rowIndex = 2;
foreach ($requests as $request) {
    $rowData = [
        $request['first_name'],
        $request['last_name'],
    ];
    for ($i = 1; $i <= 14; $i++) {
        $rowData[] = $request["es_id$i"];
    }
    $rowData[] = $request['es_complain'];

    $sheet->fromArray($rowData, null, "A$rowIndex");
    $rowIndex++;
}

// ตั้งค่าหัว HTTP เพื่อดาวน์โหลดไฟล์
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="satisfaction_report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>