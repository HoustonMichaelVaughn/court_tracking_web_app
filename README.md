# Court Tracking System 🧑‍⚖️

## Overview

The Court Tracking System is a centralized, web-based application designed for police departments to efficiently track court cases, events, and legal outcomes. It replaces paper-based and fragmented digital systems, increasing data reliability and ensuring legal compliance—particularly with spent conviction legislation. 

The system provides secure, role-based access to manage cases, defendants, lawyers, and court schedules, and includes audit logging for accountability.

## About the Project

The aim is to streamline court tracking operations using a secure, browser-accessible web platform that:

- Improves record accuracy.
- Supports upcoming court dates and verdicts tracking.
- Provides real-time updates and audit logging.
- Minimizes manual administrative work.

## Project Team

| Member      | Responsibility          | Description                                   |
|-------------|--------------------------|-----------------------------------------------|
| Micia       | Authentication           | Implemented secure login and role access      |
| Frank       | Database + Records       | Designed schema, CRUD for cases, lawyers, etc.|
| Houston     | Search                   | Developed search interface and backend        |
| Prasansha   | Logs/Dashboard           | Developed overview dashboard and logs         |
| Ricky       | Announcements/Calendar   | Developed internal notice updates and calendar|

## Business Requirements

Police departments required a modern, legally compliant way to manage court-related data. Our system:

- Prevents missed court dates and delayed updates.
- Supports tracking defendants, charges, lawyers, and outcomes.
- Ensures compliance with legal standards.

**Final Inclusions:**

- Secure login
- Case dashboard
- CRUD for cases, defendants, lawyers
- Event tracking
- Role-based access
- Audit logging

**Final Exclusions:**

- Mobile app (web only)
- Court system integrations
- Advanced analytics
- Public-facing access

## Project Approach

The system follows a three-tier MVC architecture using PHP. REST-style routing is custom-built using `mouse.php`, and each functionality is modularized to support team development and long-term maintainability.

## Strategy

- **Models:** Secure, parameterized database access.
- **Controllers:** Handle HTTP requests and business logic.
- **Views:** Render dynamic HTML with Bootstrap 5.

RESTful routing is implemented with custom curly-brace syntax (`/defendant/{id}`), allowing clean, dynamic URLs.

## The Online Prototype

Key features include:

- **Case Management:** Add/view/edit/delete cases and associate defendants, lawyers, charges, and court events.
- **User Authentication:** Role-based secure access.
- **Responsive Design:** Works on desktops and mobile browsers (Bootstrap 5).

Test Admin Account:
```
username: admin
password: password
```

## Installation Instructions

### Requirements

- PHP 8.0+
- MySQL 8.0+
- PDO Extension
- Web Server (WAMP, LAMP, MAMP)

### Document Root

Place the app folder inside your server's `/htdocs/`.

### Database Setup

1. Import the SQL file:
    - Use `phpMyAdmin` to import: `database/init_db.sql`
2. Configure credentials:
    - In `lib/includes/Database.php`, update:
      ```php
      $dbHost = 'localhost';
      $dbName = 'court_tracking_system';
      $dbUser = 'your_username';
      $dbPass = 'your_password';
      ```

3. Launch the app:
    - Start your server and access `http://localhost/court_tracking_web_app/public`

## Uninstalling

1. Delete `/court_tracking_web_app/` from `htdocs/`.
2. Drop the database:
   ```sql
   DROP DATABASE court_tracking_system;
    ```

## File and Folder Structure
```
/public/        → Public entry point (controllers, .htaccess)
/lib/
  └ includes/   → Controllers
  └ models/     → Data models (SQL logic)
  └ views/      → HTML templates and reusable partials
/database/
  └ init_db.sql → Sets up schema
/docs/          → Documentation, ERD
README.md       → Project overview
```

## Database & Application Testing

### Database Tests

Run: 
- `/database_tests/load.sql`
- `/database_tests/test.sql`

Validates all CRUD operations on cases, charges, events, defendants, and lawyers.

### Application Tests

Verify:
- Login/logout
- Add/update/delete for all entities
- Case wizard functionality
- Dashboard and logs

## Working Code Description

### Routing & Logic

Custom `mouse.php` handles route registration and resolution. Uses curly braces for route parameters:

```php
$router->get('/defendant/{id}', 'includes/defendant_controller.php');
```

### State Management

Sessions are used:
- `session_start()` in entry point
- Flash messaging: `set_session_message()`, `get_session_message()`

### MVC Breakdown

- Controllers: Handle logic (`lib/includes/`)
- Models: Query database securely with PDO (`lib/models/`)
- Views: Reusable HTML using Bootstrap 5 (`lib/views/`)

## Technology Stack

- PHP 8.2
- MySQL
- Bootstrap 5
- HTML5, CSS
- Apache/MAMP/XAMPP

## Mobile Readiness

The application is responsive using Bootstrap 5. Fully tested on Chrome mobile simulator.

## Security

- SQL Injection Protection: All queries use PDO + prepared statements (`lib/models/*.php`)
- Input Validation: Server-side validation in controllers
- Session Management: Secure login, logout, and flash messages
- Sensitive Info: DB credentials in `lib/includes/Database.php` (not exposed publicly)
- Password Protection: Passwords hashed using `password_hash()` and verified with `password_verify()`

## Licensing

Licensed under the Apache License 2.0.

- Allows commercial and private use
- Patent protection included
- No copyleft requirements
- Includes attribution requirement
