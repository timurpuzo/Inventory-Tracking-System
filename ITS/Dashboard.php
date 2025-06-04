<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: Login.html");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inventory Dashboard</title>
  <header class="dashboard-header">
    <div class="user-info">
      <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
      <p>Your role: <?= htmlspecialchars($_SESSION['role']) ?></p>
    </div>
    <a href="main.html" class="logout-button" style="background-color: dodgerblue">Home Page</a>
    <a href="logout.php" class="logout-button">Logout</a>
  </header>

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 20px;
    }
    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #34495e;
      color: white;
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .dashboard-header .user-info h2 {
      margin: 0;
      font-size: 22px;
    }

    .dashboard-header .user-info p {
      margin: 2px 0 0;
      font-size: 14px;
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
    .logout-button {
      display: inline-block;
      padding: 8px 16px;
      background-color: #e74c3c;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      font-weight: bold;
      transition: background-color 0.2s ease;
    }

    .logout-button:hover {
      background-color: #c0392b;
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

    form {
      margin-bottom: 20px;
    }
    form input {
      padding: 8px;
      margin-right: 10px;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    form button {
      padding: 8px 12px;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    form button:hover {
      background-color: #2980b9;
    }

    .delete-button {
      color: red;
      background: none;
      border: none;
      cursor: pointer;

      .dashboard-footer {
        margin-top: 40px;
        padding: 15px 20px;
        background-color: #343a40;
        text-align: center;
        font-size: 14px;
        color: #555;
        border-radius: 8px;
      }

    }
  </style>
</head>
<body>

<?php
$conn = new mysqli("localhost", "root", "Malek20167", "inventorytrackingsystem");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle Add User

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
  $username = $conn->real_escape_string($_POST['username']);
  $email = $conn->real_escape_string($_POST['email']);
  $password = $conn->real_escape_string($_POST['password']);
  $role = 'Employee';

  $stmt = $conn->prepare("INSERT INTO Users (username, email, password, role) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss",  $username, $email, $password, $role);

  if ($stmt->execute()) {
    echo "<p style='color: green;'>User successfully added.</p>";
  } else {
    echo "<p style='color: red;'>Error adding user: " . htmlspecialchars($stmt->error) . "</p>";
  }

  $stmt->close();
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $deleteId = (int) $_POST['delete_id'];
  $conn->query("DELETE FROM Products WHERE product_id = $deleteId");
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
  $name = $conn->real_escape_string($_POST['name']);
  $type = $conn->real_escape_string($_POST['type']);
  $size = $conn->real_escape_string($_POST['size']);
  $location = $conn->real_escape_string($_POST['location']);
  $quantity = (int) $_POST['quantity'];

  $conn->query("INSERT INTO Products (name, type, size, location, quantity)
                VALUES ('$name', '$type', '$size', '$location', $quantity)");
}

// Summary
$summarySql = "
SELECT
  (SELECT COUNT(*) FROM Products) AS total_products,
  (SELECT COUNT(*) FROM Products WHERE quantity BETWEEN 1 AND 5) AS low_stock,
  (SELECT COUNT(*) FROM Products WHERE quantity = 0) AS out_of_stock,
  (SELECT MAX(quantity) FROM Products) AS most_stock_quantity
";
$summaryResult = $conn->query($summarySql)->fetch_assoc();
?>

<!-- Dashboard Summary -->
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
  <div class="card green">
    <h2><?= $summaryResult['most_stock_quantity'] ?></h2>
    <p>Most Stock Quantity</p>
  </div>
</div>

<!-- Add Product Form -->
<div class="section-title">Add New Product</div>
<form method="POST">
  <input type="text" name="name" placeholder="Name" required />
  <input type="text" name="type" placeholder="Type" required />
  <input type="text" name="size" placeholder="Size" required />
  <input type="text" name="location" placeholder="Location" required />
  <input type="number" name="quantity" placeholder="Quantity" required />
  <button type="submit" name="add_product">Add Product</button>
</form>

<!-- Search Form -->
<div class="section-title">Search For Product</div>
<form method="GET">
  <input type="text" name="name" placeholder="Product Name" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>" />
  <input type="text" name="type" placeholder="Type" value="<?= htmlspecialchars($_GET['type'] ?? '') ?>" />
  <input type="text" name="location" placeholder="Location" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>" />
  <button type="submit">Search</button>
</form>

<!-- Top Products Table -->
<?php
$name = $conn->real_escape_string($_GET['name'] ?? '');
$type = $conn->real_escape_string($_GET['type'] ?? '');
$location = $conn->real_escape_string($_GET['location'] ?? '');

$whereClauses = [];
if ($name !== '') $whereClauses[] = "name LIKE '%$name%'";
if ($type !== '') $whereClauses[] = "type LIKE '%$type%'";
if ($location !== '') $whereClauses[] = "location LIKE '%$location%'";

$whereSql = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$sql = "SELECT * FROM Products $whereSql ORDER BY quantity DESC LIMIT 10";
$topProducts = $conn->query($sql);
?>

<div>
  <div class="section-title">Top 10 Products by Stock</div>
  <table>
    <thead>
    <tr>
      <th>Product ID</th>
      <th>Name</th>
      <th>Type</th>
      <th>Size</th>
      <th>Location</th>
      <th>Quantity</th>
      <th>Actions</th>
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
        <td>
          <form method="POST" onsubmit="return confirm('Delete this product?');">
            <input type="hidden" name="delete_id" value="<?= $p['product_id'] ?>" />
            <button type="submit" class="delete-button">Delete</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Low Stock Table -->
<?php
$lowStock = $conn->query("SELECT * FROM Products WHERE quantity BETWEEN 0 AND 5 ORDER BY quantity ASC");
?>

<div>
  <div class="section-title">Low Stock Products (Quantity 0–5)</div>
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
    <?php while ($p = $lowStock->fetch_assoc()): ?>
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

<!-- Add User Form -->
<div>
  <div class="section-title">Add New User</div>
  <form method="POST">
    <input type="text" name="username" placeholder="Username" required />
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit" name="add_user">Add User</button>
  </form>
</div>


<!-- Messages Section -->
<?php
$messagesResult = $conn->query("SELECT * FROM Emails ORDER BY created_at DESC");
?>

<div>
  <div class="section-title">Messages from Users</div>
  <table>
    <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Message</th>
      <th>Sent At</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($msg = $messagesResult->fetch_assoc()): ?>
      <tr>
        <td><?= $msg['id'] ?></td>
        <td><?= htmlspecialchars($msg['name']) ?></td>
        <td><?= htmlspecialchars($msg['email']) ?></td>
        <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
        <td><?= $msg['created_at'] ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<footer class="dashboard-footer">
  &copy; <?= date("Y") ?> Inventory Tracking System. All rights reserved.
</footer>


<?php $conn->close(); ?>
</body>
</html>
