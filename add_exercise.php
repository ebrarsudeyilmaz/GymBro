<?php

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
// 1. Veritabanı bağlantımızı sayfaya dahil ediyoruz
require 'db.php'; 

$mesaj = ""; // Ekranda göstereceğimiz başarı veya hata mesajı için boş değişken

// 2. Formun gönderilip gönderilmediğini kontrol ediyoruz
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri (name özelliklerine göre) PHP değişkenlerine alıyoruz
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    // 3. Veritabanına ekleme (INSERT) sorgumuzu hazırlıyoruz
    $sql = "INSERT INTO exercises (name, category, description) VALUES (:name, :category, :description)";
    $islem = $db->prepare($sql);
    
    try {
        // 4. Verileri eşleştirip sorguyu çalıştırıyoruz
        $islem->execute([
            ':name' => $name,
            ':category' => $category,
            ':description' => $description
        ]);
        $mesaj = "<p style='color: green; font-weight: bold;'>Hareket başarıyla eklendi!</p>";
    } catch(PDOException $e) {
        $mesaj = "<p style='color: red; font-weight: bold;'>Hata oluştu: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Hareket Ekle</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; }
        .form-container { max-width: 400px; margin: auto; padding: 20px; background-color: white; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #218838; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Yeni Hareket Ekle</h2>
    
    <!-- PHP'den gelen mesaj burada gösterilecek -->
    <?= $mesaj ?> 

    <!-- Veriler POST metodu ile aynı sayfaya gönderiliyor -->
    <form action="add_exercise.php" method="POST">
        <label>Hareket Adı:</label>
        <input type="text" name="name" placeholder="Örn: Barbell Squat" required>

        <label>Kategori:</label>
        <select name="category" required>
            <option value="Göğüs">Göğüs</option>
            <option value="Sırt">Sırt</option>
            <option value="Bacak">Bacak</option>
            <option value="Omuz">Omuz</option>
            <option value="Kol">Kol</option>
            <option value="Kardiyo">Kardiyo</option>
        </select>

        <label>Açıklama (Opsiyonel):</label>
        <textarea name="description" rows="3" placeholder="Hareketin yapılışıyla ilgili antrenör notları..."></textarea>

        <button type="submit">Hareketi Kaydet</button>
    </form>
</div>

</body>
</html>