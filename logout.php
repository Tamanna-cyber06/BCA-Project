<?php
// ============================================
// GlamCart — Logout
// logout.php
// ============================================

session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to homepage with message
// We start a new session to carry the flash message
session_start();
$_SESSION['msg']      = 'You have been logged out. See you soon! 👋';
$_SESSION['msg_type'] = 'info';

header("Location: index.php");
exit;
?>
