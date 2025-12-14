<?php
include "config.php";
include "class/Database.php";
include "class/Form.php";

session_start();

$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/artikel/index';

$segments = explode('/', trim($path, '/'));

$mod  = $segments[0] ?? 'artikel';
$page = $segments[1] ?? 'index';

$file = "module/$mod/$page.php";

include "template/header.php";

if (file_exists($file)) {
    include $file;
} else {
    echo "<p>Modul tidak ditemukan: $mod/$page</p>";
}

include "template/footer.php";

