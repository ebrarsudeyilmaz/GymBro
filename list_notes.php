<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

$sql = "
    SELECT 
        tn.id,
        u.name AS member_name,
        tn.note,
        tn.created_at
    FROM trainer_notes tn
    JOIN users u ON tn.member_id = u.id
    ORDER BY tn.created_at DESC
";
$sorgu = $db->query($sql);
$notlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Antrenör Notları</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; background-color: #f0f2f5; }
        .container { max-width: 900px; margin: 20px auto; padding: 0 20px; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-add { text-decoration: none; padding: 10px 15px; background-color: #28a745; color: white; border-radius: 5px; font-weight: bold; }
        .btn-add:hover { background-color: #218838; }
        .card { background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.08); border-left: 5px solid #ffc107; margin-bottom: 20px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .member-name { font-size: 18px; font-weight: bold; color: #333; }
        .created-at { font-size: 12px; color: #999; }
        .note-text { color: #555; font-size: 15px; line-height: 1.6; white-space: pre-wrap; }
        .card-actions { margin-top: 15px; display: flex; gap: 10px; border-top: 1px solid #eee; padding-top: 15px; }
        .btn-edit   { flex: 1; text-align: center; text-decoration: none; padding: 8px; background-color: #ffc107; color: black; border-radius: 5px; font-size: 14px; }
        .btn-delete { flex: 1; text-align: center; text-decoration: none; padding: 8px; background-color: #dc3545; color: white; border-radius: 5px; font-size: 14px; }
        .empty-msg { text-align: center; color: #888; margin-top: 40px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="header-flex">
        <h2>Antrenör Notları</h2>
        <a href="add_note.php" class="btn-add">+ Not Ekle</a>
    </div>

    <?php if (count($notlar) > 0): ?>
        <?php foreach ($notlar as $not): ?>
            <div class="card">
                <div class="card-header">
                    <span class="member-name"><?= htmlspecialchars($not['member_name']) ?></span>
                    <span class="created-at"><?= date('d.m.Y H:i', strtotime($not['created_at'])) ?></span>
                </div>
                <div class="note-text"><?= htmlspecialchars($not['note']) ?></div>
                <div class="card-actions">
                    <a href="edit_note.php?id=<?= $not['id'] ?>" class="btn-edit">Düzenle</a>
                    <a href="delete_note.php?id=<?= $not['id'] ?>" class="btn-delete" onclick="return confirm('Bu notu silmek istediğinize emin misiniz?');">Sil</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="empty-msg">Henüz hiç not eklenmemiş.</p>
    <?php endif; ?>
</div>

</body>
</html>