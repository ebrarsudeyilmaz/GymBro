<?php

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$sql = "SELECT * FROM exercises ORDER BY id DESC";
$sorgu = $db->query($sql);
$hareketler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hareket Listesi</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; background-color: #f9f9f9; }
        .container { max-width: 900px; margin: 20px auto; background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-add { text-decoration: none; padding: 10px 15px; background-color: #28a745; color: white; border-radius: 5px; font-weight: bold; }
        .btn-add:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        tr:hover { background-color: #f1f1f1; }
        .btn-edit { text-decoration: none; padding: 5px 10px; background-color: #ffc107; color: black; border-radius: 3px; font-size: 14px; margin-right: 5px; }
        .btn-delete { text-decoration: none; padding: 5px 10px; background-color: #dc3545; color: white; border-radius: 3px; font-size: 14px; }
    </style>
</head>
<body>

<!-- Menü Dosyasını Çağırıyoruz -->
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="header-flex">
        <h2>Sistemdeki Hareketler</h2>
        <a href="add_exercise.php" class="btn-add">+ Yeni Hareket Ekle</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Hareket Adı</th>
                <th>Kategori</th>
                <th>Açıklama</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($hareketler) > 0): ?>
                <?php foreach ($hareketler as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>
                            <a href="edit_exercise.php?id=<?= $row['id'] ?>" class="btn-edit">Düzenle</a>
                            <a href="delete_exercise.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Bu hareketi silmek istediğinize emin misiniz?');">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Henüz hiç hareket eklenmemiş.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>