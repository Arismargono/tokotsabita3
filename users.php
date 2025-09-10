<?php
// User management for Toko Tsabita
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
$error = '';

// Add new user
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        $id = $pdo->addUser($username, $password, $role);
        if ($id) {
            $message = "User berhasil ditambahkan!";
        } else {
            $error = "Username sudah digunakan!";
        }
    }
}

// Update user
if (isset($_POST['update_user'])) {
    $id = intval($_POST['id']);
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // If password is empty, don't update it
    $result = $pdo->updateUser($id, $username, $password ?: null, $role);
    if ($result) {
        $message = "User berhasil diperbarui!";
    } else {
        $error = "Gagal memperbarui user. Username mungkin sudah digunakan!";
    }
}

// Delete user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Prevent deletion of own account
    if ($id == $current_user['id']) {
        $error = "Anda tidak bisa menghapus akun sendiri!";
    } else {
        $result = $pdo->deleteUser($id);
        if ($result) {
            $message = "User berhasil dihapus!";
        } else {
            $error = "Gagal menghapus user. Tidak dapat menghapus admin terakhir!";
        }
    }
}

// Fetch all users
$users = $pdo->getUsers();

// Fetch user for editing (if requested)
$edit_user = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_user = $pdo->getUserById($id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Toko Tsabita</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'nav.php'; ?>
        
        <header>
            <h1>Manajemen User</h1>
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
        
        <?php if ($error): ?>
        <div class="error-message">
            <p><?php echo $error; ?></p>
        </div>
        <?php endif; ?>
        
        <div class="main-content">
            <div class="product-section">
                <h2><?php echo $edit_user ? 'Edit User' : 'Tambah User Baru'; ?></h2>
                <form method="POST">
                    <?php if ($edit_user): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_user['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="<?php echo $edit_user ? htmlspecialchars($edit_user['username']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" <?php echo $edit_user ? '' : 'required'; ?>>
                        <?php if ($edit_user): ?>
                            <small>Kosongkan jika tidak ingin mengganti password</small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select id="role" name="role" required>
                            <option value="cashier" <?php echo ($edit_user && $edit_user['role'] == 'cashier') ? 'selected' : ''; ?>>Cashier</option>
                            <option value="admin" <?php echo ($edit_user && $edit_user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="<?php echo $edit_user ? 'update_user' : 'add_user'; ?>">
                        <?php echo $edit_user ? 'Update User' : 'Tambah User'; ?>
                    </button>
                    
                    <?php if ($edit_user): ?>
                        <a href="users.php" class="clear-btn">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="cart-section">
                <h2>Daftar User</h2>
                <?php if (count($users) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo ucfirst($user['role']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="?edit=<?php echo $user['id']; ?>">Edit</a> 
                                <?php if ($user['id'] != $current_user['id']): ?>
                                | <a href="?delete=<?php echo $user['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">Hapus</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>Belum ada user.</p>
                <?php endif; ?>
                
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