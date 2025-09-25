<?php
session_start();
session_destroy();
header('Location: XO.php');
exit;