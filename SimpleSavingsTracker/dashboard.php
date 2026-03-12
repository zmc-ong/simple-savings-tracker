<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

$goals_stmt = $pdo->prepare("SELECT * FROM savings_goals WHERE user_id = ?");
$goals_stmt->execute([$user_id]);
$goals = $goals_stmt->fetchAll();

$expenses_stmt = $pdo->prepare("
    SELECT * FROM expenses 
    WHERE user_id = ? 
    ORDER BY date DESC 
    LIMIT 5
");
$expenses_stmt->execute([$user_id]);
$expenses = $expenses_stmt->fetchAll();

$total_saved = $pdo->prepare("SELECT SUM(current_amount) FROM savings_goals WHERE user_id = ?");
$total_saved->execute([$user_id]);
$saved_amount = $total_saved->fetchColumn() ?? 0;

$monthly_expenses = $pdo->prepare("
    SELECT SUM(amount) 
    FROM expenses 
    WHERE user_id = ? 
    AND MONTH(date) = MONTH(CURRENT_DATE())
    AND YEAR(date) = YEAR(CURRENT_DATE())
");
$monthly_expenses->execute([$user_id]);
$expenses_amount = $monthly_expenses->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Savings Tracker</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>Savings Tracker</h1>
            <nav>
                <a href="add-goal.php" class="btn">Add Goal</a>
                <a href="add-expense.php" class="btn">Add Expense</a>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </nav>
        </header>

        <div class="summary-cards">
            <div class="card">
                <h3>Total Saved</h3>
                <p class="amount">$<?= number_format($saved_amount, 2) ?></p>
            </div>
            <div class="card">
                <h3>Monthly Expenses</h3>
                <p class="amount">$<?= number_format($expenses_amount, 2) ?></p>
            </div>
        </div>

        <section class="goals-section">
            <h2>Your Savings Goals</h2>
            <?php if (empty($goals)): ?>
                <p>No goals yet. <a href="add-goal.php">Add your first goal</a></p>
            <?php else: ?>
                <div class="goals-grid">
                    <?php foreach ($goals as $goal): ?>
                        <div class="goal-card">
                            <h3><?= htmlspecialchars($goal['goal_name']) ?></h3>
                            <div class="progress-container">
                                <progress 
                                    value="<?= $goal['current_amount'] ?>" 
                                    max="<?= $goal['target_amount'] ?>">
                                </progress>
                                <span class="progress-text">
                                    $<?= number_format($goal['current_amount'], 2) ?> of 
                                    $<?= number_format($goal['target_amount'], 2) ?>
                                </span>
                            </div>
                            <div class="goal-meta">
                                <span>Deadline: <?= $goal['deadline'] ? date('M j, Y', strtotime($goal['deadline'])) : 'None' ?></span>
                            </div>
                            <div class="goal-actions">
                                <a href="edit-goal.php?id=<?= $goal['id'] ?>" class="btn btn-edit">Edit</a>
                                <a href="delete-goal.php?id=<?= $goal['id'] ?>" 
                                   class="btn btn-delete"
                                   onclick="return confirm('Delete this goal?')">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="recent-expenses">
            <h2>Recent Expenses</h2>
            <?php if (empty($expenses)): ?>
                <p>No expenses recorded yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?= date('M j, Y', strtotime($expense['date'])) ?></td>
                                <td><?= htmlspecialchars($expense['category']) ?></td>
                                <td>$<?= number_format($expense['amount'], 2) ?></td>
                                <td><?= htmlspecialchars($expense['description'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="add-expense.php" class="btn">Add New Expense</a>
            <?php endif; ?>
        </section>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>