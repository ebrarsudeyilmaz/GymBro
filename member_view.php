<?php
session_start();
require 'db.php';

// Token ile erişim
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("<h2 style='text-align:center; margin-top:50px; font-family:sans-serif;'>Geçersiz veya eksik bağlantı. Lütfen QR kodunuzu tekrar okutun.</h2>");
}

$token = $_GET['token'];

$sql_uye = "SELECT * FROM users WHERE qr_code_token = :token AND role = 'member'";
$sorgu_uye = $db->prepare($sql_uye);
$sorgu_uye->execute([':token' => $token]);
$uye = $sorgu_uye->fetch(PDO::FETCH_ASSOC);

if (!$uye) {
    die("<h2 style='text-align:center; margin-top:50px; font-family:sans-serif; color:red;'>Kullanıcı bulunamadı. Bağlantınızın süresi dolmuş olabilir.</h2>");
}

$member_id = $uye['id'];

// Oturum uyumu: token sahibi üye başka bir hesapla giriş yapmışsa senkron et
$member_logged_in = (
    isset($_SESSION['member_logged_in']) &&
    $_SESSION['member_logged_in'] === true &&
    isset($_SESSION['member_id']) &&
    $_SESSION['member_id'] == $member_id
);

// Su ekleme (POST) - sadece giriş yapmış üye yapabilir
if ($member_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_water'])) {
    $bugun = date('Y-m-d');
    $sql_w = "INSERT INTO water_logs (member_id, amount_ml, date) VALUES (:mid, 200, :date)";
    $stm_w = $db->prepare($sql_w);
    $stm_w->execute([':mid' => $member_id, ':date' => $bugun]);
    header("Location: member_view.php?token=" . urlencode($token) . "#su");
    exit;
}

