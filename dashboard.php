<?php
/**
 * Dashboard Page
 * Main dashboard showing financial overview
 * Displays total income, expenses, balance, and recent transactions
 */

// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
require_once 'includes/db.php';

$pageTitle = 'Dashboard';
$userId = $_SESSION['user_id'];

// Get financial summary
$totalIncome = fetchRow("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'income'", [$userId])['total'];
$totalExpense = fetchRow("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'expense'", [$userId])['total'];
$balance = $totalIncome - $totalExpense;

// Get recent transactions (last 10)
$recentTransactions = fetchAll("
    SELECT t.*, c.name as category_name 
    FROM transactions t 
    JOIN categories c ON t.category_id = c.id 
    WHERE t.user_id = ? 
    ORDER BY t.transaction_date DESC, t.created_at DESC 
    LIMIT 10
", [$userId]);

// Get monthly data for chart (last 6 months)
$monthlyData = fetchAll("
    SELECT 
        DATE_FORMAT(transaction_date, '%Y-%m') as month,
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
    FROM transactions 
    WHERE user_id = ? 
    AND transaction_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
    ORDER BY month
", [$userId]);

// Get category-wise expense data for pie chart
$categoryData = fetchAll("
    SELECT c.name, SUM(t.amount) as total
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ? AND t.type = 'expense'
    GROUP BY c.id, c.name
    ORDER BY total DESC
    LIMIT 8
", [$userId]);

include 'includes/header.php';
?>

<!-- Welcome Message -->
<div class="row mb-4">
    <div class="col-12">
        <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p class="text-muted">Here's your financial overview</p>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Income</h6>
                        <h3 class="mb-0">$<?php echo number_format($totalIncome, 2); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-arrow-up fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Expenses</h6>
                        <h3 class="mb-0">$<?php echo number_format($totalExpense, 2); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-arrow-down fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card <?php echo $balance >= 0 ? 'bg-primary' : 'bg-warning'; ?> text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Current Balance</h6>
                        <h3 class="mb-0">$<?php echo number_format($balance, 2); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Monthly Income vs Expenses</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Expense Categories</h5>
            </div>
            <div class="card-body">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Transactions</h5>
                <a href="view_transactions.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentTransactions)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No transactions yet. <a href="add_transaction.php">Add your first transaction</a></p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $transaction): ?>
                                <tr>
                                    <td><?php echo date('M j, Y', strtotime($transaction['transaction_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($transaction['category_name']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $transaction['type'] == 'income' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo ucfirst($transaction['type']); ?>
                                        </span>
                                    </td>
                                    <td class="<?php echo $transaction['type'] == 'income' ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo $transaction['type'] == 'income' ? '+' : '-'; ?>$<?php echo number_format($transaction['amount'], 2); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="add_transaction.php" class="btn btn-success w-100">
                            <i class="fas fa-plus me-2"></i>Add Income
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="add_transaction.php?type=expense" class="btn btn-danger w-100">
                            <i class="fas fa-minus me-2"></i>Add Expense
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="view_transactions.php" class="btn btn-info w-100">
                            <i class="fas fa-list me-2"></i>View All
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="reports.php" class="btn btn-warning w-100">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Monthly Chart Data
const monthlyData = <?php echo json_encode($monthlyData); ?>;
const categoryData = <?php echo json_encode($categoryData); ?>;

// Monthly Income vs Expenses Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: monthlyData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        }),
        datasets: [{
            label: 'Income',
            data: monthlyData.map(item => parseFloat(item.income)),
            borderColor: 'rgb(40, 167, 69)',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.1
        }, {
            label: 'Expenses',
            data: monthlyData.map(item => parseFloat(item.expense)),
            borderColor: 'rgb(220, 53, 69)',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Category Pie Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: categoryData.map(item => item.name),
        datasets: [{
            data: categoryData.map(item => parseFloat(item.total)),
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});
</script>

<?php include 'includes/footer.php'; ?>
