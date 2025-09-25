<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$conn) {
        die("การเชื่อมต่อฐานข้อมูลล้มเหลว");
    }

    // ดึงข้อมูลจากฟอร์ม
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $es_complain = isset($_POST['es_complain']) ? htmlspecialchars($_POST['es_complain']) : null;

    // ตรวจสอบค่าของ es_id1 ถึง es_id14
    $es_ids = [];
    for ($i = 1; $i <= 14; $i++) {
        $es_id_key = "es_id$i";
        $es_ids[$es_id_key] = isset($_POST[$es_id_key]) ? $_POST[$es_id_key] : null;

        // ตรวจสอบว่าค่าอยู่ในช่วง 1-5 หรือไม่
        if (!in_array($es_ids[$es_id_key], ['1', '2', '3', '4', '5'])) {
            die("ค่าใน $es_id_key ไม่ถูกต้อง");
        }
    }

    // บันทึกข้อมูลลงฐานข้อมูล
    try {
        $stmt = $conn->prepare("INSERT INTO satisfaction_surveys (
            first_name, last_name, es_id1, es_id2, es_id3, es_id4, es_id5, es_id6, es_id7,
            es_id8, es_id9, es_id10, es_id11, es_id12, es_id13, es_id14, 
            es_complain, submission_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $stmt->execute(array_merge([$first_name, $last_name], array_values($es_ids), [$es_complain]));

        echo "<p style='color: green; text-align: center;'>ขอบคุณสำหรับการตอบแบบสอบถาม!</p>";
    } catch (PDOException $e) {
        die("เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>แบบฟอร์มสำรวจความพึงพอใจ</title>
    <?php include('menu.php'); ?>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-xs-12 col-md-11">
                <?php // include('menu.php'); ?>
            </div>
        </div>
    </div>

    <!-- Start Article -->
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <br>
                <?php // include('menu_l.php'); ?>
            </div>
            <div class="col-md-9">
                <br />
                <h3 align="center">ประเมินความพึงพอใจต่อระบบ<br>School Maintenance System</h3>
                <form id="formqsys" name="formqsys" method="post" action="satisfaction_survey.php">
                    <table width="70%" border="1" align="center" cellpadding="0" cellspacing="0" class="table table-bordered table-hover">
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <b>ชื่อ</b><br />
                            <input type="text" name="first_name" id="first_name" class="form-control" required />
                            <br />
                            <b>นามสกุล</b><br />
                            <input type="text" name="last_name" id="last_name" class="form-control" required />
                            <br />
                        </div>
                    </div>
                        <tr>
                            <td width="75%" rowspan="2" align="center">
                                <br>
                                <strong>หัวข้อการประเมิน</strong>
                            </td>
                            <td colspan="5" align="center"><strong>ระดับความพึงพอใจ</strong></td>
                        </tr>
                        <tr>
                            <td width="5%" align="center"><strong>5<br>มากที่สุด</strong></td>
                            <td width="5%" align="center"><strong>4<br>มาก</strong></td>
                            <td width="5%" align="center"><strong>3<br>ปานกลาง</strong></td>
                            <td width="5%" align="center"><strong>2<br>น้อย</strong></td>
                            <td width="5%" align="center"><strong>1<br>น้อยที่สุด</strong></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 1.คู่มือการใช้งานระบบอ่านเข้าใจง่ายและปฏิบัติตามคู่มือได้ทันที</td>
                            <td height="30" align="center"><input type="radio" name="es_id1" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id1" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id1" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id1" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id1" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 2.แบบฟอร์มกรอกข้อมูลใช้งานง่าย  มีความเหมาะสม</td>
                            <td height="30" align="center"><input type="radio" name="es_id2" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id2" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id2" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id2" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id2" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 3.มีการจัดหมวดหมู่ให้ง่ายต่อการ ค้นหาและทำความเข้าใจ</td>
                            <td height="30" align="center"><input type="radio" name="es_id3" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id3" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id3" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id3" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id3" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 4.ข้อความถูกต้องตามหลักภาษา และไวยากรณ์</td>
                            <td height="30" align="center"><input type="radio" name="es_id4" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id4" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id4" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id4" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id4" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 5.ความสวยงาม ความทันสมัย น่าสนใจ</td>
                            <td height="30" align="center"><input type="radio" name="es_id5" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id5" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id5" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id5" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id5" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 6.การจัดรูปแบบง่ายต่อการอ่านและการใช้งาน</td>
                            <td height="30" align="center"><input type="radio" name="es_id6" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id6" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id6" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id6" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id6" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 7.เมนูต่างๆ ใช้งานได้ง่าย</td>
                            <td height="30" align="center"><input type="radio" name="es_id7" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id7" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id7" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id7" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id7" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 8.สีพื้นหลังกับสีตัวอักษรมีความเหมาะสมต่อการอ่าน</td>
                            <td height="30" align="center"><input type="radio" name="es_id8" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id8" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id8" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id8" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id8" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 9.ขนาดตัวอักษร และรูปแบบตัวอักษรอ่านได้ง่ายและเหมาะสม</td>
                            <td height="30" align="center"><input type="radio" name="es_id9" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id9" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id9" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id9" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id9" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 10.ความรวดเร็วในการแสดงผลและการใช้งาน</td>
                            <td height="30" align="center"><input type="radio" name="es_id10" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id10" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id10" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id10" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id10" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 11.ความถูกต้องของข้อมูล</td>
                            <td height="30" align="center"><input type="radio" name="es_id11" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id11" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id11" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id11" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id11" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 12.ความถูกต้องในการเชื่อมโยงภายในระบบ</td>
                            <td height="30" align="center"><input type="radio" name="es_id12" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id12" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id12" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id12" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id12" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 13.ความปลอดภัยในการใช้งาน</td>
                            <td height="30" align="center"><input type="radio" name="es_id13" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id13" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id13" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id13" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id13" value="1" /></td>
                        </tr>
                        <tr>
                            <td height="30">&nbsp; 14.ความสะดวกสบายในการใช้งานได้ทุกที่ทุกเวลา โดยไม่ต้องเข้ามาใช้งาน</td>
                            <td height="30" align="center"><input type="radio" name="es_id14" value="5" required /></td>
                            <td height="30" align="center"><input type="radio" name="es_id14" value="4" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id14" value="3" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id14" value="2" /></td>
                            <td height="30" align="center"><input type="radio" name="es_id14" value="1" /></td>
                        </tr>
                    </table>
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <br /><br />
                            <b>ข้อเสนอแนะเพิ่มเติม</b> <br />
                            <textarea name="es_complain" cols="80" rows="3" id="es_complain" class="form-control"></textarea>
                            <br />
                            <button type="submit" name="save" class="btn btn-primary">ส่งแบบประเมิน</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <footer>
        <div class="footer-content">
            <img src="https://www.maesai.ac.th/web/wp-content/uploads/2024/06/images.png" width="auto" height="60">
            <p class="text-white">Repair System &copy; Mae Sai Prasitsart School 2025. All rights reserved.</p>
        </div>
    </footer>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const submitButton = document.querySelector("button[type='submit']");
    const firstName = document.getElementById("first_name");
    const lastName = document.getElementById("last_name");

    function isEmpty() {
        return firstName.value.trim() === "" || lastName.value.trim() === "";
    }

    function moveButton() {
        const maxX = window.innerWidth - submitButton.offsetWidth - 20;
        const maxY = window.innerHeight - submitButton.offsetHeight - 20;

        const randomX = Math.random() * maxX;
        const randomY = Math.random() * maxY;

        submitButton.style.position = "absolute";
        submitButton.style.left = randomX + "px";
        submitButton.style.top = randomY + "px";
    }

    submitButton.addEventListener("mouseover", function () {
        if (isEmpty()) {
            moveButton();
        }
    });

    firstName.addEventListener("input", function () {
        if (!isEmpty()) {
            submitButton.style.position = "static";
        }
    });

    lastName.addEventListener("input", function () {
        if (!isEmpty()) {
            submitButton.style.position = "static";
        }
    });
});
</script>
</body>
</html>