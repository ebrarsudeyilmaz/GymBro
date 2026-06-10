<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require 'db.php';

// Sadece üyeleri (role = member) çekiyoruz
$sql = "SELECT id, name, qr_code_token FROM users WHERE role = 'member' ORDER BY id DESC";
$sorgu = $db->query($sql);
$uyeler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

// Dinamik olarak projenin ana dizinini buluyoruz (localhost/spor_salonu)
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/spor_salonu";
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Üye Listesi</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 0; margin: 0; background-color: #f9f9f9; }
        .container { max-width: 900px; margin: 20px auto; background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #343a40; color: #ffc107; }
        tr:hover { background-color: #f1f1f1; }
        
        /* Buton Tasarımları */
        .btn-copy { padding: 6px 12px; background-color: #17a2b8; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-copy:hover { background-color: #138496; }
        .btn-qr { padding: 6px 12px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; margin-left: 5px; display: inline-block; }
        .btn-qr:hover { background-color: #5a6268; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Kayıtlı Üyeler ve Erişim Linkleri</h2>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Üye Adı</th>
                <th>Token (Gizli Kod)</th>
                <th>Erişim Paylaşımı</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($uyeler) > 0): ?>
                <?php foreach ($uyeler as $uye): ?>
                    <?php 
                        // Her üye için özel linki oluşturuyoruz
                        $uye_link = $base_url . "/member_view.php?token=" . $uye['qr_code_token']; 
                        // QR Code API'si için linki formatlıyoruz
                        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($uye_link);
                    ?>
                    <tr>
                        <td><?= $uye['id'] ?></td>
                        <td><strong><?= htmlspecialchars($uye['name']) ?></strong></td>
                        <td><span style="background:#eee; padding:3px 6px; border-radius:3px; font-size:12px;"><?= htmlspecialchars($uye['qr_code_token']) ?></span></td>
                        <td>
                            <!-- JavaScript ile Kopyalama Butonu -->
                            <button class="btn-copy" onclick="linkKopyala('<?= $uye_link ?>')">📋 Linki Kopyala</button>
                            
                            <!-- QR Kodu Yeni Sekmede Açma Butonu -->
                            <a href="<?= $qr_url ?>" target="_blank" class="btn-qr">📱 QR Göster</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align: center;">Henüz sisteme kayıtlı bir üye bulunmuyor.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pano kopyalama işlemi için gerekli JavaScript fonksiyonu -->
<script>
    function linkKopyala(link) {
        navigator.clipboard.writeText(link).then(function() {
            alert("Üyenin linki başarıyla kopyalandı!\nWhatsApp vb. bir yere yapıştırabilirsiniz.\n\n" + link);
        }, function(err) {
            alert("Link kopyalanırken hata oluştu: ", err);
        });
    }
</script>

</body>
</html>