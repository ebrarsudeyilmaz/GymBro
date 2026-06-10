<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

// foods tablosunu users tablosuyla birleştirerek çekiyoruz
$sql = "
    SELECT 
        f.id,
        u.name AS member_name,
        f.name AS food_name,
        f.meal_type,
        f.date,
        f.gram,
        f.calories,
        f.protein,
        f.carb,
        f.fat
    FROM foods f
    JOIN users u ON f.member_id = u.id
    ORDER BY f.date DESC, f.id DESC
";
$sorgu = $db->query($sql);
$yemekler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yemek Listesi</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; background-color: #f9f9f9; }
        .container { max-width: 1100px; margin: 20px auto; background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-add { text-decoration: none; padding: 10px 15px; background-color: #28a745; color: white; border-radius: 5px; font-weight: bold; }
        .btn-add:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 14px; }
        th { background-color: #343a40; color: #ffc107; }
        tr:hover { background-color: #f1f1f1; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 12px; font-weight: bold; }
        .badge-kahvalti  { background-color: #fff3cd; color: #856404; }
        .badge-ogle      { background-color: #d1ecf1; color: #0c5460; }
        .badge-aksam     { background-color: #d4edda; color: #155724; }
        .badge-ara       { background-color: #f8d7da; color: #721c24; }
        .btn-edit   { text-decoration: none; padding: 5px 10px; background-color: #ffc107; color: black; border-radius: 3px; font-size: 13px; margin-right: 4px; }
        .btn-delete { text-decoration: none; padding: 5px 10px; background-color: #dc3545; color: white; border-radius: 3px; font-size: 13px; }
        .empty-msg { text-align: center; padding: 30px; color: #888; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="header-flex">
        <h2>Yemek Kayıtları</h2>
        <a href="add_meal.php" class="btn-add">+ Yemek Ekle</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Üye</th>
                <th>Yemek</th>
                <th>Öğün</th>
                <th>Tarih</th>
                <th>Gram</th>
                <th>Kalori</th>
                <th>Protein</th>
                <th>Karb</th>
                <th>Yağ</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($yemekler) > 0): ?>
                <?php foreach ($yemekler as $row): ?>
                    <?php
                        // Öğüne göre badge rengi belirleniyor
                        $badge_class = match($row['meal_type']) {
                            'Kahvaltı'  => 'badge-kahvalti',
                            'Öğle'      => 'badge-ogle',
                            'Akşam'     => 'badge-aksam',
                            'Ara Öğün'  => 'badge-ara',
                            default     => ''
                        };
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['member_name']) ?></strong></td>
                        <td><?= htmlspecialchars($row['food_name']) ?></td>
                        <td><span class="badge <?= $badge_class ?>"><?= htmlspecialchars($row['meal_type']) ?></span></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= $row['gram'] ? $row['gram'] . ' g' : '-' ?></td>
                        <td><?= $row['calories'] ? $row['calories'] . ' kcal' : '-' ?></td>
                        <td><?= $row['protein'] ? $row['protein'] . ' g' : '-' ?></td>
                        <td><?= $row['carb'] ? $row['carb'] . ' g' : '-' ?></td>
                        <td><?= $row['fat'] ? $row['fat'] . ' g' : '-' ?></td>
                        <td>
                            <a href="edit_meal.php?id=<?= $row['id'] ?>" class="btn-edit">Düzenle</a>
                            <a href="delete_meal.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Bu kaydı silmek istediğinize emin misiniz?');">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="empty-msg">Henüz hiç yemek kaydı eklenmemiş.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>