<?php

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

// 1. URL'den gelen id'yi yakala ve mevcut bilgileri veritabanından çek
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM exercises WHERE id = :id";
    $sorgu = $db->prepare($sql);
    $sorgu->execute([':id' => $id]);
    $hareket = $sorgu->fetch(PDO::FETCH_ASSOC);

    // Eğer böyle bir id yoksa listeye geri yolla
    if (!$hareket) {
        header("Location: list_exercise.php");
        exit;
    }
} else {
    header("Location: list_exercise.php");
    exit;
}

// 2. Form gönderildiğinde (Kaydet'e basıldığında) güncelleme işlemini yap
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    // UPDATE komutu ile eski verinin üstüne yazıyoruz
    $update_sql = "UPDATE exercises SET name = :name, category = :category, description = :description WHERE id = :id";
    $islem = $db->prepare($update_sql);

    try {
        $islem->execute([
            ':name' => $name,
            ':category' => $category,
            ':description' => $description,
            ':id' => $id
        ]);
        // Başarılıysa listeye yönlendir
        header("Location: list_exercise.php");
        exit;
    } catch(PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hareket Düzenle</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; }
        .form-container { max-width: 400px; margin: auto; padding: 20px; background-color: white; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background-color: #0056b3; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Hareket Düzenle</h2>
    
    <form action="edit_exercise.php?id=<?= $hareket['id'] ?>" method="POST">
        <!-- ID'yi kullanıcından gizli bir şekilde POST ile gönderiyoruz -->
        <input type="hidden" name="id" value="<?= $hareket['id'] ?>">

        <label>Hareket Adı:</label>
        <!-- value kısmına veritabanından çektiğimiz mevcut ismi yazdırıyoruz -->
        <input type="text" name="name" value="<?= htmlspecialchars($hareket['name']) ?>" required>

        <label>Kategori:</label>
        <select name="category" required>
            <!-- Hangi kategori seçiliyse onu otomatik 'selected' yapıyoruz -->
            <option value="Göğüs" <?= $hareket['category'] == 'Göğüs' ? 'selected' : '' ?>>Göğüs</option>
            <option value="Sırt" <?= $hareket['category'] == 'Sırt' ? 'selected' : '' ?>>Sırt</option>
            <option value="Bacak" <?= $hareket['category'] == 'Bacak' ? 'selected' : '' ?>>Bacak</option>
            <option value="Omuz" <?= $hareket['category'] == 'Omuz' ? 'selected' : '' ?>>Omuz</option>
            <option value="Kol" <?= $hareket['category'] == 'Kol' ? 'selected' : '' ?>>Kol</option>
            <option value="Kardiyo" <?= $hareket['category'] == 'Kardiyo' ? 'selected' : '' ?>>Kardiyo</option>
        </select>

        <label>Açıklama (Opsiyonel):</label>
        <textarea name="description" rows="3"><?= htmlspecialchars($hareket['description']) ?></textarea>

        <button type="submit">Değişiklikleri Kaydet</button>
        <a href="list_exercise.php" class="btn-cancel">İptal Et ve Geri Dön</a>
    </form>
</div>

</body>
</html>