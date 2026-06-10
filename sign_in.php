<?php
session_start();
require 'db.php';

// Zaten giriş yapılmışsa rolüne göre ilgili alanlara yönlendir
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'trainer') {
        header("Location: list_program.php");
        exit;
    } elseif ($_SESSION['role'] === 'member') {
        header("Location: member_view.php?token=" . $_SESSION['member_token']);
        exit;
    }
}

$hata = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = trim($_POST['kullanici_adi']);
    $sifre = trim($_POST['sifre']);

    // Kullanıcıyı veritabanından çekiyoruz (Yönetici, Antrenör veya Üye olabilir)
    $sql = "SELECT * FROM users WHERE name = :name";
    $sorgu = $db->prepare($sql);
    $sorgu->execute([':name' => $kullanici_adi]);
    $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

    // HOCANIN GÜVENLİK KRİTERİ: password_verify kontrolü
    if ($kullanici && password_verify($sifre, $kullanici['password'])) {
        
        // Ortak Session (Oturum) Yapısı
        $_SESSION['user_id'] = $kullanici['id'];
        $_SESSION['role']    = $kullanici['role'];
        
        if ($kullanici['role'] === 'admin' || $kullanici['role'] === 'trainer') {
            $_SESSION['admin_name'] = $kullanici['name'];
            $_SESSION['admin_logged_in'] = true;
            
            // Antrenörler için ilk giriş kontrolü (trainer123)
            if ($kullanici['role'] === 'trainer' && password_verify('trainer123', $kullanici['password'])) {
                $_SESSION['must_change_password'] = true;
                header("Location: change_password.php");
                exit;
            }
            
            header("Location: list_program.php");
            exit;
        } elseif ($kullanici['role'] === 'member') {
            $_SESSION['member_id']     = $kullanici['id'];
            $_SESSION['member_name']   = $kullanici['name'];
            $_SESSION['member_token']  = $kullanici['qr_code_token'];
            $_SESSION['member_logged_in'] = true;
            
            // ÜYELER İÇİN İLK GİRİŞ KONTROLÜ: Eğer şifresi hala varsayılan '123456' ise kilitle!
            if (password_verify('123456', $kullanici['password'])) {
                $_SESSION['must_change_password'] = true; // Şifre değiştirme kilidi aktif
                header("Location: change_password.php");  // Şifre değiştirme sayfasına zorunlu yönlendirme
                exit;
            }
            
            header("Location: member_view.php?token=" . $kullanici['qr_code_token']);
            exit;
        }
    } else {
        $hata = "Hatalı kullanıcı adı veya şifre!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>GYM PRO - Giriş</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #343a40; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); width: 100%; max-width: 350px; }
        .login-box h2 { text-align: center; margin-bottom: 20px; color: #333; }
        .hata-mesaji { color: red; text-align: center; margin-bottom: 15px; font-weight: bold; font-size: 14px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #ffc107; color: #343a40; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; }
        button:hover { background-color: #e0a800; }
    </style>
</head>
<body>
<div class="login-box">
    <h2>GYM PRO<br><small style="font-size: 15px; color: #777;">Sistem Girişi</small></h2>
    <?php if ($hata != ""): ?>
        <div class="hata-mesaji"><?= $hata ?></div>
    <?php endif; ?>
    <form action="sign_in.php" method="POST">
        <label>Kullanıcı Adı</label>
        <input type="text" name="kullanici_adi" placeholder="Adınızı girin" required>
        <label>Şifre</label>
        <input type="password" name="sifre" placeholder="Şifrenizi girin" required>
        <button type="submit">Giriş Yap</button>
    </form>
</div>
</body>
</html>