<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

// URL'den gelen id ile mevcut kaydı çek
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM foods WHERE id = :id";
    $sorgu = $db->prepare($sql);
    $sorgu->execute([':id' => $id]);
    $yemek = $sorgu->fetch(PDO::FETCH_ASSOC);

    if (!$yemek) {
        header("Location: list_meal.php");
        exit;
    }
} else {
    header("Location: list_meal.php");
    exit;
}

// Üyeleri çek (dropdown için)
$uye_sorgu = $db->query("SELECT id, name FROM users WHERE role = 'member' ORDER BY name");
$uyeler = $uye_sorgu->fetchAll(PDO::FETCH_ASSOC);

// Form gönderildiğinde güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id        = $_POST['id'];
    $member_id = $_POST['member_id'];
    $name      = trim($_POST['name']);
    $gram      = $_POST['gram'];
    $calories  = $_POST['calories'];
    $protein   = $_POST['protein'];
    $carb      = $_POST['carb'];
    $fat       = $_POST['fat'];
    $meal_type = $_POST['meal_type'];
    $date      = $_POST['date'];

    $update_sql = "UPDATE foods SET 
                    member_id = :member_id,
                    name      = :name,
                    gram      = :gram,
                    calories  = :calories,
                    protein   = :protein,
                    carb      = :carb,
                    fat       = :fat,
                    meal_type = :meal_type,
                    date      = :date
                   WHERE id = :id";
    $islem = $db->prepare($update_sql);

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
            ':date'      => $date,
            ':id'        => $id
        ]);
        header("Location: list_meal.php");
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
    <title>Yemek Düzenle</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; background-color: #f9f9f9; }
        .form-container { max-width: 500px; margin: 30px auto; padding: 20px; background-color: white; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input, select { width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background-color: #0056b3; }
        .grid-3 { display: flex; gap: 10px; }
        .grid-3 > div { flex: 1; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="form-container">
    <h2>Yemek Düzenle</h2>

    <form action="edit_meal.php?id=<?= $yemek['id'] ?>" method="POST">
        <input type="hidden" name="id" value="<?= $yemek['id'] ?>">

        <label>Üye Seçin:</label>
        <select name="member_id" required>
            <?php foreach($uyeler as $uye): ?>
                <option value="<?= $uye['id'] ?>" <?= $yemek['member_id'] == $uye['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($uye['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Yemek Adı:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($yemek['name']) ?>" required>

        <label>Öğün:</label>
        <select name="meal_type" required>
            <option value="Kahvaltı"  <?= $yemek['meal_type'] == 'Kahvaltı'  ? 'selected' : '' ?>>Kahvaltı</option>
            <option value="Öğle"      <?= $yemek['meal_type'] == 'Öğle'      ? 'selected' : '' ?>>Öğle</option>
            <option value="Akşam"     <?= $yemek['meal_type'] == 'Akşam'     ? 'selected' : '' ?>>Akşam</option>
            <option value="Ara Öğün"  <?= $yemek['meal_type'] == 'Ara Öğün'  ? 'selected' : '' ?>>Ara Öğün</option>
        </select>

        <label>Tarih:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($yemek['date']) ?>" required>

        <label>Gram:</label>
        <input type="number" name="gram" value="<?= $yemek['gram'] ?>" min="1">

        <label>Besin Değerleri:</label>
        <div class="grid-3">
            <div>
                <label>Kalori:</label>
                <input type="number" name="calories" value="<?= $yemek['calories'] ?>" min="0">
            </div>
            <div>
                <label>Protein (g):</label>
                <input type="number" name="protein" step="0.01" value="<?= $yemek['protein'] ?>" min="0">
            </div>
            <div>
                <label>Karb (g):</label>
                <input type="number" name="carb" step="0.01" value="<?= $yemek['carb'] ?>" min="0">
            </div>
        </div>

        <label>Yağ (g):</label>
        <input type="number" name="fat" step="0.01" value="<?= $yemek['fat'] ?>" min="0">

        <button type="submit">Değişiklikleri Kaydet</button>
        <a href="list_meal.php" class="btn-cancel">İptal Et ve Geri Dön</a>
    </form>
</div>

</body>
</html>