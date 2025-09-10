<?php
// Sales report for Toko Tsabita
session_start();

// Include authentication check
require_once 'includes/auth.php';

// Only admin users can access this page
if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

require_once 'includes/db_config.php';

// Get all cashier names
$cashier_names = $pdo->getCashierNames();

// Get selected cashier from GET parameter
$selected_cashier = isset($_GET['cashier']) ? $_GET['cashier'] : '';

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Fetch transactions based on filter
if (!empty($selected_cashier)) {
    $transactions = $pdo->getTransactionsByCashier($selected_cashier, $limit, $offset);
    $total_transactions = $pdo->getTransactionCountByCashier($selected_cashier);
    $total_sales = $pdo->getTotalSalesByCashier($selected_cashier);
} else {
    $transactions = $pdo->getTransactions($limit, $offset);
    $total_transactions = $pdo->getTransactionCount();
    $total_sales = $pdo->getTotalSales();
}

$total_pages = ceil($total_transactions / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Toko Tsabita</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'nav.php'; ?>
        
        <header>
            <h1>Laporan Penjualan</h1>
            <p>Toko Tsabita</p>
            <div class="user-info">
                <p>Login sebagai: <?php echo htmlspecialchars($current_user['username']); ?> (<?php echo htmlspecialchars($current_user['role']); ?>) | 
                <a href="logout.php">Logout</a></p>
            </div>
        </header>
        
        <div class="main-content">
            <div class="product-section">
                <h2>Filter Laporan</h2>
                <form method="GET">
                    <div class="form-group">
                        <label for="cashier">Pilih Kasir:</label>
                        <select id="cashier" name="cashier">
                            <option value="">Semua Kasir</option>
                            <?php foreach ($cashier_names as $cashier): ?>
                                <option value="<?php echo htmlspecialchars($cashier); ?>" <?php echo ($selected_cashier == $cashier) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cashier); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit">Tampilkan</button>
                    <a href="sales_report.php" class="clear-btn">Reset</a>
                </form>
                
                <h2 style="margin-top: 30px;">Daftar Transaksi</h2>
                <?php if (count($transactions) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Kembalian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo $transaction['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($transaction['cashier_name']); ?></td>
                            <td>Rp <?php echo number_format($transaction['total_amount'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($transaction['payment_amount'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($transaction['change_amount'], 0, ',', '.'); ?></td>
                            <td>
                                <a href="receipt.php?id=<?php echo $transaction['id']; ?>" target="_blank">Lihat Struk</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo !empty($selected_cashier) ? '&cashier=' . urlencode($selected_cashier) : ''; ?>">&laquo; Sebelumnya</a>
                    <?php endif; ?>
                    
                    <span>Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?></span>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo !empty($selected_cashier) ? '&cashier=' . urlencode($selected_cashier) : ''; ?>">Selanjutnya &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                <p>Belum ada transaksi.</p>
                <?php endif; ?>
            </div>
            
            <div class="cart-section">
                <h2>Ringkasan</h2>
                <?php if (!empty($selected_cashier)): ?>
                    <p><strong>Kasir:</strong> <?php echo htmlspecialchars($selected_cashier); ?></p>
                <?php else: ?>
                    <p><strong>Semua Kasir</strong></p>
                <?php endif; ?>
                <p><strong>Total Transaksi:</strong> <?php echo $total_transactions; ?></p>
                <p><strong>Total Penjualan:</strong> Rp <?php echo number_format($total_sales, 0, ',', '.'); ?></p>
                
                <div style="margin-top: 20px;">
                    <a href="index.php" class="clear-btn">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
        
        <footer>
            <p>&copy; 2025 Toko Tsabita. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>