<?php
session_start();
require 'config.php';
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

// ดึงข้อมูลจากฐานข้อมูล
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

// สร้างไฟล์ Word
$phpWord = new PhpWord();

// เพิ่มส่วนหัวของไฟล์
$section = $phpWord->addSection();
$section->addText('สรุปผลจากแบบประเมินความพึงพอใจของผู้เข้าทดสอบระบบ แจ้ซ่อมแซมอัจฉริยะ', array('bold' => true, 'size' => 16));
$section->addText('(School Maintenance System) ในรายวิชา ว32104 การออกแบบเทคโนโลยี', array('size' => 14));
$section->addText('');

// เพิ่มตารางสรุปผล
$table = $section->addTable(array('borderSize' => 6, 'borderColor' => '000000', 'width' => 100 * 50));
$table->addRow();
$table->addCell(2000)->addText('หัวข้อการประเมิน', array('bold' => true));
$table->addCell(1000)->addText('ค่าเฉลี่ย', array('bold' => true));

foreach ($summary as $key => $value) {
    $table->addRow();
    $table->addCell(2000)->addText("หัวข้อ $key");
    $table->addCell(1000)->addText($value);
}

// บันทึกไฟล์ชั่วคราว
$filename = 'satisfaction_summary.docx';
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($filename);

// ดาวน์โหลดไฟล์
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename=' . $filename);
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));
readfile($filename);

// ลบไฟล์ชั่วคราวหลังจากดาวน์โหลดเสร็จ
unlink($filename);
exit;
?>