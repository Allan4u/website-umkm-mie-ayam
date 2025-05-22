<?php
$host = 'localhost';           
$username = 'root';            
$password = '';                
$database = 'mie_ayam';        

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

function format_rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

function generate_slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9 -]/', '', $string);
    $string = str_replace(' ', '-', $string);
    return $string;
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function require_admin_login() {
    if (!is_admin_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Jakarta');

?>