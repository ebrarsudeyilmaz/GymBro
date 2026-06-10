<?php

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$mesaj = ""; // Hata almamak için mesaj değişkenini başta boş tanımlıyoruz

// Form gönderildiğinde veritabanına ekleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $exercise_id = $_POST['exercise_id'];
    $sets = $_POST['sets'];
    $reps = $_POST['reps'];
    $weight = $_POST['weight'];
    $day_of_week = $_POST['day_of_week'];

    $sql = "INSERT INTO training_programs (member_id, exercise_id, sets, reps, weight, day_of_week) 
            VALUES (:member_id, :exercise_id, :sets, :reps, :weight, :day_of_week)";
    $islem = $db->prepare($sql);
    
    try {
        $islem->execute([
            ':member_id' => $member_id,
            ':exercise_id' => $exercise_id,
            ':sets' => $sets,
            ':reps' => $reps,
            ':weight' => $weight,
            ':day_of_week' => $day_of_week
        ]);
        $mesaj = "<p style='color: green; font-weight: bold;'>Program başarıyla eklendi!</p>";
    } catch(PDOException $e) {
        $mesaj = "<p style='color: red; font-weight: bold;'>Hata oluştu: " . $e->getMessage() . "</p>";
    }
}

// Üyeleri çek (Dropdown için)
$uye_sorgu = $db->query("SELECT id, name FROM users WHERE role = 'member'");
$uyeler = $uye_sorgu->fetchAll(PDO::FETCH_ASSOC);

// Hareketleri çek (Dropdown için)
$hareket_sorgu = $db->query("SELECT id, name FROM exercises");
$hareketler = $hareket_sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Antrenman Ekle</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; }
        .form-container { max-width: 500px; margin: auto; padding: 20px; background-color: white; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select { width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background-color: #0056b3; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        .grid-2 { display: flex; gap: 10px; }
        .grid-2 > div { flex: 1; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Antrenman Programı Ekle</h2>
    
    <!-- Başarı/Hata mesajı burada gösterilecek -->
    <?= $mesaj ?> 

    <form action="add_program.php" method="POST">
        <label>Üye Seçin:</label>
        <select name="member_id" required>
            <?php foreach($uyeler as $uye): ?>
                <option value="<?= $uye['id'] ?>"><?= htmlspecialchars($uye['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Hareket Seçin:</label>
        <select name="exercise_id" required>
            <?php foreach($hareketler as $hareket): ?>
                <option value="<?= $hareket['id'] ?>"><?= htmlspecialchars($hareket['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="grid-2">
            <div>
                <label>Set:</label>
                <input type="number" name="sets" required min="1">
            </div>
            <div>
                <label>Tekrar:</label>
                <input type="number" name="reps" required min="1">
            </div>
        </div>

        <label>Ağırlık (Örn: 50kg, Boş Bar, Vücut Ağırlığı):</label>
        <input type="text" name="weight">

        <label>Antrenman Günü:</label>
        <select name="day_of_week" required>
            <option value="Pazartesi">Pazartesi</option>
            <option value="Salı">Salı</option>
            <option value="Çarşamba">Çarşamba</option>
            <option value="Perşembe">Perşembe</option>
            <option value="Cuma">Cuma</option>
            <option value="Cumartesi">Cumartesi</option>
            <option value="Pazar">Pazar</option>
        </select>

        <button type="submit">Programa Ekle</button>
    </form>
</div>

</body>
</html>