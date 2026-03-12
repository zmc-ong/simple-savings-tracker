<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
redirectIfNotLoggedIn();

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$goal_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("DELETE FROM savings_goals WHERE id = ? AND user_id = ?");
$stmt->execute([$goal_id, $user_id]);

header("Location: dashboard.php");
exit();
?>