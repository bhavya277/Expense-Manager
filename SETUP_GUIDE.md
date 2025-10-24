# Expense Manager - Setup Guide

## Quick Start Guide for Students

### 🎯 What You'll Learn
- PHP web development fundamentals
- MySQL database operations
- User authentication and security
- Responsive web design
- Data visualization with charts

### 📋 Prerequisites
- Basic understanding of HTML, CSS, and JavaScript
- XAMPP installed on your computer
- Text editor (VS Code, Sublime Text, or Notepad++)

## Step-by-Step Installation

### Step 1: Install XAMPP
1. **Download XAMPP**
   - Go to [https://www.apachefriends.org/download.html](https://www.apachefriends.org/download.html)
   - Download XAMPP for your operating system
   - Run the installer and follow the setup wizard

2. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Click "Start" next to Apache
   - Click "Start" next to MySQL
   - Both services should show "Running" status

### Step 2: Setup Project Files
1. **Download Project**
   - Download or clone the expense-manager project
   - Extract the files to your computer

2. **Copy to XAMPP Directory**
   - Navigate to your XAMPP installation folder
   - Go to `htdocs` folder
   - Copy the entire `expense-manager` folder here
   - Path should be: `C:\xampp\htdocs\expense-manager\` (Windows)

### Step 3: Create Database
1. **Open phpMyAdmin**
   - Open your web browser
   - Go to `http://localhost/phpmyadmin`
   - You should see the phpMyAdmin interface

2. **Create Database**
   - Click "New" in the left sidebar
   - Enter database name: `expense_manager`
   - Click "Create"

3. **Import Database Structure**
   - Select the `expense_manager` database
   - Click "Import" tab
   - Click "Choose File" and select `database.sql`
   - Click "Go" to import

### Step 4: Test Installation
1. **Open Application**
   - Go to `http://localhost/expense-manager`
   - You should see the home page

2. **Register Account**
   - Click "Register" or "Get Started"
   - Fill in the registration form
   - Click "Register"

3. **Login and Explore**
   - Login with your credentials
   - Explore the dashboard and features

## 🎮 How to Use the Application

### First Time Setup
1. **Create Account**
   - Register with username, email, and password
   - Remember your login credentials

2. **Add Your First Transaction**
   - Click "Add Transaction" from dashboard
   - Select "Expense" or "Income"
   - Enter amount, select category, add description
   - Choose date and save

3. **Explore Features**
   - View your transactions
   - Check the dashboard for overview
   - Generate reports to analyze spending

### Key Features to Try
- **Dashboard**: See your financial summary
- **Add Transaction**: Record income and expenses
- **View Transactions**: Browse all your records
- **Reports**: Generate detailed financial reports
- **Categories**: Add custom categories

## 🔧 Configuration Options

### Database Settings
If you need to change database settings, edit `includes/db.php`:

```php
$host = 'localhost';           // Database server
$dbname = 'expense_manager';   // Database name
$username = 'root';            // Database username
$password = '';                // Database password
```

### Customization
- **Styling**: Edit `assets/style.css` for custom colors and layout
- **Functionality**: Modify `assets/script.js` for additional features
- **Database**: Update `database.sql` for schema changes

## 🐛 Common Issues & Solutions

### Issue: "Connection failed" Error
**Solution:**
- Check if MySQL is running in XAMPP Control Panel
- Verify database name is `expense_manager`
- Ensure no other application is using port 3306

### Issue: Page Shows "404 Not Found"
**Solution:**
- Verify files are in `C:\xampp\htdocs\expense-manager\`
- Check if Apache is running
- Try accessing `http://localhost/expense-manager/index.php`

### Issue: Charts Not Displaying
**Solution:**
- Check internet connection (Chart.js loads from CDN)
- Verify JavaScript is enabled in browser
- Check browser console for errors

### Issue: Can't Login After Registration
**Solution:**
- Check if database was imported correctly
- Verify user table has data
- Try registering again with different username

## 📚 Learning Path

### Week 1: Basic Setup
- Install XAMPP and project
- Understand file structure
- Learn basic PHP syntax

### Week 2: Database Operations
- Study database schema
- Learn SQL queries
- Understand relationships

### Week 3: User Interface
- Explore HTML structure
- Study CSS styling
- Learn JavaScript interactions

### Week 4: Advanced Features
- Implement new features
- Add custom functionality
- Deploy to web server

## 🎯 Project Extensions

### Beginner Level
- Add more transaction categories
- Create custom dashboard widgets
- Implement data export functionality

### Intermediate Level
- Add user profiles and settings
- Implement data backup/restore
- Create mobile-responsive improvements

### Advanced Level
- Add multi-currency support
- Implement budget tracking
- Create API endpoints
- Add real-time notifications

## 📖 Additional Resources

### PHP Learning
- [PHP.net Official Documentation](https://www.php.net/docs.php)
- [W3Schools PHP Tutorial](https://www.w3schools.com/php/)
- [PHP The Right Way](https://phptherightway.com/)

### MySQL Learning
- [MySQL Official Documentation](https://dev.mysql.com/doc/)
- [W3Schools SQL Tutorial](https://www.w3schools.com/sql/)
- [MySQL Tutorial](https://www.mysqltutorial.org/)

### Web Development
- [MDN Web Docs](https://developer.mozilla.org/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)
- [Chart.js Documentation](https://www.chartjs.org/docs/)

## 🎓 Assignment Ideas

### Basic Assignments
1. Add a "Notes" field to transactions
2. Create a "Favorites" feature for categories
3. Implement transaction search functionality

### Intermediate Assignments
1. Add data validation for negative amounts
2. Create a "Budget" tracking system
3. Implement transaction tags/labels

### Advanced Assignments
1. Add user roles (Admin, User)
2. Create data import/export features
3. Implement real-time dashboard updates

## 🚀 Deployment Options

### Local Development
- Use XAMPP for development and testing
- Perfect for learning and experimentation

### Web Hosting
- Upload to shared hosting (cPanel, etc.)
- Use cloud platforms (Heroku, DigitalOcean)
- Deploy to VPS for production use

## 📞 Getting Help

### When You're Stuck
1. **Check Error Messages**: Look for specific error details
2. **Review Code**: Compare with working examples
3. **Ask Questions**: Use online forums and communities
4. **Documentation**: Consult official documentation

### Useful Communities
- Stack Overflow
- Reddit r/PHP
- PHP.net Community
- GitHub Discussions

---

**Happy Learning! 🎉**

*This project is designed to teach you real-world web development skills. Take your time, experiment, and don't hesitate to explore and modify the code!*
