<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

// URL'den gelen id ile mevcut notu çek
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM trainer_notes WHERE id = :id";
    $sorgu = $db->prepare($sql);
    $sorgu->execute([':id' => $id]);
    $not = $sorgu->fetch(PDO::FETCH_ASSOC);

    if (!$not) {
        header("Location: list_notes.php");
        exit;
    }
} else {
    header("Location: list_notes.php");
    exit;
}

// Üyeleri çek (dropdown için)
$uye_sorgu = $db->query("SELECT id, name FROM users WHERE role = 'member' ORDER BY name");
$uyeler = $uye_sorgu->fetchAll(PDO::FETCH_ASSOC);

// Form gönderildiğinde güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id        = $_POST['id'];
    $member_id = $_POST['member_id'];
    $note      = trim($_POST['note']);

    $update_sql = "UPDATE trainer_notes SET member_id = :member_id, note = :note WHERE id = :id";
    $islem = $db->prepare($update_sql);

    try {
        $islem->execute([
            ':member_id' => $member_id,
            ':note'      => $note,
            ':id'        => $id
        ]);
        header("Location: list_notes.php");
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
    <title>Not Düzenle</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; background-color: #f9f9f9; }
        .form-container { max-width: 500px; margin: 30px auto; padding: 20px; background-color: white; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        select, textarea { width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        textarea { resize: vertical; min-height: 120px; font-family: Arial, sans-serif; font-size: 14px; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background-color: #0056b3; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="form-container">
    <h2>Not Düzenle</h2>

    <form action="edit_note.php?id=<?= $not['id'] ?>" method="POST">
        <input type="hidden" name="id" value="<?= $not['id'] ?>">

        <label>Üye Seçin:</label>
        <select name="member_id" required>
            <?php foreach($uyeler as $uye): ?>
                <option value="<?= $uye['id'] ?>" <?= $not['member_id'] == $uye['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($uye['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Not:</label>
        <textarea name="note" required><?= htmlspecialchars($not['note']) ?></textarea>

        <button type="submit">Değişiklikleri Kaydet</button>
        <a href="list_notes.php" class="btn-cancel">İptal Et ve Geri Dön</a>
    </form>
</div>

</body>
</html>