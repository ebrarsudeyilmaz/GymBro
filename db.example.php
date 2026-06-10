<?php
// Bu dosya örnek bağlantı dosyasıdır.
// Kendi ortamınızda kullanmak için bu dosyanın adını 'db.php' yapın ve bilgileri doldurun.

$host = 'localhost';
$dbname = 'veritabani_adiniz_buraya';
$username = 'kullanici_adiniz_buraya'; 
$password = 'sifreniz_buraya';     

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Veritabanı bağlantı hatası: " . $e->getMessage();
    exit;
}
?>