<?php
/**
 * Edit Transaction Page
 * Allows users to edit existing transactions
 * Includes form validation and pre-populated data
 */

// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
require_once 'includes/db.php';

$pageTitle = 'Edit Transaction';
$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Get transaction ID
$transactionId = intval($_GET['id'] ?? 0);

if (!$transactionId) {
    header('Location: view_transactions.php');
    exit();
}

// Get transaction details
$transaction = fetchRow("
    SELECT t.*, c.name as category_name 
    FROM transactions t 
    JOIN categories c ON t.category_id = c.id 
    WHERE t.id = ? AND t.user_id = ?
", [$transactionId, $userId]);

if (!$transaction) {
    header('Location: view_transactions.php');
    exit();
}

// Get categories for the form
$categories = fetchAll("SELECT * FROM categories WHERE user_id IS NULL OR user_id = ? ORDER BY type, name", [$userId]);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    $type = $_POST['type'];
    $categoryId = intval($_POST['category_id']);
    $description = trim($_POST['description']);
    $transactionDate = $_POST['transaction_date'];
    
    // Validation
    if ($amount <= 0) {
        $error = 'Amount must be greater than 0.';
    } elseif (empty($description)) {
        $error = 'Description is required.';
    } elseif (empty($transactionDate)) {
        $error = 'Transaction date is required.';
    } else {
        // Update transaction
        $result = executeQuery(
            "UPDATE transactions SET amount = ?, type = ?, category_id = ?, description = ?, transaction_date = ? WHERE id = ? AND user_id = ?",
            [$amount, $type, $categoryId, $description, $transactionDate, $transactionId, $userId]
        );
        
        if ($result) {
            $success = 'Transaction updated successfully!';
            // Refresh transaction data
            $transaction = fetchRow("
                SELECT t.*, c.name as category_name 
                FROM transactions t 
                JOIN categories c ON t.category_id = c.id 
                WHERE t.id = ? AND t.user_id = ?
            ", [$transactionId, $userId]);
        } else {
            $error = 'Failed to update transaction. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4><i class="fas fa-edit me-2"></i>Edit Transaction</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-1"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-1"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Transaction Type</label>
                            <select class="form-select" id="type" name="type" required onchange="updateCategories()">
                                <option value="expense" <?php echo $transaction['type'] == 'expense' ? 'selected' : ''; ?>>Expense</option>
                                <option value="income" <?php echo $transaction['type'] == 'income' ? 'selected' : ''; ?>>Income</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       step="0.01" min="0.01" value="<?php echo htmlspecialchars($transaction['amount']); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select a category</option>
                                <?php 
                                // Generate all categories with data-type attributes for JavaScript filtering
                                foreach ($categories as $category): 
                                ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        data-type="<?php echo $category['type']; ?>"
                                        <?php echo $category['id'] == $transaction['category_id'] ? 'selected' : ''; ?>
                                        <?php echo ($category['type'] == $transaction['type']) ? '' : 'style="display:none"'; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php 
                                endforeach; 
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="transaction_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="transaction_date" name="transaction_date" 
                                   value="<?php echo $transaction['transaction_date']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Enter transaction description..." required><?php echo htmlspecialchars($transaction['description']); ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Update Transaction
                        </button>
                        <a href="view_transactions.php" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <a href="delete_transaction.php?id=<?php echo $transaction['id']; ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Are you sure you want to delete this transaction?')">
                            <i class="fas fa-trash me-1"></i>Delete
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Update categories based on selected type
function updateCategories() {
    const type = document.getElementById('type').value;
    const categorySelect = document.getElementById('category_id');
    const options = categorySelect.querySelectorAll('option[data-type]');
    
    // Reset to default option
    categorySelect.value = '';
    
    // Show/hide options based on type
    options.forEach(option => {
        if (option.getAttribute('data-type') === type) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
