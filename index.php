<?php
  $pdo = new PDO('sqlite:chinook.db');
  $sql = '
    SELECT
      InvoiceId,
      InvoiceDate,
      Total,
      customers.FirstName as CustomerFirstName,
      customers.LastName as CustomerLastName,
      customers.Email as CustomerEmail,
      employees.FirstName as EmployeeFirstName,
      employees.LastName as EmployeeLastName
    FROM invoices
    INNER JOIN customers
    ON invoices.CustomerId = customers.CustomerId
    INNER JOIN employees
    ON customers.SupportRepId = employees.EmployeeId
  ';

  if (isset($_GET['search'])) {
    $sql = $sql . ' WHERE customers.FirstName LIKE ?';
    $sql = $sql . ' OR customers.LastName LIKE ?';
  }

  $sql = $sql . ' ORDER BY InvoiceDate desc';
  $statement = $pdo->prepare($sql);

  if (isset($_GET['search'])) {
    $boundSearchParam = '%' . $_GET['search'] . '%';
    $statement->bindParam(1, $boundSearchParam);
    $statement->bindParam(2, $boundSearchParam);
  }

  $statement->execute();
  $invoices = $statement->fetchAll(PDO::FETCH_OBJ);
?>
<?php include('header.php') ?>

<h1 class="mt-3 mb-3">Invoices</h1>

<form action="index.php" method="get" class="mb-3">
  <div class="form-group">
    <input
      type="text"
      name="search"
      class="form-control"
      placeholder="Search..."
      value="<?php echo isset($_GET['search']) ? $_GET['search'] : '' ?>">
  </div>

  <button type="submit" class="btn btn-primary">
    Search
  </button>

  <a href="/" class="btn btn-default">
    Clear
  </a>
</form>

<table class="table">
  <tr>
    <th>Date</th>
    <th>Total</th>
    <th>Name</th>
    <th>Email</th>
    <th colspan="2">
      Employee
    </th>
  </tr>
  <?php foreach($invoices as $invoice) : ?>
    <tr>
      <td><?php echo $invoice->InvoiceDate ?></td>
      <td>$<?php echo $invoice->Total ?></td>
      <td>
        <?php echo $invoice->CustomerFirstName . ' ' . $invoice->CustomerLastName ?>
      </td>
      <td><?php echo $invoice->CustomerEmail ?></td>
      <td>
        <?php echo $invoice->EmployeeFirstName . ' ' . $invoice->EmployeeLastName ?>
      </td>
      <td>
        <a href="invoice-details.php?invoice=<?php echo $invoice->InvoiceId ?>">Details</a>
      </td>
    </tr>
  <?php endforeach ?>

  <?php if (count($invoices) === 0) : ?>
    <tr>
      <td colspan="5">No results</td>
    </tr>
  <?php endif ?>
</table>

<?php include('footer.php') ?>