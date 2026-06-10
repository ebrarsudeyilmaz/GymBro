<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .navbar { 
        background-color: #343a40; 
        padding: 15px 30px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        flex-wrap: wrap; 
        gap: 10px; }

    .navbar-brand { 
        font-size: 24px; 
        font-weight: bold; 
        text-decoration: none; 
        color: #ffc107; 
        letter-spacing: 1px; }

    .nav-links { 
        list-style: none; 
        margin: 0; 
        padding: 0; 
        display: flex; 
        flex-wrap: wrap; 
        gap: 5px; 
        align-items: center; }

    .nav-links li a { 
        text-decoration: none; 
        color: #ffffff; 
        font-size: 14px; 
        font-weight: 500; 
        padding: 7px 10px; 
        border-radius: 4px; 
        display: block; 
        transition: background-color 0.3s; }

    .nav-links li a:hover { 
        background-color: #495057; 
        color: #ffc107; }

    .nav-divider { 
        width: 1px; 
        background-color: #495057; 
        margin: 0 5px; 
        align-self: stretch; 
        min-height: 20px; }

    .role-badge { 
        font-size: 12px; 
        color: #ffc107;
        border: 1px solid #ffc107; 
        padding: 3px 8px; 
        border-radius: 10px; 
        margin-right: 10px; }
        
</style>

<div class="navbar">
    <a href="list_program.php" class="navbar-brand">GYM BRO</a>

    <ul class="nav-links">

        <?php if (isset($_SESSION['role'])): ?>
            <span class="role-badge">
                <?= $_SESSION['role'] === 'admin' ? '👑 Yönetici' : '🏋️ Antrenör' ?>:
                <?= htmlspecialchars($_SESSION['admin_name']) ?>
            </span>
        <?php endif; ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <!-- Sadece Admin Görür -->
            <li><a href="add_member.php">+ Üye Ekle</a></li>
            <li><a href="list_member.php">Üyeler</a></li>
            <div class="nav-divider"></div>
            <li><a href="add_trainer.php">+ Antrenör Ekle</a></li>
            <li><a href="list_trainers.php">Antrenörler</a></li>
            <div class="nav-divider"></div>
        <?php endif; ?>

        <!-- Admin ve Trainer Görür -->
        <li><a href="add_program.php">+ Antrenman Ata</a></li>
        <li><a href="list_program.php">Programlar</a></li>
        <li><a href="add_exercise.php">+ Hareket Ekle</a></li>
        <li><a href="list_exercise.php">Hareketler</a></li>

        <div class="nav-divider"></div>

        <li><a href="add_meal.php">+ Yemek Ekle</a></li>
        <li><a href="list_meal.php">Yemekler</a></li>
        <li><a href="list_water.php">Su Takibi</a></li>

        <div class="nav-divider"></div>

        <li><a href="add_note.php">+ Not Ekle</a></li>
        <li><a href="list_notes.php">Notlar</a></li>

        <div class="nav-divider"></div>

        <li><a href="logout.php" style="background-color: #dc3545; padding: 7px 12px; border-radius: 4px;">Çıkış Yap</a></li>
    </ul>
</div>