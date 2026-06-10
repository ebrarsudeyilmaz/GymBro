<?php
require 'role_control.php';

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
require 'db.php';

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $yeni_sifre = trim($_POST['yeni_sifre']);
    $yeni_sifre_tekrar = trim($_POST['yeni_sifre_tekrar']);

    if (strlen($yeni_sifre) < 6) {
        $mesaj = "<p style='color: red; font-weight: bold;'>Şifreniz en az 6 karakter olmalıdır!</p>";
    } elseif ($yeni_sifre !== $yeni_sifre_tekrar) {
        $mesaj = "<p style='color: red; font-weight: bold;'>Girdiğiniz şifreler birbiriyle eşleşmiyor!</p>";
    } elseif ($yeni_sifre === '123456' || $yeni_sifre === 'trainer123') {
        $mesaj = "<p style='color: red; font-weight: bold;'>Güvenliğiniz için lütfen varsayılan şifre dışında yeni bir şifre belirleyin!</p>";
    } else {
        // HOCANIN KRİTERİ: Yeni şifreyi hash yapısına çeviriyoruz
        $guvenli_sifre = password_hash($yeni_sifre, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $db->prepare($sql);
        
        try {
            $stmt->execute([
                ':password' => $guvenli_sifre,
                ':id' => $_SESSION['user_id']
            ]);

            // Şifre başarıyla değiştiği için programsal kilidi tamamen kaldırıyoruz!
            unset($_SESSION['must_change_password']);

            $mesaj = "<p style='color: green; font-weight: bold;'>🔑 Şifreniz başarıyla güncellendi! Yönlendiriliyorsunuz...</p>";
            
            // DİNAMİK YÖNLENDİRME: Kullanıcı üye ise kendi sayfasına, admin/trainer ise ortak panele fırlatıyoruz
            if ($_SESSION['role'] === 'member') {
                header("refresh:2; url=member_view.php?token=" . $_SESSION['member_token']);
            } else {
                header("refresh:2; url=list_program.php");
            }
        } catch(PDOException $e) {
            $mesaj = "<p style='color: red;'>Hata oluştu: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Şifre Yenileme Merkezi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .form-container { max-width: 400px; margin: 50px auto; padding: 30px; background-color: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border-top: 5px solid #ffc107; }
        input { width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background-color: #0056b3; }
        label { font-weight: bold; display: block; margin-bottom: 5px; color: #333; }
        .uyari-kutusu { background-color: #fff3cd; color: #856404; padding: 12px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; line-height: 1.4; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>🔒 Şifre Güncelleme</h2>
    
    <?php if (isset($_SESSION['must_change_password'])): ?>
        <div class="uyari-kutusu">
            <strong>İlk Giriş Güvenlik Uyarısı:</strong> <br>
            Sistem güvenliğiniz için varsayılan şifrenizi değiştirmeden sistemi kullanmanıza izin verilmemektedir. Lütfen yeni bir şifre belirleyin.
        </div>
    <?php endif; ?>

    <?= $mesaj ?> 

    <form action="change_password.php" method="POST">
        <label>Yeni Şifre:</label>
        <input type="password" name="yeni_sifre" placeholder="En az 6 karakter girin" required>

        <label>Yeni Şifre (Tekrar):</label>
        <input type="password" name="yeni_sifre_tekrar" placeholder="Yeni şifrenizi tekrar yazın" required>

        <button type="submit">Şifremi Güncelle</button>
    </form>
</div>

</body>
</html>