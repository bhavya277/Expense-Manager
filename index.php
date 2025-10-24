<?php
/**
 * Home Page / Landing Page
 * Redirects logged-in users to dashboard
 * Shows welcome page for non-logged-in users
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to dashboard if already logged in
    header('Location: dashboard.php');
    exit();
}

$pageTitle = 'Home';
include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="row">
    <div class="col-12">
        <div class="jumbotron bg-primary text-white rounded p-5 mb-4">
            <div class="container-fluid">
                <h1 class="display-4">
                    <i class="fas fa-wallet me-3"></i>Expense Manager
                </h1>
                <p class="lead">Track your income and expenses with ease. Perfect for students and professionals.</p>
                <hr class="my-4">
                <p>Manage your finances, view reports, and stay on top of your budget.</p>
                <div class="d-flex gap-3">
                    <a class="btn btn-light btn-lg" href="register.php" role="button">
                        <i class="fas fa-user-plus me-2"></i>Get Started
                    </a>
                    <a class="btn btn-outline-light btn-lg" href="login.php" role="button">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="row">
    <div class="col-12">
        <h2 class="text-center mb-4">Features</h2>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Track Expenses</h5>
                <p class="card-text">Record and categorize your daily expenses to understand your spending patterns.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-money-bill-wave fa-3x text-success mb-3"></i>
                <h5 class="card-title">Manage Income</h5>
                <p class="card-text">Keep track of all your income sources and see your financial growth.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-chart-pie fa-3x text-info mb-3"></i>
                <h5 class="card-title">View Reports</h5>
                <p class="card-text">Generate detailed reports and visualizations of your financial data.</p>
            </div>
        </div>
    </div>
</div>

<!-- How it Works Section -->
<div class="row mt-5">
    <div class="col-12">
        <h2 class="text-center mb-4">How It Works</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-3 text-center mb-4">
        <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
            <span class="fw-bold">1</span>
        </div>
        <h5>Register</h5>
        <p>Create your free account in seconds</p>
    </div>
    
    <div class="col-md-3 text-center mb-4">
        <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
            <span class="fw-bold">2</span>
        </div>
        <h5>Add Transactions</h5>
        <p>Record your income and expenses</p>
    </div>
    
    <div class="col-md-3 text-center mb-4">
        <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
            <span class="fw-bold">3</span>
        </div>
        <h5>View Dashboard</h5>
        <p>See your financial overview at a glance</p>
    </div>
    
    <div class="col-md-3 text-center mb-4">
        <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
            <span class="fw-bold">4</span>
        </div>
        <h5>Generate Reports</h5>
        <p>Analyze your spending with detailed reports</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
