<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

if (isset($_GET['member_id']) && isset($_GET['date'])) {
    $member_id = $_GET['member_id'];
    $date      = $_GET['date'];

    // O güne ait tüm su kayıtlarını sil
    $sql = "DELETE FROM water_logs WHERE member_id = :member_id AND date = :date";
    $islem = $db->prepare($sql);

    try {
        $islem->execute([':member_id' => $member_id, ':date' => $date]);
        header("Location: list_water.php");
        exit;
    } catch(PDOException $e) {
        echo "Silme işlemi sırasında hata oluştu: " . $e->getMessage();
    }
} else {
    header("Location: list_water.php");
    exit;
}
?>