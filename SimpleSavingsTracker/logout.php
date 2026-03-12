<?php
require_once 'includes/auth.php';

$_SESSION = array();
session_destroy();

header("Location: login.php");
exit();
?>