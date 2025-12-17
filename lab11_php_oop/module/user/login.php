<?php
// kalau sudah login, langsung ke artikel
if (isset($_SESSION['is_login'])) {
    header("Location: /lab11_php_oop/artikel/index");
    exit;
}

$message = "";

if ($_POST) {
    $db = new Database();

    $username = $_POST['username'];
    $password = $_POST['password'];

    // cari user
    $sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $result = $db->query($sql);
    $user = $result->fetch_assoc();

    // cek password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['is_login'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama']     = $user['nama'];

        header("Location: /lab11_php_oop/artikel/index");
        exit;
    } else {
        $message = "Username atau password salah!";
    }
}
?>

<h3>Login User</h3>

<?php if ($message): ?>
    <p style="color:red"><?= $message ?></p>
<?php endif; ?>

<form method="post">
    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>
