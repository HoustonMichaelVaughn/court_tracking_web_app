# 🧑‍⚖️ Court Tracking System

A centralized, web-based application for police departments to securely track court cases, events, and legal outcomes. Replaces paper-based and fragmented digital systems, ensuring data reliability and compliance with spent-conviction legislation.

## 🚀 Features

### 🔐 Secure Access & User Roles
- **Role-Based Access Control**  
  - **Admins**: Full access to create/edit/delete data, manage users, and view audit logs.  
  - **Users**: View-only access to cases, events, and verdicts (cannot create accounts, edit cases, or view logs).  
- **Password Security**  
  - Passwords are hashed using PHP’s `password_hash()` and verified with `password_verify()`.  
- **Session Management**  
  - Secure login/logout, flash messaging for success & error notifications.

### 📁 Case Management
- **Full CRUD** for court cases, defendants, lawyers, charges, and outcomes.  
- **Case Wizard** to step through adding defendants, assigning charges, and scheduling events.  
- **Rich Case Notes**: Attach hearing transcripts, evidence references, and outcome summaries.

### 📊 Dashboard & Logs
- **Overview Dashboard**: Key metrics on open cases.  
- **Audit Logging**: Track who did what and when—critical for accountability and legal compliance.

## 👥 User Roles & Permissions

| Role  | CRUD Defendants & Lawyers | CRUD Cases (with Charges & Events) | View Cases | Manage Users | View Logs |
|-------|---------------------------|-------------------------------------|------------|--------------|-----------|
| Admin | ✔️                        | ✔️                                  | ✔️         | ✔️           | ✔️        |
| User  | ✔️                        | ❌                                  | ✔️         | ❌           | ❌        |


## 📝 Default Test Account

Use these credentials to log in as an Admin for testing:

    Username: admin
    Password: password

## 🏗️ Architecture & Tech Stack

- **Architecture**: Three-tier MVC (Models, Views, Controllers)  
- **Routing**: Custom REST-style routing via `mouse.php` (e.g., `/defendant/{id}`)  
- **Backend**: PHP 8.2, PDO for secure parameterized queries  
- **Database**: MySQL 8.0+  
- **Frontend**: Bootstrap 5, HTML5, CSS3  
- **Server**: Apache (WAMP/LAMP/MAMP/XAMPP compatible)

## ⚙️ Installation

1. Clone or copy the `court_tracking_web_app/` folder into your server’s document root (e.g., `htdocs` or `www`).  
2. Install dependencies:  
    - PHP 8.0+ with PDO extension  
    - MySQL 8.0+  
3. Database setup:  
    - Import `database/init_db.sql` via phpMyAdmin or CLI:  
        mysql -u your_username -p < database/init_db.sql  
    - Update credentials in `lib/includes/Database.php`:  
        ```php
        $dbHost = 'localhost';
        $dbName = 'court_tracking_system';
        $dbUser = 'your_username';
        $dbPass = 'your_password';
        ```  
4. Launch the app by navigating to:  
    http://localhost/court_tracking_web_app/public

## 🗑️ Uninstall

1. Remove the project folder from your web root:
   ``` 
   rm -rf /path/to/htdocs/court_tracking_web_app
   ```  
3. Drop the database:
   ```sql
   DROP DATABASE court_tracking_system;
   ```

## 📂 Project Structure

    /public/                → Public entry point, .htaccess, asset routing  
    /lib/
      ├ includes/           → Controllers (HTTP request handlers)  
      ├ models/             → Data access layer (PDO, SQL logic)  
      └ views/              → HTML templates & Bootstrap partials  
    /database/
      └ init_db.sql         → Database schema & initial data  
    /docs/                  → Documentation, ERDs
    README.md               → Project overview & setup instructions

## ✅ Testing

### Database Tests
Run SQL scripts in `/database_tests/`:

    mysql -u your_username -p < database_tests/load.sql  
    mysql -u your_username -p < database_tests/test.sql  

### Application Tests
- Verify login/logout flows.  
- Test CRUD operations for cases, defendants, lawyers, charges, and events.  
- Confirm dashboard metrics and audit logs.

## 🔒 Security

- **SQL Injection Protection**: All queries use parameterized PDO statements.  
- **Input Validation**: Server-side checks in controllers.  
- **Session Security**: Regenerated session IDs on login, secure cookie flags.  
- **Password Handling**: Strong hashing and salting (`bcrypt` via `password_hash()`).

## 📜 License

Licensed under the **Apache License 2.0**  
- Commercial & private use permitted  
- Patent protection included  
- Attribution required  
- No copyleft obligations  

Enjoy streamlined, compliant court tracking!
