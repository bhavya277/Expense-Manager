/**
 * Custom JavaScript for Expense Manager
 * Enhanced functionality and user interactions
 * Form validation and AJAX operations
 */

// DOM Content Loaded Event
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeFormValidation();
    initializeTooltips();
    initializeAlerts();
    initializeCharts();
    initializeFilters();
});

/**
 * Form Validation
 * Client-side validation for better user experience
 */
function initializeFormValidation() {
    // Amount validation
    const amountInputs = document.querySelectorAll('input[name="amount"]');
    amountInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value < 0) {
                this.value = 0;
            }
            if (value > 999999.99) {
                this.value = 999999.99;
            }
        });
    });

    // Password confirmation validation
    const confirmPassword = document.getElementById('confirm_password');
    const password = document.getElementById('password');
    
    if (confirmPassword && password) {
        function validatePassword() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
        
        password.addEventListener('change', validatePassword);
        confirmPassword.addEventListener('keyup', validatePassword);
    }

    // Date validation
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            
            if (selectedDate > today) {
                this.setCustomValidity('Date cannot be in the future');
            } else {
                this.setCustomValidity('');
            }
        });
    });
}

/**
 * Initialize Bootstrap Tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Auto-hide alerts after 5 seconds
 */
function initializeAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success')) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        }
    });
}

/**
 * Chart initialization and management
 */
function initializeCharts() {
    // Chart.js global configuration
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.color = '#6c757d';
    
    // Responsive charts
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;
}

/**
 * Filter functionality
 */
function initializeFilters() {
    // Date range validation
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    
    if (dateFrom && dateTo) {
        function validateDateRange() {
            if (dateFrom.value && dateTo.value) {
                const fromDate = new Date(dateFrom.value);
                const toDate = new Date(dateTo.value);
                
                if (fromDate > toDate) {
                    dateTo.setCustomValidity('End date must be after start date');
                } else {
                    dateTo.setCustomValidity('');
                }
            }
        }
        
        dateFrom.addEventListener('change', validateDateRange);
        dateTo.addEventListener('change', validateDateRange);
    }
}

/**
 * AJAX Helper Functions
 */
function showLoading(element) {
    element.classList.add('loading');
    element.disabled = true;
}

function hideLoading(element) {
    element.classList.remove('loading');
    element.disabled = false;
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-1"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}

/**
 * Category Management
 */
function updateCategories() {
    const type = document.getElementById('type');
    const categorySelect = document.getElementById('category_id');
    
    if (!type || !categorySelect) return;
    
    const selectedType = type.value;
    const options = categorySelect.querySelectorAll('option[data-type]');
    
    // Reset to default option
    categorySelect.value = '';
    
    // Show/hide options based on type
    options.forEach(option => {
        if (option.getAttribute('data-type') === selectedType) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    });
}

/**
 * Add Custom Category
 */
function addCustomCategory() {
    const form = document.getElementById('addCategoryForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const nameInput = document.getElementById('new_category_name');
        const typeSelect = document.getElementById('new_category_type');
        
        if (!nameInput || !typeSelect) return;
        
        const name = nameInput.value.trim();
        const type = typeSelect.value;
        
        if (!name) {
            showAlert('Please enter a category name', 'danger');
            return;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        showLoading(submitBtn);
        
        // Send AJAX request
        fetch('ajax/add_category.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `name=${encodeURIComponent(name)}&type=${type}`
        })
        .then(response => response.json())
        .then(data => {
            hideLoading(submitBtn);
            
            if (data.success) {
                // Add new option to category select
                const categorySelect = document.getElementById('category_id');
                if (categorySelect) {
                    const newOption = document.createElement('option');
                    newOption.value = data.category_id;
                    newOption.textContent = name;
                    newOption.setAttribute('data-type', type);
                    categorySelect.appendChild(newOption);
                }
                
                // Clear form
                nameInput.value = '';
                showAlert('Category added successfully!', 'success');
            } else {
                showAlert('Error adding category: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            hideLoading(submitBtn);
            console.error('Error:', error);
            showAlert('Error adding category', 'danger');
        });
    });
}

/**
 * Transaction Form Enhancements
 */
function enhanceTransactionForm() {
    const typeSelect = document.getElementById('type');
    if (typeSelect) {
        typeSelect.addEventListener('change', updateCategories);
    }
    
    // Auto-focus on amount field
    const amountInput = document.getElementById('amount');
    if (amountInput) {
        amountInput.focus();
    }
    
    // Add enter key support for quick submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                e.preventDefault();
                form.submit();
            }
        });
    }
}

/**
 * Table Enhancements
 */
function enhanceTables() {
    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(0, 123, 255, 0.05)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
}

/**
 * Search and Filter Enhancements
 */
function enhanceFilters() {
    // Add search functionality to tables
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.table tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
}

/**
 * Export Functionality
 */
function exportData(format = 'csv') {
    const table = document.querySelector('.table');
    if (!table) return;
    
    let csv = '';
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('th, td');
        const rowData = Array.from(cells).map(cell => {
            return '"' + cell.textContent.replace(/"/g, '""') + '"';
        });
        csv += rowData.join(',') + '\n';
    });
    
    if (format === 'csv') {
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'transactions.csv';
        a.click();
        window.URL.revokeObjectURL(url);
    }
}

/**
 * Print Functionality
 */
function printPage() {
    window.print();
}

/**
 * Initialize all enhancements
 */
document.addEventListener('DOMContentLoaded', function() {
    enhanceTransactionForm();
    enhanceTables();
    enhanceFilters();
    addCustomCategory();
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + N for new transaction
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            window.location.href = 'add_transaction.php';
        }
        
        // Ctrl + D for dashboard
        if (e.ctrlKey && e.key === 'd') {
            e.preventDefault();
            window.location.href = 'dashboard.php';
        }
        
        // Ctrl + R for reports
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            window.location.href = 'reports.php';
        }
    });
});

/**
 * Utility Functions
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    }).format(new Date(date));
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
