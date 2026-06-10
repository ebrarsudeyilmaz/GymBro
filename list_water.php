<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

// Günlük toplamları üyeye göre çek
$sql = "
    SELECT 
        u.id AS member_id,
        u.name AS member_name,
        wl.date,
        SUM(wl.amount_ml) AS gunluk_toplam
    FROM water_logs wl
    JOIN users u ON wl.member_id = u.id
    GROUP BY wl.member_id, wl.date
    ORDER BY wl.date DESC, u.name ASC
";
$sorgu = $db->query($sql);
$kayitlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);

// Üye bazında grupla
$gruplu = [];
foreach ($kayitlar as $kayit) {
    $gruplu[$kayit['member_name']][] = $kayit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Su Takip Kayıtları</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; background-color: #f9f9f9; }
        .container { max-width: 900px; margin: 20px auto; padding: 0 20px; }
        h2 { color: #333; }
        .uye-blok { background-color: white; border-radius: 10px; padding: 20px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .uye-baslik { font-size: 18px; font-weight: bold; color: #343a40; border-bottom: 2px solid #007bff; padding-bottom: 8px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { background-color: #f8f9fa; color: #555; font-weight: bold; }
        tr:last-child td { border-bottom: none; }
        .miktar { font-weight: bold; color: #007bff; }
        .hedef-ok { color: #28a745; font-weight: bold; }
        .hedef-eksik { color: #dc3545; }
        .empty-msg { text-align: center; color: #888; margin-top: 40px; }
        .btn-sil { text-decoration: none; padding: 4px 10px; background-color: #dc3545; color: white; border-radius: 3px; font-size: 12px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h2>💧 Su Takip Kayıtları</h2>

    <?php if (count($gruplu) > 0): ?>
        <?php foreach ($gruplu as $uye_adi => $gunler): ?>
            <div class="uye-blok">
                <div class="uye-baslik">👤 <?= htmlspecialchars($uye_adi) ?></div>
                <table>
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Günlük Toplam</th>
                            <th>Bardak Sayısı</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gunler as $gun): ?>
                            <tr>
                                <td><?= date('d.m.Y', strtotime($gun['date'])) ?></td>
                                <td class="miktar"><?= number_format($gun['gunluk_toplam']) ?> ml</td>
                                <td><?= $gun['gunluk_toplam'] / 200 ?> bardak</td>
                                <td>
                                    <?php if ($gun['gunluk_toplam'] >= 2500): ?>
                                        <span class="hedef-ok">✅ Hedefe ulaştı</span>
                                    <?php else: ?>
                                        <span class="hedef-eksik">⚠️ <?= 2500 - $gun['gunluk_toplam'] ?> ml eksik</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="delete_water.php?member_id=<?= $gun['member_id'] ?>&date=<?= $gun['date'] ?>" 
                                       class="btn-sil" 
                                       onclick="return confirm('Bu güne ait tüm su kayıtları silinecek. Emin misiniz?');">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="empty-msg">Henüz hiç su kaydı bulunmuyor.</p>
    <?php endif; ?>
</div>

</body>
</html>