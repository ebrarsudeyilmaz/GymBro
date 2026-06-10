<?php
// Olmayan auth_helper yerine kendi yazdığımız güvenlik kalkanını çağırıyoruz
require 'role_control.php';

// Bu sayfaya sadece 'admin' (yönetici) girebilsin diyoruz
check_access(['admin']);

require 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sorgu = $db->prepare("SELECT * FROM users WHERE id = :id AND role = 'trainer'");
    $sorgu->execute([':id' => $id]);
    $antrenor = $sorgu->fetch(PDO::FETCH_ASSOC);
    if (!$antrenor) { 
        header("Location: list_trainers.php"); 
        exit; 
    }
} else {
    header("Location: list_trainers.php"); 
    exit;
}

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id       = intval($_POST['id']);
    $name     = trim($_POST['name']);
    $password = trim($_POST['password']);

    if (!empty($password)) {
        // HOCANIN KRİTERİ: Şifreyi veritabanına asla düz metin yazmıyoruz!
        $guvenli_sifre = password_hash($password, PASSWORD_DEFAULT);
        
        $sql    = "UPDATE users SET name = :name, password = :password WHERE id = :id AND role = 'trainer'";
        $params = [':name' => $name, ':password' => $guvenli_sifre, ':id' => $id];
    } else {
        $sql    = "UPDATE users SET name = :name WHERE id = :id AND role = 'trainer'";
        $params = [':name' => $name, ':id' => $id];
    }

    try {
        $db->prepare($sql)->execute($params);
        header("Location: list_trainers.php");
        exit;
    } catch(PDOException $e) {
        $mesaj = "<p style='color:red; font-weight:bold;'>Hata: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Antrenör Düzenle</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; background-color: #f9f9f9; }
        .form-container { max-width: 400px; margin: 30px auto; padding: 20px; background-color: white; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input { width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background-color: #0056b3; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
        .sifre-bilgi { font-size: 12px; color: #999; margin-top: -10px; margin-bottom: 15px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="form-container">
    <h2>Antrenör Bilgilerini Düzenle</h2>
    <?= $mesaj ?>
    <form action="edit_trainer.php?id=<?= $antrenor['id'] ?>" method="POST">
        <input type="hidden" name="id" value="<?= $antrenor['id'] ?>">

        <label>Ad Soyad:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($antrenor['name']) ?>" required>

        <label>Yeni Şifre:</label>
        <input type="password" name="password" placeholder="Değiştirmek istemiyorsanız boş bırakın">
        <p class="sifre-bilgi">Boş bırakırsanız mevcut şifre korunur.</p>

        <button type="submit">Değişiklikleri Kaydet</button>
        <a href="list_trainers.php" class="btn-cancel">İptal Et ve Geri Dön</a>
    </form>
</div>
</body>
</html>