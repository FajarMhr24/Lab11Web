<?php
$db = new Database();
$data = $db->query("SELECT * FROM artikel");
?>

<h3>Daftar Artikel</h3>
<a href="/lab11_php_oop/module/artikel/tambah.php" class="action-link">+ Tambah Artikel</a>

<table>
    <tr>
        <th>Judul</th>
        <th>Isi Artikel</th>
    </tr>

    <?php  while ($row = $data->fetch_assoc()): ?>
    <tr>
        <td><?= $row['judul']; ?></td>
        <td><?= $row['isi']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>
