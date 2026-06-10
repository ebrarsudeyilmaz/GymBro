<?php
require 'role_control.php';
check_access(['admin']);
require 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Güvenlik tedbiri: id = 1 olan yönetici kazara silinmesin
    if ($id === 1) {
        die("Yönetici hesabı silinemez.");
    }

    try {
        // 1. Önce bu antrenöre bağlı olan üyelerin trainer_id alanını temizle (Karışıklığı önler)
        $update_sql = "UPDATE users SET trainer_id = NULL WHERE trainer_id = :id";
        $db->prepare($update_sql)->execute([':id' => $id]);

        // 2. Antrenörü veritabanından kaldır
        $delete_sql = "DELETE FROM users WHERE id = :id AND role = 'trainer'";
        $db->prepare($delete_sql)->execute([':id' => $id]);

        header("Location: list_trainers.php");
        exit;
    } catch(PDOException $e) {
        echo "Silme işlemi sırasında hata oluştu: " . $e->getMessage();
    }
} else {
    header("Location: list_trainers.php");
    exit;
}
?>