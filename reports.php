<?php
/**
 * Reports Page
 * Generates financial reports with filtering options
 * Shows charts and detailed breakdowns
 */

// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
require_once 'includes/db.php';

$pageTitle = 'Reports';
$userId = $_SESSION['user_id'];

// Get filter parameters
$dateFrom = $_GET['date_from'] ?? date('Y-m-01'); // Default to current month start
$dateTo = $_GET['date_to'] ?? date('Y-m-d'); // Default to today
$categoryFilter = $_GET['category'] ?? '';

// Build WHERE clause for filtering
$whereConditions = ["t.user_id = ?"];
$params = [$userId];

if ($dateFrom) {
    $whereConditions[] = "t.transaction_date >= ?";
    $params[] = $dateFrom;
}

if ($dateTo) {
    $whereConditions[] = "t.transaction_date <= ?";
    $params[] = $dateTo;
}

if ($categoryFilter) {
    $whereConditions[] = "t.category_id = ?";
    $params[] = $categoryFilter;
}

$whereClause = implode(' AND ', $whereConditions);

// Get summary data
$summary = fetchRow("
    SELECT 
        COALESCE(SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE 0 END), 0) as total_income,
        COALESCE(SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END), 0) as total_expense,
        COUNT(CASE WHEN t.type = 'income' THEN 1 END) as income_count,
        COUNT(CASE WHEN t.type = 'expense' THEN 1 END) as expense_count
    FROM transactions t 
    WHERE $whereClause
", $params);

$balance = $summary['total_income'] - $summary['total_expense'];

// Get category breakdown
$categoryBreakdown = fetchAll("
    SELECT 
        c.name as category_name,
        c.type,
        COALESCE(SUM(t.amount), 0) as total_amount,
        COUNT(t.id) as transaction_count
    FROM categories c
    LEFT JOIN transactions t ON c.id = t.category_id AND $whereClause
    WHERE c.user_id IS NULL OR c.user_id = ?
    GROUP BY c.id, c.name, c.type
    HAVING total_amount > 0
    ORDER BY total_amount DESC
", array_merge([$userId], $params));

// Get monthly trend data
$monthlyTrend = fetchAll("
    SELECT 
        DATE_FORMAT(transaction_date, '%Y-%m') as month,
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
    FROM transactions 
    WHERE $whereClause
    GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
    ORDER BY month
", $params);

// Get categories for filter dropdown
$categories = fetchAll("SELECT * FROM categories WHERE user_id IS NULL OR user_id = ? ORDER BY name", [$userId]);

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-chart-bar me-2"></i>Financial Reports</h2>
        <p class="text-muted">Analyze your financial data with detailed reports</p>
    </div>
</div>

<!-- Filter Form -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Report Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="<?php echo htmlspecialchars($dateFrom); ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="<?php echo htmlspecialchars($dateTo); ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo $categoryFilter == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>Generate Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Total Income</h5>
                <h3 class="mb-0">$<?php echo number_format($summary['total_income'], 2); ?></h3>
                <small><?php echo $summary['income_count']; ?> transactions</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Total Expenses</h5>
                <h3 class="mb-0">$<?php echo number_format($summary['total_expense'], 2); ?></h3>
                <small><?php echo $summary['expense_count']; ?> transactions</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card <?php echo $balance >= 0 ? 'bg-primary' : 'bg-warning'; ?> text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Net Balance</h5>
                <h3 class="mb-0">$<?php echo number_format($balance, 2); ?></h3>
                <small><?php echo $balance >= 0 ? 'Surplus' : 'Deficit'; ?></small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Total Transactions</h5>
                <h3 class="mb-0"><?php echo $summary['income_count'] + $summary['expense_count']; ?></h3>
                <small>All time</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Monthly Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Category Breakdown</h5>
            </div>
            <div class="card-body">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Category Breakdown Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Detailed Category Breakdown</h5>
            </div>
            <div class="card-body">
                <?php if (empty($categoryBreakdown)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No data available for the selected period.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Total Amount</th>
                                    <th>Transactions</th>
                                    <th>Average</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalAmount = array_sum(array_column($categoryBreakdown, 'total_amount'));
                                foreach ($categoryBreakdown as $category): 
                                    $percentage = $totalAmount > 0 ? ($category['total_amount'] / $totalAmount) * 100 : 0;
                                    $average = $category['transaction_count'] > 0 ? $category['total_amount'] / $category['transaction_count'] : 0;
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($category['category_name']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $category['type'] == 'income' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo ucfirst($category['type']); ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold">
                                        $<?php echo number_format($category['total_amount'], 2); ?>
                                    </td>
                                    <td>
                                        <?php echo $category['transaction_count']; ?>
                                    </td>
                                    <td>
                                        $<?php echo number_format($average, 2); ?>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar <?php echo $category['type'] == 'income' ? 'bg-success' : 'bg-danger'; ?>" 
                                                 style="width: <?php echo $percentage; ?>%">
                                                <?php echo number_format($percentage, 1); ?>%
                                            </div>
                                        </div>
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

<script>
// Chart data
const monthlyTrendData = <?php echo json_encode($monthlyTrend); ?>;
const categoryBreakdownData = <?php echo json_encode($categoryBreakdown); ?>;

// Monthly Trend Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: monthlyTrendData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        }),
        datasets: [{
            label: 'Income',
            data: monthlyTrendData.map(item => parseFloat(item.income)),
            borderColor: 'rgb(40, 167, 69)',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.1
        }, {
            label: 'Expenses',
            data: monthlyTrendData.map(item => parseFloat(item.expense)),
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

// Category Breakdown Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: categoryBreakdownData.map(item => item.category_name),
        datasets: [{
            data: categoryBreakdownData.map(item => parseFloat(item.total_amount)),
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF',
                '#4BC0C0', '#9966FF', '#FF9F40', '#FF6384'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
