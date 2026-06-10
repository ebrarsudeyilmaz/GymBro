<?php
require 'role_control.php';
// GÜVENLİK: Sadece yönetici (admin) yeni üye ekleyebilir
check_access(['admin']);
require 'db.php';

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $trainer_id = !empty($_POST['trainer_id']) ? intval($_POST['trainer_id']) : NULL;
    $token = uniqid('token_'); 
    
    // HOCANIN KRİTERİ: Üye için varsayılan şifreyi (123456) güvenli hash'leyerek ekliyoruz
    $guvenli_sifre = password_hash('123456', PASSWORD_DEFAULT); 

    // Üyeyi antrenör bağlantısıyla birlikte kaydediyoruz
    $sql = "INSERT INTO users (name, role, password, qr_code_token, trainer_id) 
            VALUES (:name, 'member', :password, :token, :trainer_id)";
    $islem = $db->prepare($sql);
    
    try {
        $islem->execute([
            ':name' => $name, 
            ':password' => $guvenli_sifre, 
            ':token' => $token,
            ':trainer_id' => $trainer_id
        ]);
        header("Location: list_member.php");
        exit;
    } catch(PDOException $e) {
        $mesaj = "<p style='color: red; font-weight: bold;'>Hata oluştu: " . $e->getMessage() . "</p>";
    }
}

// Antrenörleri çekiyoruz (Açılır menü için)
$antrenor_sorgu = $db->query("SELECT id, name FROM users WHERE role = 'trainer' ORDER BY name");
$antrenorler = $antrenor_sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Üye Ekle</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; }
        .container { max-width: 400px; margin: 50px auto; background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #218838; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h2 style="margin-top:0;">Yeni Üye Kaydı (Yönetici Paneli)</h2>
    <?= $mesaj ?>
    <form action="add_member.php" method="POST">
        <label>Üye Adı Soyadı:</label>
        <input type="text" name="name" required placeholder="Örn: Ahmet Yılmaz">
        
        <label>Özel Antrenör Atayın:</label>
        <select name="trainer_id" required>
            <option value="" disabled selected>-- Antrenör Seçin --</option>
            <?php foreach($antrenorler as $ant): ?>
                <option value="<?= $ant['id'] ?>"><?= htmlspecialchars($ant['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Üyeyi Sisteme Ekle</button>
    </form>
</div>

</body>
</html>