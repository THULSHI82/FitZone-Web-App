# FitZone Fitness Management System

A role-based fitness centre management web application built as an academic full-stack project. FitZone helps members book classes and contact staff, while staff and administrators manage bookings, customer queries, and users.

## Features

- Member registration and secure login
- Member, staff, and administrator dashboards
- Fitness class and membership-plan booking
- Booking updates and cancellations
- Customer query submission and staff replies
- Profile management with optional password changes
- Administrator management of users, bookings, and queries
- Responsive interface using Bootstrap

## Technology Stack

- PHP 8
- MySQL / MariaDB
- HTML5, CSS3, and JavaScript
- Bootstrap 5 and Font Awesome
- PHP sessions and MySQLi prepared statements

## Project Structure

```text
backend/          PHP application logic and dashboard pages
public/           Landing page, styling, JavaScript, and images
database/         MySQL database schema
scripts/          Command-line setup utilities
```

## Local Setup

1. Install PHP 8 and MySQL/MariaDB, or use XAMPP.
2. Import `database/schema.sql` using phpMyAdmin or the MySQL command line.
3. Set the database environment variables if your configuration differs from the defaults:

   ```bash
   export DB_HOST=localhost
   export DB_USER=root
   export DB_PASSWORD=''
   export DB_NAME=fitzonefitness
   ```

4. From the project directory, start the PHP development server:

   ```bash
   php -S localhost:8000
   ```

5. Visit `http://localhost:8000/public/`.

Members can create an account from the registration page. To create a staff or administrator account, run:

```bash
php scripts/create_user.php "Admin User" admin StrongPassword123 admin
```

Replace the example values with your own secure credentials.

## Security Improvements

- Passwords are stored using PHP password hashing.
- Database operations that accept user input use prepared statements.
- Database credentials can be supplied through environment variables.
- Sessions are regenerated after successful login.
- Personal test records and credentials are excluded from the public repository.

## Academic Context

This project demonstrates server-side web development, relational database design, role-based access control, CRUD operations, authentication, and responsive UI development.

## Author

**Thulshani Nawoda Dissanayaka**

Developed as part of the Higher National Diploma in Computing.
