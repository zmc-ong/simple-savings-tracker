<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
redirectIfNotLoggedIn();

$categories = ['Food', 'Transport', 'Housing', 'Utilities', 'Entertainment', 'Healthcare', 'Education', 'Shopping', 'Other'];

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    $amount = (float)$_POST['amount'];
    $category = $_POST['category'];
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['date'];

    if ($amount <= 0) {
        $error = "Amount must be positive";
    } elseif (!in_array($category, $categories)) {
        $error = "Invalid category";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO expenses 
                (user_id, amount, category, description, date) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $amount,
                $category,
                $description,
                $date
            ]);
            header("Location: dashboard.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error saving expense: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense - Savings Tracker</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>Add New Expense</h1>
            <nav>
                <a href="dashboard.php" class="btn">Back to Dashboard</a>
            </nav>
        </header>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="expense-form">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="form-group">
                <label for="amount">Amount ($)</label>
                <input type="number" id="amount" name="amount" step="0.01" min="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description (optional)</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Save Expense</button>
        </form>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>