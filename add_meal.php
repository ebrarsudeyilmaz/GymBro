<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $name      = trim($_POST['name']);
    $gram      = $_POST['gram'];
    $calories  = $_POST['calories'];
    $protein   = $_POST['protein'];
    $carb      = $_POST['carb'];
    $fat       = $_POST['fat'];
    $meal_type = $_POST['meal_type'];
    $date      = $_POST['date'];

    $sql = "INSERT INTO foods (member_id, name, gram, calories, protein, carb, fat, meal_type, date) 
            VALUES (:member_id, :name, :gram, :calories, :protein, :carb, :fat, :meal_type, :date)";
    $islem = $db->prepare($sql);

    try {
        $islem->execute([
            ':member_id' => $member_id,
            ':name'      => $name,
            ':gram'      => $gram,
            ':calories'  => $calories,
            ':protein'   => $protein,
            ':carb'      => $carb,
            ':fat'       => $fat,
            ':meal_type' => $meal_type,
            ':date'      => $date
        ]);
        $mesaj = "<p style='color: green; font-weight: bold;'>Yemek başarıyla eklendi!</p>";
    } catch(PDOException $e) {
        $mesaj = "<p style='color: red; font-weight: bold;'>Hata oluştu: " . $e->getMessage() . "</p>";
    }
}

// Üyeleri çek (dropdown için)
$uye_sorgu = $db->query("SELECT id, name FROM users WHERE role = 'member' ORDER BY name");
$uyeler = $uye_sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yemek Ekle</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; background-color: #f9f9f9; }
        .form-container { max-width: 500px; margin: 30px auto; padding: 20px; background-color: white; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input, select { width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background-color: #218838; }
        .grid-3 { display: flex; gap: 10px; }
        .grid-3 > div { flex: 1; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="form-container">
    <h2>Yemek Ekle</h2>

    <?= $mesaj ?>

    <form action="add_meal.php" method="POST">

        <label>Üye Seçin:</label>
        <select name="member_id" required>
            <option value="" disabled selected>-- Üye seçin --</option>
            <?php foreach($uyeler as $uye): ?>
                <option value="<?= $uye['id'] ?>"><?= htmlspecialchars($uye['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Yemek Adı:</label>
        <input type="text" name="name" placeholder="Örn: Tavuk Göğsü" required>

        <label>Öğün:</label>
        <select name="meal_type" required>
            <option value="" disabled selected>-- Öğün seçin --</option>
            <option value="Kahvaltı">Kahvaltı</option>
            <option value="Öğle">Öğle</option>
            <option value="Akşam">Akşam</option>
            <option value="Ara Öğün">Ara Öğün</option>
        </select>

        <label>Tarih:</label>
        <input type="date" name="date" required>

        <label>Gram:</label>
        <input type="number" name="gram" placeholder="Örn: 150" min="1">

        <label>Besin Değerleri:</label>
        <div class="grid-3">
            <div>
                <label>Kalori:</label>
                <input type="number" name="calories" placeholder="kcal" min="0">
            </div>
            <div>
                <label>Protein (g):</label>
                <input type="number" name="protein" step="0.01" placeholder="g" min="0">
            </div>
            <div>
                <label>Karb (g):</label>
                <input type="number" name="carb" step="0.01" placeholder="g" min="0">
            </div>
        </div>

        <label>Yağ (g):</label>
        <input type="number" name="fat" step="0.01" placeholder="g" min="0">

        <button type="submit">Yemeği Kaydet</button>
    </form>
</div>

</body>
</html>