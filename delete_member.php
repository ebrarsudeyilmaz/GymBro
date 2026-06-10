<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Üyeye ait tüm ilişkili kayıtları sil, sonra üyeyi sil
        $db->prepare("DELETE FROM training_programs WHERE member_id = :id")->execute([':id' => $id]);
        $db->prepare("DELETE FROM foods          WHERE member_id = :id")->execute([':id' => $id]);
        $db->prepare("DELETE FROM water_logs     WHERE member_id = :id")->execute([':id' => $id]);
        $db->prepare("DELETE FROM trainer_notes  WHERE member_id = :id")->execute([':id' => $id]);
        $db->prepare("DELETE FROM users          WHERE id = :id AND role = 'member'")->execute([':id' => $id]);

        header("Location: list_member.php");
        exit;
    } catch(PDOException $e) {
        echo "Silme işlemi sırasında hata oluştu: " . $e->getMessage();
    }
} else {
    header("Location: list_member.php");
    exit;
}
?>