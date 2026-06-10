<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// GÜVENLİK KİLİDİ: Eğer kullanıcının şifre değiştirme zorunluluğu varsa başka sayfaya geçişini engelle!
if (isset($_SESSION['must_change_password']) && $_SESSION['must_change_password'] === true) {
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($current_page !== 'change_password.php' && $current_page !== 'logout.php') {
        header("Location: change_password.php");
        exit;
    }
}

/**
 * Sayfalara rol bazlı erişim yetkisini kontrol eden fonksiyon
 */
function check_access($allowed_roles) {
    if (!isset($_SESSION['role'])) {
        header("Location: login.php");
        exit;
    }

    if (!in_array($_SESSION['role'], $allowed_roles)) {
        die("<h2 style='color:red; text-align:center; margin-top:50px;'>⚠️ Yetkisiz Erişim: Bu sayfayı görüntüleme izniniz bulunmamaktadır!</h2>");
    }
}
?>