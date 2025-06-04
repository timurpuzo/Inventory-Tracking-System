<?php
global $pdo;
require 'db.php'; // connects using your existing db.php config

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize and validate inputs
  $name = htmlspecialchars(trim($_POST['name']));
  $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
  $subject = htmlspecialchars(trim($_POST['subject']));
  $message = htmlspecialchars(trim($_POST['message']));

  if (!$name || !$email || !$message) {
    die('Please fill out all required fields correctly.');
  }

  try {
    $stmt = $pdo->prepare("INSERT INTO Emails (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
    $stmt->execute([
      ':name' => $name,
      ':email' => $email,
      ':subject' => $subject,
      ':message' => $message
    ]);

    echo "Thank you! Your message has been received.";
    // Optional: Redirect to a success page
    // header("Location: thank_you.html");
  } catch (PDOException $e) {
    die("Failed to send message: " . $e->getMessage());
  }
} else {
  die("Invalid request.");
}
?>
