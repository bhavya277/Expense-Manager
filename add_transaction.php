<?php
/**
 * Add Transaction Page
 * Allows users to add new income or expense transactions
 * Includes form validation and category management
 */

// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
require_once 'includes/db.php';

$pageTitle = 'Add Transaction';
$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Get default transaction type from URL parameter
$defaultType = isset($_GET['type']) && in_array($_GET['type'], ['income', 'expense']) ? $_GET['type'] : 'expense';

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
        // Insert transaction
        $result = executeQuery(
            "INSERT INTO transactions (user_id, amount, type, category_id, description, transaction_date) VALUES (?, ?, ?, ?, ?, ?)",
            [$userId, $amount, $type, $categoryId, $description, $transactionDate]
        );
        
        if ($result) {
            $success = 'Transaction added successfully!';
            // Clear form data
            $amount = $description = '';
            $transactionDate = date('Y-m-d');
        } else {
            $error = 'Failed to add transaction. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-plus me-2"></i>Add New Transaction</h4>
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
                                <option value="expense" <?php echo $defaultType == 'expense' ? 'selected' : ''; ?>>Expense</option>
                                <option value="income" <?php echo $defaultType == 'income' ? 'selected' : ''; ?>>Income</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       step="0.01" min="0.01" value="<?php echo htmlspecialchars($amount ?? ''); ?>" required>
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
                                        <?php echo ($category['type'] == $defaultType) ? '' : 'style="display:none"'; ?>>
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
                                   value="<?php echo $transactionDate ?? date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Enter transaction description..."required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Add Transaction
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Quick Add Categories -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Add Custom Category</h5>
            </div>
            <div class="card-body">
                <form id="addCategoryForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_category_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="new_category_name" placeholder="Enter category name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_category_type" class="form-label">Type</label>
                            <select class="form-select" id="new_category_type">
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>Add Category
                    </button>
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

// Add custom category
document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const name = document.getElementById('new_category_name').value;
    const type = document.getElementById('new_category_type').value;
    
    if (!name.trim()) {
        alert('Please enter a category name');
        return;
    }
    
    // Send AJAX request to add category
    fetch('ajax/add_category.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `name=${encodeURIComponent(name)}&type=${type}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new option to category select
            const categorySelect = document.getElementById('category_id');
            const newOption = document.createElement('option');
            newOption.value = data.category_id;
            newOption.textContent = name;
            newOption.setAttribute('data-type', type);
            categorySelect.appendChild(newOption);
            
            // Clear form
            document.getElementById('new_category_name').value = '';
            
            alert('Category added successfully!');
        } else {
            alert('Error adding category: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding category');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
