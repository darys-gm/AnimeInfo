<?php
require_once 'config/auth.php';

$auth = new Auth();
$auth->logout();

// Redirect ke halaman login
header('Location: auth.php');
exit();
?>