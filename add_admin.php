<?php
require 'db.php';

try {
    // Çakışma olmaması için her seferinde benzersiz bir token üretiyoruz
    $benzersiz_token = 'admin_token_' . time(); 
    
    $sql = "INSERT INTO users (name, role, password, qr_code_token) 
            VALUES ('Admin', 'admin', 'admin123', '$benzersiz_token')";
    
    $db->exec($sql);
    
    echo "<h2 style='color:green; text-align:center; margin-top:50px;'>
          Harika! Admin hesabı veritabanına başarıyla eklendi. <br> 
          Şimdi login.php adresine gidip giriş yapabilirsin!
          </h2>";
          
} catch(PDOException $e) {
    echo "<h2 style='color:red; text-align:center; margin-top:50px;'>
          Bir hata oluştu: " . $e->getMessage() . "
          </h2>";
}
?>