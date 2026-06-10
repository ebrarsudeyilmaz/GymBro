<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM trainer_notes WHERE id = :id";
    $islem = $db->prepare($sql);

    try {
        $islem->execute([':id' => $id]);
        header("Location: list_notes.php");
        exit;
    } catch(PDOException $e) {
        echo "Silme işlemi sırasında hata oluştu: " . $e->getMessage();
    }
} else {
    header("Location: list_notes.php");
    exit;
}
?>