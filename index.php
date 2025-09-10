<?php
// Main Cashier Application for Toko Tsabita
session_start();

// Include authentication check
require_once 'includes/auth.php';

// Include database configuration
require_once 'includes/db_config.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle adding product to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);
    
    // Fetch product details
    $product = $pdo->getProductById($product_id);
    
    if ($product) {
        // Check if product already in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity'] += $quantity;
                $item['subtotal'] = $item['quantity'] * $item['price'];
                $found = true;
                break;
            }
        }
        
        // If not found, add new item to cart
        if (!$found) {
            $_SESSION['cart'][] = array(
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'subtotal' => $product['price'] * $quantity
            );
        }
    }
}

// Handle removing item from cart
if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        // Re-index array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Handle clearing cart
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = array();
}

// Handle checkout
$transaction_success = false;
$transaction_id = 0;
if (isset($_POST['checkout'])) {
    $total_amount = floatval($_POST['total_amount']);
    $payment_amount = floatval($_POST['payment_amount']);
    $change_amount = $payment_amount - $total_amount;
    
    if ($payment_amount >= $total_amount && count($_SESSION['cart']) > 0) {
        // Insert transaction with cashier name
        $cashier_name = $current_user['username'];
        $transaction_id = $pdo->addTransaction($total_amount, $payment_amount, $change_amount, $_SESSION['cart'], $cashier_name);
        
        // Clear cart
        $_SESSION['cart'] = array();
        $transaction_success = true;
    }
}

// Fetch all products
$products = $pdo->getProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Toko Tsabita</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'nav.php'; ?>
        
        <header>
            <h1>Kasir Toko Tsabita</h1>
            <p>Sistem Kasir untuk Toko Tsabita</p>
            <div class="user-info">
                <p>Login sebagai: <?php echo htmlspecialchars($current_user['username']); ?> (<?php echo htmlspecialchars($current_user['role']); ?>) | 
                <a href="logout.php">Logout</a></p>
            </div>
        </header>
        
        <?php if ($transaction_success): ?>
        <div class="success-message">
            <p>Transaksi berhasil! ID Transaksi: <?php echo $transaction_id; ?></p>
            <a href="receipt.php?id=<?php echo $transaction_id; ?>" target="_blank">Cetak Struk</a>
        </div>
        <?php endif; ?>
        
        <div class="main-content">
            <!-- Product Selection -->
            <div class="product-section">
                <h2>Daftar Produk</h2>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <form method="POST">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                            <p class="stock">Stok: <?php echo $product['stock']; ?></p>
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" required>
                            <button type="submit" name="add_to_cart" <?php echo ($product['stock'] <= 0) ? 'disabled' : ''; ?>>Tambah</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Shopping Cart -->
            <div class="cart-section">
                <h2>Keranjang Belanja</h2>
                <?php if (count($_SESSION['cart']) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $index => $item): 
                            $total += $item['subtotal'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                            <td><a href="?remove=<?php echo $index; ?>" class="remove-btn">Hapus</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong>Total:</strong></td>
                            <td colspan="2"><strong>Rp <?php echo number_format($total, 0, ',', '.'); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
                
                <!-- Checkout Form -->
                <div class="checkout-section">
                    <h3>Proses Pembayaran</h3>
                    <form method="POST">
                        <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
                        <label for="payment_amount">Jumlah Bayar:</label>
                        <input type="number" id="payment_amount" name="payment_amount" min="<?php echo $total; ?>" step="100" required>
                        <button type="submit" name="checkout">Bayar</button>
                        <a href="?clear" class="clear-btn">Batal</a>
                    </form>
                </div>
                <?php else: ?>
                <p>Keranjang belanja kosong.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <footer>
            <p>&copy; 2025 Toko Tsabita. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>