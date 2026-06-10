<?php
$host = 'localhost';
$dbname = 'dbstorage24360859159';
$username = 'dbusr24360859159'; // XAMPP'ın varsayılan veritabanı kullanıcısı
$password = 'dUzy2uAlTTa9';     // XAMPP'ta varsayılan şifre boştur

try {
    // PDO ile güvenli veritabanı bağlantısı kuruyoruz
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Veritabanı bağlantı hatası: " . $e->getMessage();
    exit;
}
?>