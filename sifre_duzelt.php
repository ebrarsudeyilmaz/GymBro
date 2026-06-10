<?php
require 'db.php';

try {
    // admin123 şifresini login.php'nin anlayacağı formata (kriptolu) çeviriyoruz
    $kriptolu_sifre = password_hash('admin123', PASSWORD_DEFAULT);
    $benzersiz_token = 'admin_token_' . time(); 
    
    // Çakışma olmaması için önceki düz metin şifreli hatalı adminleri siliyoruz
    $db->exec("DELETE FROM users WHERE name = 'Admin'");
    
    // Yeni, şifresi kriptolanmış mükemmel admini ekliyoruz
    $sql = "INSERT INTO users (name, role, password, qr_code_token) 
            VALUES ('Admin', 'admin', '$kriptolu_sifre', '$benzersiz_token')";
    
    $db->exec($sql);
    
    echo "<h2 style='color:green; text-align:center; margin-top:50px;'>
          Kriptolu Admin Başarıyla Eklendi! <br> 
          Artık login.php sayfasına gidip giriş yapabilirsin.
          </h2>";
          
} catch(PDOException $e) {
    echo "Bir hata oluştu: " . $e->getMessage();
}
?>