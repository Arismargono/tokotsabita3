<?php
// Receipt generation for Toko Tsabita
session_start();

// Include authentication check
require_once 'includes/auth.php';

require_once 'includes/db_config.php';

// Get transaction ID from URL
$transaction_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($transaction_id > 0) {
    // Fetch transaction details
    $transaction = $pdo->getTransactionById($transaction_id);
    
    if ($transaction) {
        // Fetch transaction items
        $items = $pdo->getTransactionItems($transaction_id);
    } else {
        die("Transaction not found.");
    }
} else {
    die("Invalid transaction ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian - Toko Tsabita</title>
    <link rel="stylesheet" href="css/receipt.css">
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>Toko Tsabita</h1>
            <p>Jl. Manunggal RT 03 RW 10 Kadipiro Banjarsari Surakarta</p>
            <p>Telp. 085866005699</p>
        </div>
        
        <div class="receipt-info">
            <p><strong>ID Transaksi:</strong> <?php echo $transaction['id']; ?></p>
            <p><strong>Tanggal:</strong> <?php echo date('d/m/Y H:i:s', strtotime($transaction['created_at'])); ?></p>
            <p><strong>Kasir:</strong> <?php echo isset($transaction['cashier_name']) ? htmlspecialchars($transaction['cashier_name']) : 'Unknown'; ?></p>
        </div>
        
        <table class="receipt-items">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="receipt-total">
            <p><strong>Total:</strong> Rp <?php echo number_format($transaction['total_amount'], 0, ',', '.'); ?></p>
            <p><strong>Bayar:</strong> Rp <?php echo number_format($transaction['payment_amount'], 0, ',', '.'); ?></p>
            <p><strong>Kembalian:</strong> Rp <?php echo number_format($transaction['change_amount'], 0, ',', '.'); ?></p>
        </div>
        
        <div class="receipt-footer">
            <p>Terima kasih telah berbelanja di Toko Tsabita</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
            <button onclick="window.print()">Cetak Struk</button>
            <div style="margin-top: 10px;">
                <a href="index.php">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>