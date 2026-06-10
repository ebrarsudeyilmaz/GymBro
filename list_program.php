<?php

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$sql = "
    SELECT 
        tp.id, 
        u.name AS member_name, 
        e.name AS exercise_name, 
        e.category,
        tp.sets, 
        tp.reps, 
        tp.weight, 
        tp.day_of_week 
    FROM training_programs tp
    JOIN users u ON tp.member_id = u.id
    JOIN exercises e ON tp.exercise_id = e.id
    ORDER BY tp.id DESC
";
$sorgu = $db->query($sql);
$programlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Antrenman Programları</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; background-color: #f0f2f5; }
        .container { max-width: 1000px; margin: 20px auto; padding: 0 20px; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-add { text-decoration: none; padding: 10px 15px; background-color: #28a745; color: white; border-radius: 5px; font-weight: bold; }
        .btn-add:hover { background-color: #218838; }
        
        .card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .card { background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-top: 5px solid #007bff; }
        .day-badge { display: inline-block; background-color: #007bff; color: white; padding: 5px 10px; border-radius: 15px; font-size: 14px; font-weight: bold; margin-bottom: 10px; }
        .exercise-title { font-size: 20px; margin: 0 0 10px 0; color: #333; }
        .info-row { margin-bottom: 8px; color: #555; }
        
        .card-actions { margin-top: 15px; display: flex; gap: 10px; border-top: 1px solid #eee; padding-top: 15px; }
        .btn-edit { flex: 1; text-align: center; text-decoration: none; padding: 8px; background-color: #ffc107; color: black; border-radius: 5px; font-size: 14px; }
        .btn-delete { flex: 1; text-align: center; text-decoration: none; padding: 8px; background-color: #dc3545; color: white; border-radius: 5px; font-size: 14px; }
    </style>
</head>
<body>

<!-- Menü Dosyasını Çağırıyoruz -->
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="header-flex">
        <h2>Atanan Antrenman Programları</h2>
        <a href="add_program.php" class="btn-add">+ Yeni Program Ata</a>
    </div>

    <div class="card-grid">
        <?php if (count($programlar) > 0): ?>
            <?php foreach ($programlar as $prog): ?>
                <div class="card">
                    <span class="day-badge"><?= htmlspecialchars($prog['day_of_week']) ?></span>
                    <h3 class="exercise-title"><?= htmlspecialchars($prog['exercise_name']) ?></h3>
                    
                    <div class="info-row"><strong>Üye:</strong> <?= htmlspecialchars($prog['member_name']) ?></div>
                    <div class="info-row"><strong>Kategori:</strong> <?= htmlspecialchars($prog['category']) ?></div>
                    <div class="info-row"><strong>Set / Tekrar:</strong> <?= $prog['sets'] ?> x <?= $prog['reps'] ?></div>
                    <div class="info-row"><strong>Ağırlık:</strong> <?= htmlspecialchars($prog['weight']) ?></div>
                    
                    <div class="card-actions">
                        <a href="edit_program.php?id=<?= $prog['id'] ?>" class="btn-edit">Düzenle</a>
                        <a href="delete_program.php?id=<?= $prog['id'] ?>" class="btn-delete" onclick="return confirm('Silmek istediğinize emin misiniz?');">Sil</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Henüz kimseye antrenman programı atanmamış.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>