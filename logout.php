<?php
session_start();
session_destroy(); // Oturumdaki tüm bilgileri yok et
header("Location: login.php"); // Giriş sayfasına geri gönder
exit;
?>