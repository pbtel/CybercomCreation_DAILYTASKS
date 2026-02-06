<?php
// Start session
session_start();

// Set session variable
$_SESSION['username'] = 'InternName';

echo "Session is set for username: " . $_SESSION['username'];
echo "<br><a href='welcome.php'>Go to Welcome Page</a>";

?>
