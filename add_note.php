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
    $note      = trim($_POST['note']);

    $sql = "INSERT INTO trainer_notes (member_id, note) VALUES (:member_id, :note)";
    $islem = $db->prepare($sql);

    try {
        $islem->execute([
            ':member_id' => $member_id,
            ':note'      => $note
        ]);
        $mesaj = "<p style='color: green; font-weight: bold;'>Not başarıyla eklendi!</p>";
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
    <title>Antrenör Notu Ekle</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; background-color: #f9f9f9; }
        .form-container { max-width: 500px; margin: 30px auto; padding: 20px; background-color: white; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        select, textarea { width: 100%; margin-bottom: 15px; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        textarea { resize: vertical; min-height: 120px; font-family: Arial, sans-serif; font-size: 14px; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        button:hover { background-color: #218838; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="form-container">
    <h2>Antrenör Notu Ekle</h2>

    <?= $mesaj ?>

    <form action="add_note.php" method="POST">

        <label>Üye Seçin:</label>
        <select name="member_id" required>
            <option value="" disabled selected>-- Üye seçin --</option>
            <?php foreach($uyeler as $uye): ?>
                <option value="<?= $uye['id'] ?>"><?= htmlspecialchars($uye['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Not:</label>
        <textarea name="note" placeholder="Üye hakkında notunuzu buraya yazın..." required></textarea>

        <button type="submit">Notu Kaydet</button>
    </form>
</div>

</body>
</html>