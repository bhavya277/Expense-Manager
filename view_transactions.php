<?php
/**
 * View Transactions Page
 * Displays all user transactions in a table format
 * Includes filtering, pagination, and edit/delete options
 */

// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
require_once 'includes/db.php';

$pageTitle = 'View Transactions';
$userId = $_SESSION['user_id'];

// Get filter parameters
$typeFilter = $_GET['type'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Build WHERE clause for filtering
$whereConditions = ["t.user_id = ?"];
$params = [$userId];

if ($typeFilter) {
    $whereConditions[] = "t.type = ?";
    $params[] = $typeFilter;
}

if ($categoryFilter) {
    $whereConditions[] = "t.category_id = ?";
    $params[] = $categoryFilter;
}

if ($dateFrom) {
    $whereConditions[] = "t.transaction_date >= ?";
    $params[] = $dateFrom;
}

if ($dateTo) {
    $whereConditions[] = "t.transaction_date <= ?";
    $params[] = $dateTo;
}

$whereClause = implode(' AND ', $whereConditions);

// Get total count for pagination
$totalCount = fetchRow("
    SELECT COUNT(*) as count 
    FROM transactions t 
    WHERE $whereClause
", $params)['count'];

$totalPages = ceil($totalCount / $limit);

// Get transactions with pagination
$transactions = fetchAll("
    SELECT t.*, c.name as category_name 
    FROM transactions t 
    JOIN categories c ON t.category_id = c.id 
    WHERE $whereClause
    ORDER BY t.transaction_date DESC, t.created_at DESC 
    LIMIT $limit OFFSET $offset
", $params);

// Get categories for filter dropdown
$categories = fetchAll("SELECT * FROM categories WHERE user_id IS NULL OR user_id = ? ORDER BY name", [$userId]);

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-list me-2"></i>All Transactions</h4>
                <a href="add_transaction.php" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Transaction
                </a>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                <option value="income" <?php echo $typeFilter == 'income' ? 'selected' : ''; ?>>Income</option>
                                <option value="expense" <?php echo $typeFilter == 'expense' ? 'selected' : ''; ?>>Expense</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
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
                        
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="<?php echo htmlspecialchars($dateFrom); ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="<?php echo htmlspecialchars($dateTo); ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <a href="view_transactions.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Transactions Table -->
                <?php if (empty($transactions)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No transactions found.</p>
                        <a href="add_transaction.php" class="btn btn-primary">Add Your First Transaction</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
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
                                    <td class="<?php echo $transaction['type'] == 'income' ? 'text-success' : 'text-danger'; ?> fw-bold">
                                        <?php echo $transaction['type'] == 'income' ? '+' : '-'; ?>$<?php echo number_format($transaction['amount'], 2); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit_transaction.php?id=<?php echo $transaction['id']; ?>" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete_transaction.php?id=<?php echo $transaction['id']; ?>" 
                                               class="btn btn-outline-danger" title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this transaction?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Transaction pagination">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
