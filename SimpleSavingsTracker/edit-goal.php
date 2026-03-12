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

// Verify goal belongs to user
$stmt = $pdo->prepare("SELECT * FROM savings_goals WHERE id = ? AND user_id = ?");
$stmt->execute([$goal_id, $user_id]);
$goal = $stmt->fetch();

if (!$goal) {
    header("Location: dashboard.php");
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    $goal_name = trim($_POST['goal_name']);
    $target_amount = (float)$_POST['target_amount'];
    $current_amount = (float)$_POST['current_amount'];
    $deadline = $_POST['deadline'] ?: null;

    if (strlen($goal_name) < 2) {
        $error = "Goal name must be at least 2 characters";
    } elseif ($target_amount <= 0) {
        $error = "Target amount must be positive";
    } elseif ($current_amount < 0) {
        $error = "Current amount cannot be negative";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE savings_goals 
                SET goal_name = ?, target_amount = ?, current_amount = ?, deadline = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $goal_name,
                $target_amount,
                $current_amount,
                $deadline,
                $goal_id
            ]);
            header("Location: dashboard.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error updating goal: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Goal - Savings Tracker</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>Edit Goal</h1>
            <nav>
                <a href="dashboard.php" class="btn">Back to Dashboard</a>
            </nav>
        </header>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="goal-form">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="form-group">
                <label for="goal_name">Goal Name</label>
                <input type="text" id="goal_name" name="goal_name" 
                       value="<?= htmlspecialchars($goal['goal_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="target_amount">Target Amount ($)</label>
                <input type="number" id="target_amount" name="target_amount" 
                       step="0.01" min="0.01" 
                       value="<?= $goal['target_amount'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="current_amount">Current Amount ($)</label>
                <input type="number" id="current_amount" name="current_amount" 
                       step="0.01" min="0" 
                       value="<?= $goal['current_amount'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="deadline">Deadline (optional)</label>
                <input type="date" id="deadline" name="deadline" 
                       value="<?= $goal['deadline'] ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Update Goal</button>
        </form>
    </div>
</body>
</html>