<?php
session_start();
if (!isset($_SESSION['member_logged_in']) || $_SESSION['member_logged_in'] !== true) {
    header("Location: sign_in.php");
    exit;
}
require 'db.php';

$member_id = $_SESSION['member_id'];
$bugun = date('Y-m-d');

// 200ml ekle butonu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "INSERT INTO water_logs (member_id, amount_ml, date) VALUES (:member_id, 200, :date)";
    $islem = $db->prepare($sql);
    $islem->execute([':member_id' => $member_id, ':date' => $bugun]);
}

// Bugünkü toplam su miktarını çek
$sql_toplam = "SELECT SUM(amount_ml) AS toplam FROM water_logs WHERE member_id = :member_id AND date = :date";
$sorgu_toplam = $db->prepare($sql_toplam);
$sorgu_toplam->execute([':member_id' => $member_id, ':date' => $bugun]);
$toplam = $sorgu_toplam->fetch(PDO::FETCH_ASSOC)['toplam'] ?? 0;

// Günlük hedef
$hedef = 2500;
$yuzde = min(100, round(($toplam / $hedef) * 100));
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Su Takibi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .header { background-color: #343a40; color: #ffc107; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 5px 0 0 0; color: #fff; font-size: 13px; }
        .container { max-width: 400px; margin: 30px auto; padding: 0 15px; }

        .su-kart { background-color: white; border-radius: 12px; padding: 25px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .su-miktar { font-size: 52px; font-weight: bold; color: #007bff; margin: 10px 0 5px 0; }
        .su-hedef { font-size: 14px; color: #888; margin-bottom: 20px; }

        .progress-bar { background-color: #e9ecef; border-radius: 20px; height: 16px; overflow: hidden; margin-bottom: 8px; }
        .progress-fill { height: 100%; border-radius: 20px; background: linear-gradient(90deg, #17a2b8, #007bff); transition: width 0.4s ease; }
        .progress-yuzde { font-size: 13px; color: #555; margin-bottom: 20px; }

        .btn-su { width: 100%; padding: 18px; font-size: 18px; font-weight: bold; background-color: #007bff; color: white; border: none; border-radius: 10px; cursor: pointer; transition: background 0.2s; }
        .btn-su:hover { background-color: #0056b3; }
        .btn-su:active { transform: scale(0.98); }

        .bardak-bilgi { font-size: 13px; color: #aaa; margin-top: 10px; }

        .geri-link { display: block; text-align: center; margin-top: 20px; color: #007bff; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="header">
    <h1>💧 Su Takibi</h1>
    <p><?= htmlspecialchars($_SESSION['member_name']) ?> — <?= date('d.m.Y') ?></p>
</div>

<div class="container">
    <div class="su-kart">
        <p style="color:#555; margin:0 0 5px 0; font-size:15px;">Bugün içtiğin su</p>
        <div class="su-miktar"><?= number_format($toplam) ?> ml</div>
        <div class="su-hedef">Günlük hedef: <?= number_format($hedef) ?> ml</div>

        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $yuzde ?>%;"></div>
        </div>
        <div class="progress-yuzde">%<?= $yuzde ?> tamamlandı</div>

        <form method="POST">
            <button type="submit" class="btn-su">💧 200ml İçtim</button>
        </form>
        <p class="bardak-bilgi">Her basış = 1 bardak (200ml)</p>
    </div>

    <a href="member_view.php?token=<?= htmlspecialchars($_SESSION['member_token']) ?>" class="geri-link">← Antrenman sayfama dön</a>
</div>

</body>
</html>