<?php
// Product management for Toko Tsabita
session_start();

// Include authentication check
require_once 'includes/auth.php';

// Only admin users can access this page
if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

require_once 'includes/db_config.php';

// Handle form submissions
$message = '';

// Add new product
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    
    try {
        $id = $pdo->addProduct($name, $price, $stock);
        $message = "Produk berhasil ditambahkan!";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Update product
if (isset($_POST['update_product'])) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    
    try {
        $result = $pdo->updateProduct($id, $name, $price, $stock);
        if ($result) {
            $message = "Produk berhasil diperbarui!";
        } else {
            $message = "Gagal memperbarui produk.";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Delete product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    try {
        $result = $pdo->deleteProduct($id);
        if ($result) {
            $message = "Produk berhasil dihapus!";
        } else {
            $message = "Gagal menghapus produk.";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch all products
$products = $pdo->getProducts();

// Fetch product for editing (if requested)
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_product = $pdo->getProductById($id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - Toko Tsabita</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'nav.php'; ?>
        
        <header>
            <h1>Manajemen Produk</h1>
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
                <h2><?php echo $edit_product ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h2>
                <form method="POST">
                    <?php if ($edit_product): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Nama Produk:</label>
                        <input type="text" id="name" name="name" value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Harga (Rp):</label>
                        <input type="number" id="price" name="price" value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" min="0" step="100" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stok:</label>
                        <input type="number" id="stock" name="stock" value="<?php echo $edit_product ? $edit_product['stock'] : ''; ?>" min="0" required>
                    </div>
                    
                    <button type="submit" name="<?php echo $edit_product ? 'update_product' : 'add_product'; ?>">
                        <?php echo $edit_product ? 'Update Produk' : 'Tambah Produk'; ?>
                    </button>
                    
                    <?php if ($edit_product): ?>
                        <a href="products.php" class="clear-btn">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="cart-section">
                <h2>Daftar Produk</h2>
                <?php if (count($products) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            <td>
                                <a href="?edit=<?php echo $product['id']; ?>">Edit</a> | 
                                <a href="?delete=<?php echo $product['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>Belum ada produk.</p>
                <?php endif; ?>
                
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