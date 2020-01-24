<?php
  if (!isset($_GET['invoice'])) {
    header('Location: index.php');
    exit();
  }

  $pdo = new PDO('sqlite:chinook.db');
  $statement = $pdo->prepare('
    SELECT artists.Name as ArtistName, tracks.Name as TrackName, Quantity, invoice_items.UnitPrice
    FROM invoice_items
    INNER JOIN tracks
    ON invoice_items.TrackId = tracks.TrackId
    INNER JOIN albums
    ON tracks.AlbumId = albums.AlbumId
    INNER JOIN artists
    ON artists.ArtistId = albums.ArtistId
    WHERE InvoiceId = ?
  ');
  $statement->bindParam(1, $_GET['invoice']);
  $statement->execute();
  $invoiceItems = $statement->fetchAll(PDO::FETCH_OBJ);
?>

<?php include('header.php') ?>

<h1 class="mt-3 mb-3">
  Invoice Details: #<?php echo $_GET['invoice'] ?>
</h1>

<table class="table">
  <tr>
    <th>Track</th>
    <th>Quantity</th>
    <th>Price</th>
  </tr>
  <?php foreach($invoiceItems as $invoiceItem) : ?>
    <tr>
      <td>
        <?php echo $invoiceItem->TrackName ?>
        by <?php echo $invoiceItem->ArtistName ?>
      </td>
      <td><?php echo $invoiceItem->Quantity ?></td>
      <td>$<?php echo $invoiceItem->UnitPrice ?></td>
    </tr>
  <?php endforeach ?>
</table>

<?php include('footer.php') ?>
