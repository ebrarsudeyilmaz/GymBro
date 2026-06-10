<?php
require 'role_control.php';
// GÜVENLİK: Antrenör listesini sadece yönetici (admin) görebilir
check_access(['admin']);
require 'db.php';

// Veritabanından sadece role = 'trainer' olanları çekiyoruz
$sql = "SELECT id, name, qr_code_token FROM users WHERE role = 'trainer' ORDER BY id DESC";
$sorgu = $db->query($sql);
$antrenorler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Antrenör Listesi</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; background-color: #f9f9f9; }
        .container { max-width: 900px; margin: 20px auto; background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-add { text-decoration: none; padding: 10px 15px; background-color: #007bff; color: white; border-radius: 5px; font-weight: bold; }
        .btn-add:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #343a40; color: #ffc107; }
        tr:hover { background-color: #f1f1f1; }
        .btn-edit { text-decoration: none; padding: 5px 10px; background-color: #ffc107; color: black; border-radius: 3px; font-size: 14px; margin-right: 5px; }
        .btn-delete { text-decoration: none; padding: 5px 10px; background-color: #dc3545; color: white; border-radius: 3px; font-size: 14px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="header-flex">
        <h2>Sistemdeki Kayıtlı Antrenörler</h2>
        <a href="add_trainer.php" class="btn-add">+ Yeni Antrenör Ekle</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Antrenör Adı Soyadı</th>
                <th>Sistem Kimliği (Token)</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($antrenorler) > 0): ?>
                <?php foreach ($antrenorler as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td><span style="background:#eee; padding:3px 6px; border-radius:3px; font-size:12px;"><?= htmlspecialchars($row['qr_code_token']) ?></span></td>
                        <td>
                            <a href="edit_trainer.php?id=<?= $row['id'] ?>" class="btn-edit">Düzenle</a>
                            <a href="delete_trainer.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Bu antrenörü silmek istediğinize emin misiniz? Sorumlu olduğu üyeler antrenörsüz kalacaktır.');">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Henüz sisteme antrenör eklenmemiş.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>