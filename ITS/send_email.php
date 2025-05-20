<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $to = "maleknafaa4@gmail.com";
  $name = htmlspecialchars(trim($_POST["name"]));
  $email = htmlspecialchars(trim($_POST["email"]));
  $subject = htmlspecialchars(trim($_POST["subject"]));
  $message = htmlspecialchars(trim($_POST["message"]));

  $email_subject = !empty($subject) ? $subject : "New Contact Form Message";
  $email_body = "You received a new message from the contact form:\n\n"
    . "Name: $name\n"
    . "Email: $email\n"
    . "Subject: $subject\n"
    . "Message:\n$message";

  $headers = "From: $email\r\n";
  $headers .= "Reply-To: $email\r\n";

  if (mail($to, $email_subject, $email_body, $headers)) {
    echo "<script>alert('Message sent successfully.'); window.location.href = 'contact.html';</script>";
  } else {
    echo "<script>alert('Message failed to send. Please try again later.'); window.history.back();</script>";
  }
} else {
  header("Location: contact.html");
  exit();
}
