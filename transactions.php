<?php
// Transaction history for Toko Tsabita
session_start();

// Include authentication check
require_once 'includes/auth.php';

require_once 'includes/db_config.php';

// Handle delete transaction request (admin only)
$message = '';
if (isset($_GET['delete']) && isAdmin()) {
    $transaction_id = intval($_GET['delete']);
    if ($pdo->deleteTransaction($transaction_id)) {
        $message = "Transaksi berhasil dihapus!";
    } else {
        $message = "Gagal menghapus transaksi.";
    }
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Fetch transactions with pagination
$transactions = $pdo->getTransactions($limit, $offset);
$total_transactions = $pdo->getTransactionCount();
$total_pages = ceil($total_transactions / $limit);

// Get total sales
$total_sales = $pdo->getTotalSales();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Toko Tsabita</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'nav.php'; ?>
        
        <header>
            <h1>Riwayat Transaksi</h1>
            <p>Toko Tsabita</p>
            <div class="user-info">
                <p>Login sebagai: <?php echo htmlspecialchars($current_user['username']); ?> (<?php echo htmlspecialchars($current_user['role']); ?>) | 
                <a href="logout.php">Logout</a></p>
            </div>
        </header>
        
        <?php if ($message): ?>
        <div class="success-message">
            <p><?php echo $message; ?></p>
        </div>
        <?php endif; ?>
        
        <div class="main-content">
            <div class="product-section">
                <h2>Daftar Transaksi</h2>
                <?php if (count($transactions) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
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
                            <td>Rp <?php echo number_format($transaction['total_amount'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($transaction['payment_amount'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($transaction['change_amount'], 0, ',', '.'); ?></td>
                            <td>
                                <a href="receipt.php?id=<?php echo $transaction['id']; ?>" target="_blank">Lihat Struk</a>
                                <?php if (isAdmin()): ?>
                                | <a href="?delete=<?php echo $transaction['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>">&laquo; Sebelumnya</a>
                    <?php endif; ?>
                    
                    <span>Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?></span>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>">Selanjutnya &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                <p>Belum ada transaksi.</p>
                <?php endif; ?>
            </div>
            
            <div class="cart-section">
                <h2>Ringkasan</h2>
                <p><strong>Total Transaksi:</strong> <?php echo $total_transactions; ?></p>
                <p><strong>Total Penjualan:</strong> Rp <?php echo number_format($total_sales, 0, ',', '.'); ?></p>
                
                <div style="margin-top: 20px;">
                    <a href="index.php" class="clear-btn">Kembali ke Kasir</a>
                </div>
            </div>
        </div>
        
        <footer>
            <p>&copy; 2025 Toko Tsabita. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>