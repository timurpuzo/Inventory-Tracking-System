<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inventory Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 20px;
    }
    .summary {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 30px;
    }
    .card {
      flex: 1 1 150px;
      background-color: #fff;
      border-left: 8px solid;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      border-radius: 8px;
    }
    .card h2 {
      margin: 0 0 10px;
      font-size: 24px;
    }
    .card p {
      margin: 0;
      font-size: 18px;
    }
    .red { border-color: #e74c3c; }
    .pink { border-color: #f39c12; }
    .purple { border-color: #8e44ad; }
    .orange { border-color: #e67e22; }
    .green { border-color: #27ae60; }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }
    th {
      background-color: #f1f1f1;
    }
    .section-title {
      margin-bottom: 10px;
      font-size: 20px;
      font-weight: bold;
    }
  </style>
</head>
<body>

<?php

$conn = new mysqli("localhost", "root", "", "inventorytrackingsystem");
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

// Summary queries
$summarySql = "
SELECT
(SELECT COUNT(*) FROM Products) AS total_products,
(SELECT COUNT(*) FROM Products WHERE quantity BETWEEN 1 AND 5) AS low_stock,
(SELECT COUNT(*) FROM Products WHERE quantity = 0) AS out_of_stock,
(SELECT COUNT(*) FROM Products WHERE quantity = 0) AS zero_stock,
(SELECT MAX(quantity) FROM Products) AS most_stock_quantity
";
$summaryResult = $conn->query($summarySql)->fetch_assoc();
?>

<div class="summary">
  <div class="card red">
    <h2><?= $summaryResult['total_products'] ?></h2>
    <p>Total Products</p>
  </div>
  <div class="card pink">
    <h2><?= $summaryResult['low_stock'] ?></h2>
    <p>Low Stock Products</p>
  </div>
  <div class="card purple">
    <h2><?= $summaryResult['out_of_stock'] ?></h2>
    <p>Out of Stock Products</p>
  </div>
  <div class="card orange">
    <h2><?= $summaryResult['zero_stock'] ?></h2>
    <p>Zero Stock Products</p>
  </div>
  <div class="card green">
    <h2><?= $summaryResult['most_stock_quantity'] ?></h2>
    <p>Most Stock Quantity</p>
  </div>
</div>

<!-- Placeholder for Recent Invoices -->
<div>
  <div class="section-title">Recent Purchase Invoice</div>
  <p>Invoices table not yet implemented in the database.</p>
</div>

<!-- Top 5 Products by Quantity -->
<?php
$topProducts = $conn->query("SELECT * FROM Products ORDER BY quantity DESC LIMIT 5");
?>
<div>
  <div class="section-title">Top 5 Products by Stock</div>
  <table>
    <thead>
    <tr>
      <th>Product ID</th>
      <th>Name</th>
      <th>Type</th>
      <th>Size</th>
      <th>Location</th>
      <th>Quantity</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($p = $topProducts->fetch_assoc()): ?>
    <tr>
      <td><?= $p['product_id'] ?></td>
      <td><?= $p['name'] ?></td>
      <td><?= $p['type'] ?></td>
      <td><?= $p['size'] ?></td>
      <td><?= $p['location'] ?></td>
      <td><?= $p['quantity'] ?></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php $conn->close(); ?>
</body>
</html>
