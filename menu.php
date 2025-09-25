<nav>
    <div class="hamburger" onclick="toggleMenu()">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <ul>
        <li><a class="active" href="index.php">Home</a></li>
        <li><a href="satisfaction_survey.php">แบบประเมินการใช้งาน</a></li>
        <li><a href="notification_form.php">ฟอร์มแจ้งการซ่อมบำรุง</a></li>
        <li><a href="monthly_summary.php">ดูสรุปรายเดือนและรายปี</a></li>
        <li><a href="login.php">Login for Admin/Staff</a></li>
        <li><a href="in_progress_public.php">ดูคำขอที่กำลังดำเนินการอยู่</a></li>
        <li><a href="completed_public.php">ดูคำขอที่เสร็จสมบูรณ์</a></li>
        <li><a href="satisfaction_report.php">รายงานการประเมินการใช้งาน</a></li>
        <li><a href="about.php">About</a></li>
    </ul>
</nav>

<style>
    nav {
        background-color: #1ABC9C;
        padding: 10px 0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    nav ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
    }

    nav ul li {
        margin: 0 15px;
    }

    nav ul li a {
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    nav ul li a:hover:not(.active) {
        background-color: #1e6658;
        color: #1ABC9C;
    }

    nav ul li a.active {
        background-color: #1ABC9C;
        color: white;
    }

    .hamburger {
        display: none;
        flex-direction: column;
        cursor: pointer;
        margin: 0 15px;
    }

    .hamburger div {
        width: 25px;
        height: 3px;
        background-color: white;
        margin: 4px 0;
        transition: 0.3s;
    }

    @media (max-width: 1440px) {
        nav ul {
            flex-direction: column;
            display: none;
            background-color: #1ABC9C;
            width: 100%;
            text-align: center;
        }

        nav ul.open {
            display: flex;
        }

        .hamburger {
            display: flex;
        }
    }
</style>

<script>
    function toggleMenu() {
        const navMenu = document.querySelector("nav ul");
        navMenu.classList.toggle("open");
    }
</script>
