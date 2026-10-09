# Federal School of Surveying Online Classroom

A specialized classroom management system for the Federal School of Surveying, built with pure PHP and MySQL.

## Features

- **User Authentication**: Secure register, login, and logout system with role-based redirection (Admin, Teacher, Student).
- **Teacher Dashboard**:
  - Create and manage classes (auto-generate class codes).
  - Post class announcements.
  - Create and manage assignments with file uploads.
  - View student submissions and provide grades/feedback.
- **Student Dashboard**:
  - Join classes using class codes.
  - Search for available classes.
  - View class announcements and assignments.
  - Submit assignments with file uploads.
  - View grades and feedback from teachers.
- **Admin Dashboard**:
  - Overview of system statistics (users, classes, assignments, submissions).
  - Manage all users (view and delete).
  - Manage all classes (view and delete).
- **Security**: Prepared statements for all database queries, password hashing, and secure session management.
- **UI Design**: Modern, responsive design using Bootstrap 5 and Google Fonts, inspired by Google Classroom.

## How to Run on XAMPP

1.  **Extract the project**: Place the `google-classroom` folder inside your XAMPP's `htdocs` directory (usually `C:\xampp\htdocs\`).
2.  **Start XAMPP**: Open the XAMPP Control Panel and start **Apache** and **MySQL**.
3.  **Import Database**:
    - Open your browser and go to `http://localhost/phpmyadmin/`.
    - Create a new database named `classroom_db`.
    - Click on the `Import` tab and select the `database.sql` file located in the project root.
    - Click `Go` to import the tables and sample data.
4.  **Run the Application**:
    - Open your browser and go to `http://localhost/google-classroom/`.
    - You can now register a new account or use the sample credentials below.

## Sample Credentials

- **Admin**: `admin@example.com` / `123456`
- **Teacher**: `teacher@example.com` / `123456`
- **Student**: `student@example.com` / `123456`

## Project Structure

```
/google-classroom
│── index.php (Home Page)
│── login.php (Login Page)
│── register.php (Registration Page)
│── logout.php (Logout Script)
│── profile.php (User Profile Management)
│── database.sql (MySQL Schema and Sample Data)
│── /config
│   └── db.php (Database Connection and Helpers)
│── /includes
│   ├── header.php (Shared Header)
│   ├── footer.php (Shared Footer)
│   └── auth.php (Authentication Protection)
│── /admin
│── /teacher
│── /student
│── /uploads (Stores uploaded assignments and submissions)
│── /assets
│   ├── css/style.css
│   ├── img/fss-logo.png (FSS School Logo)
│   └── js/main.js
```

## Security Note

-   This project uses `PDO` with prepared statements to prevent SQL injection.
-   Passwords are encrypted using `password_hash()`.
-   Session-based authentication ensures that only authorized users can access specific pages.
-   File uploads should be further restricted in production (e.g., checking MIME types beyond extensions).
