<?php
session_start();
require 'db.php';

// Sistemde zaten admin var mı kontrol et
$kontrol = $db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
$admin_sayisi = $kontrol->fetchColumn();

// Admin varsa bu sayfaya erişimi kapat
if ($admin_sayisi > 0) {
    die("
        <div style='font-family:Arial; text-align:center; margin-top:80px;'>
            <h2>⛔ Bu sayfaya erişim kapalı.</h2>
            <p>Sistemde zaten bir yönetici hesabı mevcut.</p>
            <a href='sign_in.php' style='color:#007bff;'>Giriş sayfasına dön</a>
        </div>
    ");
}

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $password = trim($_POST['password']);
    $tekrar   = trim($_POST['password_tekrar']);

    if ($password !== $tekrar) {
        $mesaj = "<p style='color:red; font-weight:bold;'>Şifreler eşleşmiyor!</p>";
    } elseif (strlen($password) < 4) {
        $mesaj = "<p style='color:red; font-weight:bold;'>Şifre en az 4 karakter olmalı!</p>";
    } else {
        $token = uniqid('admin_');
        $sql = "INSERT INTO users (name, role, password, qr_code_token) VALUES (:name, 'admin', :password, :token)";
        $islem = $db->prepare($sql);

        try {
            $islem->execute([':name' => $name, ':password' => $password, ':token' => $token]);
            // Admin oluşturuldu, giriş sayfasına yönlendir
            header("Location: sign_in.php?setup=ok");
            exit;
        } catch(PDOException $e) {
            $mesaj = "<p style='color:red; font-weight:bold;'>Hata: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>GYM PRO - İlk Kurulum</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #343a40; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); width: 100%; max-width: 380px; }
        .box h2 { text-align: center; margin-bottom: 5px; color: #333; }
        .box p.aciklama { text-align: center; color: #888; font-size: 13px; margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; }
        button:hover { background-color: #218838; }
        .uyari { background-color: #fff3cd; border: 1px solid #ffc107; padding: 10px; border-radius: 4px; font-size: 13px; color: #856404; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="box">
    <h2>GYM PRO</h2>
    <p class="aciklama">İlk yönetici hesabını oluştur</p>

    <div class="uyari">
        ⚠️ Bu sayfa yalnızca sistemde hiç yönetici yokken erişilebilir.
    </div>

    <?= $mesaj ?>

    <form method="POST">
        <label>Yönetici Adı:</label>
        <input type="text" name="name" placeholder="Örn: admin" required>

        <label>Şifre:</label>
        <input type="password" name="password" placeholder="Şifre belirle" required>

        <label>Şifre Tekrar:</label>
        <input type="password" name="password_tekrar" placeholder="Şifreyi tekrar gir" required>

        <button type="submit">Hesabı Oluştur</button>
    </form>
</div>

</body>
</html>