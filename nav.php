<?php
// Determine current page
$current_page = basename($_SERVER['PHP_SELF']);

// Include auth to check user role
require_once 'includes/auth.php';
?>

<nav class="main-nav">
    <ul>
        <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Kasir</a></li>
        <?php if (isAdmin()): ?>
        <li><a href="products.php" class="<?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">Produk</a></li>
        <li><a href="users.php" class="<?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">User</a></li>
        <li><a href="sales_report.php" class="<?php echo ($current_page == 'sales_report.php') ? 'active' : ''; ?>">Laporan</a></li>
        <?php endif; ?>
        <li><a href="transactions.php" class="<?php echo ($current_page == 'transactions.php') ? 'active' : ''; ?>">Transaksi</a></li>
    </ul>
</nav>