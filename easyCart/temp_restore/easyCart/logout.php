<?php
require_once 'includes/session.php';

logoutUser();
setFlashMessage('success', 'You have been logged out successfully.');
header('Location: index.php');
exit;
?>
