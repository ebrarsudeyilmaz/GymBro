<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

// URL'den gelen id ile mevcut üyeyi çek
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id = :id AND role = 'member'";
    $sorgu = $db->prepare($sql);
    $sorgu->execute([':id' => $id]);
    $uye = $sorgu->fetch(PDO::FETCH_ASSOC);

    if (!$uye) {
        header("Location: list_member.php");
        exit;
    }
} else {
    header("Location: list_member.php");
    exit;
}

$mesaj = "";

// Form gönderildiğinde güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id       = $_POST['id'];
    $name     = trim($_POST['name']);
    $password = trim($_POST['password']);

    // Şifre alanı boş bırakıldıysa mevcut şifreyi koru
    if (!empty($password)) {
        $update_sql = "UPDATE users SET name = :name, password = :password WHERE id = :id";
        $params = [':name' => $name, ':password' => $password, ':id' => $id];
    } else {
        $update_sql = "UPDATE users SET name = :name WHERE id = :id";
        $params = [':name' => $name, ':id' => $id];
    }

    $islem = $db->prepare($update_sql);

    try {
        $islem->execute($params);
        header("Location: list_member.php");
        exit;
    } catch(PDOException $e) {
        $mesaj = "<p style='color: red; font-weight: bold;'>Hata oluştu: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Üye Düzenle</title>
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
    <h2>Üye Düzenle</h2>

    <?= $mesaj ?>

    <form action="edit_member.php?id=<?= $uye['id'] ?>" method="POST">
        <input type="hidden" name="id" value="<?= $uye['id'] ?>">

        <label>Üye Adı Soyadı:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($uye['name']) ?>" required>

        <label>Yeni Şifre:</label>
        <input type="password" name="password" placeholder="Değiştirmek istemiyorsanız boş bırakın">
        <p class="sifre-bilgi">Boş bırakırsanız mevcut şifre korunur.</p>

        <button type="submit">Değişiklikleri Kaydet</button>
        <a href="list_member.php" class="btn-cancel">İptal Et ve Geri Dön</a>
    </form>
</div>

</body>
</html>