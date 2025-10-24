# Expense Manager - PHP Web Application

A complete PHP + MySQL web application for managing personal finances, designed for college-level students. This project demonstrates core web development concepts including user authentication, database operations, and responsive design.

## 🚀 Features

### User Authentication
- User registration with email validation
- Secure login/logout functionality
- Password hashing for security
- Session management

### Financial Management
- **Dashboard**: Overview of income, expenses, and balance
- **Add Transactions**: Record income and expenses with categories
- **View Transactions**: List all transactions with filtering options
- **Edit/Delete**: Modify or remove existing transactions
- **Categories**: Predefined and custom categories for organization

### Reporting & Analytics
- **Financial Reports**: Detailed breakdowns by date range and category
- **Charts & Graphs**: Visual representation of financial data
- **Category Analysis**: Spending patterns and trends
- **Monthly Trends**: Track financial progress over time

### User Interface
- **Responsive Design**: Works on desktop, tablet, and mobile
- **Bootstrap Integration**: Modern, clean interface
- **Interactive Charts**: Chart.js for data visualization
- **Form Validation**: Client and server-side validation

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+ (Core PHP, no frameworks)
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Styling**: Bootstrap 5.3, Custom CSS
- **Charts**: Chart.js
- **Icons**: Font Awesome 6.4
- **Server**: XAMPP/WAMP/LAMP

## 📁 Project Structure

```
expense-manager/
├── index.php                 # Home page
├── register.php             # User registration
├── login.php               # User login
├── logout.php              # Logout handler
├── dashboard.php           # Main dashboard
├── add_transaction.php     # Add new transactions
├── view_transactions.php   # View all transactions
├── edit_transaction.php    # Edit transactions
├── delete_transaction.php  # Delete transactions
├── reports.php            # Financial reports
├── database.sql           # Database structure
├── includes/
│   ├── db.php            # Database connection
│   ├── header.php        # Common header
│   └── footer.php        # Common footer
├── ajax/
│   └── add_category.php   # AJAX endpoint for categories
└── assets/
    ├── style.css         # Custom CSS
    └── script.js         # Custom JavaScript
```

## 🗄️ Database Schema

### Tables

1. **accounts** - User accounts
   - `id` (Primary Key)
   - `username` (Unique)
   - `email` (Unique)
   - `password` (Hashed)
   - `created_at`

2. **categories** - Transaction categories
   - `id` (Primary Key)
   - `name`
   - `type` (income/expense)
   - `user_id` (Foreign Key, NULL for global)
   - `created_at`

3. **transactions** - Financial records
   - `id` (Primary Key)
   - `user_id` (Foreign Key)
   - `amount`
   - `type` (income/expense)
   - `category_id` (Foreign Key)
   - `description`
   - `transaction_date`
   - `created_at`

## 🚀 Installation & Setup

### Prerequisites
- XAMPP/WAMP/LAMP server
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser (Chrome, Firefox, Safari, Edge)

### Step 1: Download and Setup XAMPP
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP on your system
3. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Project Setup
1. Clone or download this project
2. Copy the project folder to `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/` (Linux/Mac)
3. Rename the folder to `expense-manager`

### Step 3: Database Setup
1. Open your web browser and go to `http://localhost/phpmyadmin`
2. Create a new database named `expense_manager`
3. Import the `database.sql` file:
   - Click on the `expense_manager` database
   - Go to the "Import" tab
   - Choose the `database.sql` file
   - Click "Go" to import

### Step 4: Configuration
1. Open `includes/db.php`
2. Update database credentials if needed:
   ```php
   $host = 'localhost';
   $dbname = 'expense_manager';
   $username = 'root';  // Default XAMPP username
   $password = '';      // Default XAMPP password (empty)
   ```

### Step 5: Access the Application
1. Open your web browser
2. Navigate to `http://localhost/expense-manager`
3. Register a new account or login to start using the application

## 📱 Usage Guide

### Getting Started
1. **Register**: Create a new account with username, email, and password
2. **Login**: Access your account with your credentials
3. **Dashboard**: View your financial overview and recent transactions

### Adding Transactions
1. Click "Add Transaction" from the dashboard or navigation
2. Select transaction type (Income/Expense)
3. Enter amount, select category, add description
4. Choose transaction date
5. Click "Add Transaction"

### Managing Categories
- Use predefined categories (Food, Bills, Salary, etc.)
- Add custom categories for personal needs
- Categories are automatically filtered by type

### Viewing Reports
1. Navigate to "Reports" from the main menu
2. Set date range and category filters
3. View detailed breakdowns and charts
4. Analyze spending patterns and trends

## 🔧 Customization

### Adding New Features
- **New Pages**: Create PHP files following the existing pattern
- **Database Changes**: Modify `database.sql` and update connection logic
- **Styling**: Edit `assets/style.css` for custom appearance
- **Functionality**: Add JavaScript functions in `assets/script.js`

### Security Enhancements
- Implement CSRF protection
- Add rate limiting for login attempts
- Use prepared statements (already implemented)
- Add input sanitization

### Performance Optimization
- Add database indexing
- Implement caching
- Optimize image loading
- Use CDN for external resources

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check if MySQL is running in XAMPP
   - Verify database credentials in `includes/db.php`
   - Ensure database `expense_manager` exists

2. **Page Not Loading**
   - Verify Apache is running in XAMPP
   - Check file permissions
   - Ensure files are in correct directory

3. **Charts Not Displaying**
   - Check internet connection (Chart.js loads from CDN)
   - Verify JavaScript is enabled in browser
   - Check browser console for errors

4. **Session Issues**
   - Clear browser cookies and cache
   - Check PHP session configuration
   - Verify file permissions

### Debug Mode
Enable error reporting by adding to the top of PHP files:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📚 Learning Objectives

This project demonstrates:

- **PHP Fundamentals**: Variables, functions, arrays, loops, conditionals
- **Database Operations**: CRUD operations, relationships, queries
- **Web Security**: Password hashing, input validation, SQL injection prevention
- **Frontend Development**: HTML structure, CSS styling, JavaScript interactions
- **Responsive Design**: Mobile-first approach, Bootstrap framework
- **Data Visualization**: Chart.js integration, interactive graphs
- **Project Structure**: Organized code, separation of concerns

## 🎓 Educational Value

Perfect for college students learning:
- Web development fundamentals
- Database design and management
- User interface design
- Security best practices
- Project organization and documentation

## 📄 License

This project is created for educational purposes. Feel free to use, modify, and distribute for learning purposes.

## 🤝 Contributing

This is an educational project. Suggestions and improvements are welcome:
- Code optimization
- Feature additions
- Documentation improvements
- Bug fixes

## 📞 Support

For questions or issues:
1. Check the troubleshooting section
2. Review the code comments
3. Consult PHP and MySQL documentation
4. Ask your instructor or peers

---

**Happy Coding! 🚀**

*This project demonstrates real-world web development skills and is perfect for building a strong foundation in PHP and MySQL development.*