// Antrenman programları
$sql_program = "
    SELECT tp.sets, tp.reps, tp.weight, tp.day_of_week,
           e.name AS exercise_name, e.category
    FROM training_programs tp
    JOIN exercises e ON tp.exercise_id = e.id
    WHERE tp.member_id = :member_id
    ORDER BY FIELD(tp.day_of_week, 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar')
";
$sorgu_program = $db->prepare($sql_program);
$sorgu_program->execute([':member_id' => $member_id]);
$programlar = $sorgu_program->fetchAll(PDO::FETCH_ASSOC);

// Yemek listesi (son 7 gün)
$sql_meals = "
    SELECT name, meal_type, date, gram, calories, protein, carb, fat
    FROM foods
    WHERE member_id = :member_id
    ORDER BY date DESC, id DESC
    LIMIT 30
";
$sorgu_meals = $db->prepare($sql_meals);
$sorgu_meals->execute([':member_id' => $member_id]);
$yemekler = $sorgu_meals->fetchAll(PDO::FETCH_ASSOC);

// Antrenör notları
$sql_notes = "
    SELECT note, created_at FROM trainer_notes
    WHERE member_id = :member_id
    ORDER BY created_at DESC
    LIMIT 20
";
$sorgu_notes = $db->prepare($sql_notes);
$sorgu_notes->execute([':member_id' => $member_id]);
$notlar = $sorgu_notes->fetchAll(PDO::FETCH_ASSOC);

// Su takibi
$bugun = date('Y-m-d');
$sql_water = "SELECT SUM(amount_ml) AS toplam FROM water_logs WHERE member_id = :mid AND date = :date";
$stm_water = $db->prepare($sql_water);
$stm_water->execute([':mid' => $member_id, ':date' => $bugun]);
$su_toplam = $stm_water->fetch(PDO::FETCH_ASSOC)['toplam'] ?? 0;
$su_hedef  = 2500;
$su_yuzde  = min(100, round(($su_toplam / $su_hedef) * 100));
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($uye['name']) ?> - GYM PRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .site-header { background-color: #343a40; color: #ffc107; padding: 20px; text-align: center; }
        .site-header h1 { margin: 0; font-size: 24px; }
        .site-header p  { margin: 5px 0 0; color: #fff; font-size: 14px; }
        .section-title { background-color: #343a40; color: #ffc107; padding: 10px 16px; border-radius: 8px; font-size: 18px; font-weight: bold; margin: 30px 0 15px; }
        .exercise-card { background: white; border-left: 4px solid #ffc107; border-radius: 8px; padding: 15px; margin-bottom: 12px; box-shadow: 0 2px 4px rgba(0,0,0,.06); }
        .day-title { background-color: #007bff; color: white; padding: 8px 14px; border-radius: 8px; margin-bottom: 12px; font-size: 16px; font-weight: bold; }
        .stats-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; background: #f8f9fa; padding: 10px; border-radius: 6px; text-align: center; }
        .stat-box strong { display: block; font-size: 18px; color: #28a745; }
        .stat-box span   { font-size: 12px; color: #555; }
        .meal-badge { display: inline-block; padding: 3px 9px; border-radius: 10px; font-size: 12px; font-weight: bold; }
        .badge-kahvalti { background:#fff3cd; color:#856404; }
        .badge-ogle     { background:#d1ecf1; color:#0c5460; }
        .badge-aksam    { background:#d4edda; color:#155724; }
        .badge-ara      { background:#f8d7da; color:#721c24; }
        .note-card { background: white; border-left: 4px solid #ffc107; border-radius: 8px; padding: 14px; margin-bottom: 10px; box-shadow: 0 2px 4px rgba(0,0,0,.05); }
        .water-card { background: white; border-radius: 12px; padding: 25px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,.08); }
        .su-miktar { font-size: 48px; font-weight: bold; color: #007bff; }
        .progress-bar-bg { background:#e9ecef; border-radius:20px; height:16px; overflow:hidden; margin:10px 0 6px; }
        .progress-fill   { height:100%; border-radius:20px; background: linear-gradient(90deg,#17a2b8,#007bff); transition: width .4s; }
    </style>
</head>
<body>

<div class="site-header">
    <h1>GYM PRO</h1>
    <p>Sporcu: <strong><?= htmlspecialchars($uye['name']) ?></strong>
    <?php if (!$member_logged_in): ?>
        &nbsp;|&nbsp; <a href="sign_in.php" style="color:#ffc107;">Giriş yap</a>
    <?php endif; ?>
    </p>
</div>

<div class="container" style="max-width:680px; padding:10px 15px 40px;">

    <!-- NAVİGASYON -->
    <nav class="d-flex gap-2 flex-wrap mt-3 mb-2">
        <a href="#antrenman" class="btn btn-sm btn-outline-primary">🏋️ Antrenman</a>
        <a href="#yemek"     class="btn btn-sm btn-outline-success">🥗 Yemek Listesi</a>
        <a href="#notlar"    class="btn btn-sm btn-outline-warning">📝 Notlar</a>
        <a href="#su"        class="btn btn-sm btn-outline-info">💧 Su Takibi</a>
        <?php if ($member_logged_in): ?>
            <a href="logout.php" class="btn btn-sm btn-outline-danger ms-auto">Çıkış</a>
        <?php endif; ?>
    </nav>

    <!-- ANTRENMAN PROGRAMLARI -->
    <div id="antrenman">
        <div class="section-title">🏋️ Antrenman Programı</div>
        <?php if (count($programlar) > 0):
            $gruplu = [];
            foreach ($programlar as $p) $gruplu[$p['day_of_week']][] = $p;
            foreach ($gruplu as $gun => $hareketler): ?>
            <div class="day-title"><?= htmlspecialchars($gun) ?> Günü</div>
            <?php foreach ($hareketler as $h): ?>
                <div class="exercise-card">
                    <small style="color:#888; text-transform:uppercase; letter-spacing:1px;"><?= htmlspecialchars($h['category']) ?></small>
                    <h5 style="margin:5px 0 10px; color:#333;"><?= htmlspecialchars($h['exercise_name']) ?></h5>
                    <div class="stats-grid">
                        <div class="stat-box"><strong><?= $h['sets'] ?></strong><span>Set</span></div>
                        <div class="stat-box"><strong><?= $h['reps'] ?></strong><span>Tekrar</span></div>
                        <div class="stat-box"><strong><?= htmlspecialchars($h['weight']) ?></strong><span>Ağırlık</span></div>
                    </div>
                </div>
            <?php endforeach; endforeach; ?>
        <?php else: ?>
            <p class="text-muted text-center mt-3">Henüz antrenman programı atanmamış.</p>
        <?php endif; ?>
    </div>

    <!-- YEMEK LİSTESİ -->
    <div id="yemek">
        <div class="section-title">🥗 Yemek Listesi</div>
        <?php if (count($yemekler) > 0): ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover bg-white rounded shadow-sm">
                    <thead style="background:#343a40; color:#ffc107;">
                        <tr><th>Yemek</th><th>Öğün</th><th>Tarih</th><th>Kalori</th><th>P/K/Y</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($yemekler as $y):
                        $bc = match($y['meal_type']) {
                            'Kahvaltı' => 'badge-kahvalti', 'Öğle' => 'badge-ogle',
                            'Akşam'    => 'badge-aksam',   'Ara Öğün' => 'badge-ara', default => ''
                        }; ?>
                        <tr>
                            <td><?= htmlspecialchars($y['name']) ?></td>
                            <td><span class="meal-badge <?= $bc ?>"><?= htmlspecialchars($y['meal_type']) ?></span></td>
                            <td><?= htmlspecialchars($y['date']) ?></td>
                            <td><?= $y['calories'] ? $y['calories'].' kcal' : '-' ?></td>
                            <td style="font-size:12px;"><?= $y['protein'] ?>g / <?= $y['carb'] ?>g / <?= $y['fat'] ?>g</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center mt-3">Henüz yemek kaydı eklenmemiş.</p>
        <?php endif; ?>
    </div>

    <!-- ANTRENÖR NOTLARI -->
    <div id="notlar">
        <div class="section-title">📝 Antrenör Notları</div>
        <?php if (count($notlar) > 0): ?>
            <?php foreach ($notlar as $n): ?>
                <div class="note-card">
                    <p style="margin:0 0 6px; color:#333;"><?= nl2br(htmlspecialchars($n['note'])) ?></p>
                    <small style="color:#aaa;"><?= date('d.m.Y H:i', strtotime($n['created_at'])) ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted text-center mt-3">Henüz antrenörünüzden not bulunmuyor.</p>
        <?php endif; ?>
    </div>

    <!-- SU TAKİBİ -->
    <div id="su">
        <div class="section-title">💧 Su Takibi</div>
        <div class="water-card">
            <p style="color:#555; margin:0 0 4px;">Bugün içtiğin su</p>
            <div class="su-miktar"><?= number_format($su_toplam) ?> ml</div>
            <small style="color:#888;">Günlük hedef: <?= number_format($su_hedef) ?> ml</small>
            <div class="progress-bar-bg">
                <div class="progress-fill" style="width:<?= $su_yuzde ?>%;"></div>
            </div>
            <small style="color:#555;">%<?= $su_yuzde ?> tamamlandı</small>

            <?php if ($member_logged_in): ?>
                <form method="POST" class="mt-3">
                    <input type="hidden" name="add_water" value="1">
                    <button type="submit" class="btn btn-primary w-100" style="font-size:16px; padding:14px;">💧 200ml İçtim</button>
                </form>
                <p style="color:#aaa; font-size:13px; margin-top:8px;">Her basış = 1 bardak (200ml)</p>
            <?php else: ?>
                <div class="alert alert-warning mt-3 py-2" style="font-size:14px;">
                    Su takibini kullanmak için <a href="sign_in.php">giriş yapın</a>.
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
</body>
</html>
