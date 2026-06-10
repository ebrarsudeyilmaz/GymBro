<?php
require 'role_control.php';
// GÜVENLİK: Sisteme yeni antrenör ekleme yetkisi sadece yöneticidedir!
check_access(['admin']);
require 'db.php';

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $token = uniqid('trainer_'); 
    
    // HOCANIN KRİTERİ: Şifreyi veritabanına güvenli hash'leyerek kaydediyoruz
    // Varsayılan giriş şifresi: trainer123
    $guvenli_sifre = password_hash('trainer123', PASSWORD_DEFAULT); 

    // users tablonuzdaki kolon yapısına birebir uygun INSERT sorgusu
    $sql = "INSERT INTO users (name, role, password, qr_code_token, trainer_id) 
            VALUES (:name, 'trainer', :password, :token, NULL)";
    $islem = $db->prepare($sql);
    
    try {
        $islem->execute([
            ':name' => $name, 
            ':password' => $guvenli_sifre, 
            ':token' => $token
        ]);
        $mesaj = "<p style='color: green; font-weight: bold;'>🏋️‍♂️ Antrenör başarıyla eklendi! Giriş Şifresi: trainer123</p>";
    } catch(PDOException $e) {
        $mesaj = "<p style='color: red; font-weight: bold;'>Hata oluştu: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Antrenör Ekle</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; }
        .container { max-width: 400px; margin: 50px auto; background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h2 style="margin-top:0;">Yeni Antrenör Kaydı</h2>
    
    <?= $mesaj ?> 

    <form action="add_trainer.php" method="POST">
        <label>Antrenör Adı Soyadı:</label>
        <input type="text" name="name" placeholder="Örn: Murat Hoca" required>
        
        <button type="submit">Antrenörü Sisteme Kaydet</button>
    </form>
</div>

</body>
</html>