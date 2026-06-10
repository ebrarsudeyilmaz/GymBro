<?php

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// 1. Veritabanı bağlantısı
require 'db.php';

// 2. URL'den 'id' değerinin gelip gelmediğini kontrol ediyoruz
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 3. Silme sorgusunu hazırlıyoruz (Gelen ID'ye sahip kaydı bul ve sil)
    $sql = "DELETE FROM exercises WHERE id = :id";
    $islem = $db->prepare($sql);

    try {
        // Sorguyu çalıştırıyoruz
        $islem->execute([':id' => $id]);
        
        // 4. İşlem başarılıysa kullanıcıyı tekrar listeleme sayfasına yönlendiriyoruz
        header("Location: list_exercise.php");
        exit;
    } catch(PDOException $e) {
        echo "Silme işlemi sırasında hata oluştu: " . $e->getMessage();
    }
} else {
    // Eğer sayfaya id olmadan girilmeye çalışılırsa direkt listeye geri gönder
    header("Location: list_exercises.php");
    exit;
}
?>