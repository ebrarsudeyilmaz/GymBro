<?php

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // training_programs tablosundan ilgili ID'yi sil
    $sql = "DELETE FROM training_programs WHERE id = :id";
    $islem = $db->prepare($sql);

    try {
        $islem->execute([':id' => $id]);
        // Silme başarılıysa listeye (kartların olduğu sayfaya) geri dön
        header("Location: list_program.php");
        exit;
    } catch(PDOException $e) {
        echo "Silme işlemi sırasında hata oluştu: " . $e->getMessage();
    }
} else {
    // Sayfaya yanlışlıkla direkt girilirse listeye geri gönder
    header("Location: list_program.php");
    exit;
}
?>