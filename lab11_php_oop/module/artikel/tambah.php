<?php
$form = new Form("", "Simpan Artikel");

if ($_POST) {
    $db = new Database();
    $db->insert('artikel', [
        'judul' => $_POST['judul'],
        'isi'   => $_POST['isi']
    ]);

    header("Location: /lab11_php_oop/index.php/artikel/index");
    exit;
}
?>

<h3>Tambah Artikel</h3>

<?php
$form->addField("judul", "Judul Artikel");
$form->addField("isi", "Isi Artikel", "textarea");

$form->displayForm();
?>
