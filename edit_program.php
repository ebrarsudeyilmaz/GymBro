<?php

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

// 1. URL'den gelen id'yi yakala ve mevcut programı veritabanından çek
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM training_programs WHERE id = :id";
    $sorgu = $db->prepare($sql);
    $sorgu->execute([':id' => $id]);
    $program = $sorgu->fetch(PDO::FETCH_ASSOC);

    // Eğer böyle bir id yoksa listeye geri yolla
    if (!$program) {
        header("Location: list_program.php");
        exit;
    }
} else {
    header("Location: list_program.php");
    exit;
}

// Üyeleri çek (Dropdown için)
$uye_sorgu = $db->query("SELECT id, name FROM users WHERE role = 'member'");
$uyeler = $uye_sorgu->fetchAll(PDO::FETCH_ASSOC);

// Hareketleri çek (Dropdown için)
$hareket_sorgu = $db->query("SELECT id, name FROM exercises");
$hareketler = $hareket_sorgu->fetchAll(PDO::FETCH_ASSOC);

// 2. Form gönderildiğinde (Kaydet'e basıldığında) güncelleme işlemini yap
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $member_id = $_POST['member_id'];
    $exercise_id = $_POST['exercise_id'];
    $sets = $_POST['sets'];
    $reps = $_POST['reps'];
    $weight = $_POST['weight'];
    $day_of_week = $_POST['day_of_week'];

    // UPDATE komutu ile eski verinin üstüne yazıyoruz
    $update_sql = "UPDATE training_programs SET 
                    member_id = :member_id, 
                    exercise_id = :exercise_id, 
                    sets = :sets, 
                    reps = :reps, 
                    weight = :weight, 
                    day_of_week = :day_of_week 
                   WHERE id = :id";
    $islem = $db->prepare($update_sql);

    try {
        $islem->execute([
            ':member_id' => $member_id,
            ':exercise_id' => $exercise_id,
            ':sets' => $sets,
            ':reps' => $reps,
            ':weight' => $weight,
            ':day_of_week' => $day_of_week,
            ':id' => $id
        ]);
        // Başarılıysa listeye yönlendir
        header("Location: list_program.php");
        exit;
    } catch(PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Program Düzenle</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; }
        .form-container { max-width: 500px; margin: auto; padding: 20px; background-color: white; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select { width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background-color: #0056b3; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        .grid-2 { display: flex; gap: 10px; }
        .grid-2 > div { flex: 1; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Antrenman Programı Düzenle</h2>
    
    <form action="edit_program.php?id=<?= $program['id'] ?>" method="POST">
        <!-- ID'yi gizli olarak gönderiyoruz -->
        <input type="hidden" name="id" value="<?= $program['id'] ?>">

        <label>Üye Seçin:</label>
        <select name="member_id" required>
            <?php foreach($uyeler as $uye): ?>
                <!-- Eğer döngüdeki üye id'si, programın member_id'sine eşitse o seçeneği 'selected' yapıyoruz -->
                <option value="<?= $uye['id'] ?>" <?= $program['member_id'] == $uye['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($uye['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Hareket Seçin:</label>
        <select name="exercise_id" required>
            <?php foreach($hareketler as $hareket): ?>
                <option value="<?= $hareket['id'] ?>" <?= $program['exercise_id'] == $hareket['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($hareket['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="grid-2">
            <div>
                <label>Set:</label>
                <input type="number" name="sets" value="<?= $program['sets'] ?>" required min="1">
            </div>
            <div>
                <label>Tekrar:</label>
                <input type="number" name="reps" value="<?= $program['reps'] ?>" required min="1">
            </div>
        </div>

        <label>Ağırlık (Örn: 50kg, Boş Bar, Vücut Ağırlığı):</label>
        <input type="text" name="weight" value="<?= htmlspecialchars($program['weight']) ?>">

        <label>Antrenman Günü:</label>
        <select name="day_of_week" required>
            <option value="Pazartesi" <?= $program['day_of_week'] == 'Pazartesi' ? 'selected' : '' ?>>Pazartesi</option>
            <option value="Salı" <?= $program['day_of_week'] == 'Salı' ? 'selected' : '' ?>>Salı</option>
            <option value="Çarşamba" <?= $program['day_of_week'] == 'Çarşamba' ? 'selected' : '' ?>>Çarşamba</option>
            <option value="Perşembe" <?= $program['day_of_week'] == 'Perşembe' ? 'selected' : '' ?>>Perşembe</option>
            <option value="Cuma" <?= $program['day_of_week'] == 'Cuma' ? 'selected' : '' ?>>Cuma</option>
            <option value="Cumartesi" <?= $program['day_of_week'] == 'Cumartesi' ? 'selected' : '' ?>>Cumartesi</option>
            <option value="Pazar" <?= $program['day_of_week'] == 'Pazar' ? 'selected' : '' ?>>Pazar</option>
        </select>

        <button type="submit">Değişiklikleri Kaydet</button>
        <a href="list_program.php" class="btn-cancel">İptal Et ve Geri Dön</a>
    </form>
</div>

</body>
</html>