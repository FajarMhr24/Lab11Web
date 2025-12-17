<?php
$db = new Database();
$message = "";

if ($_POST) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $username = $_SESSION['username'];

    $db->query("UPDATE users SET password='$password' WHERE username='$username'");
    $message = "Password berhasil diubah";
}
?>

<h3>Profil User</h3>

<p><b>Nama:</b> <?= $_SESSION['nama'] ?></p>
<p><b>Username:</b> <?= $_SESSION['username'] ?></p>

<?php if ($message): ?>
<p style="color:green"><?= $message ?></p>
<?php endif; ?>

<form method="post">
    <label>Password Baru</label><br>
    <input type="password" name="password" required><br><br>
    <button type="submit">Ubah Password</button>
</form>
