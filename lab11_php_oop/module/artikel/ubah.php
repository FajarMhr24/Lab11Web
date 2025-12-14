<?php
$db = new Database();
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<p>ID artikel tidak ditemukan</p>";
    return;
}

$artikel = $db->get('artikel', "id=$id");

if ($_POST) {
    $db->update('artikel', [
        'judul' => $_POST['judul'],
        'isi'   => $_POST['isi']
    ], "id=$id");

    header("Location: /lab11_php_oop/index.php/artikel/index");
    exit;
}
?>

<h3>Ubah Artikel</h3>

<form method="post">
    <label>Judul Artikel</label>
    <input type="text" name="judul" value="<?= $artikel['judul']; ?>" required>

    <label>Isi Artikel</label>
    <textarea name="isi" required><?= $artikel['isi']; ?></textarea>

    <input type="submit" value="Update Artikel">
</form>
