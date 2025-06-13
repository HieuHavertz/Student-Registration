# Student Registration System

A modern web application for managing student records built with Vue 3, Vuetify, and PHP/MySQL.

## Features

- ✨ Add new students with validation
- 📋 View all students in a responsive card layout
- 👁️ View detailed student information
- ✏️ Edit student information inline
- 🗑️ Delete students with confirmation
- 📱 Fully responsive design

## Screenshot demo
![](https://i.postimg.cc/qRCyDKyF/homepage.png)
<br></br>
![](https://i.postimg.cc/fb10dbpf/addstudent.png)

## Prerequisites

- XAMPP (Apache + MySQL + PHP)
- Web browser (Chrome, Firefox, Safari, Edge)

## Installation & Setup

1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Run the installer as Administrator
3. Install to `C:\xampp` (default location)
4. During installation, select:
   - ✅ Apache
   - ✅ MySQL
   - ✅ PHP
   - ✅ phpMyAdmin

## Start XAMPP Services

1. Open XAMPP Control Panel
2. Click **Start** button for **Apache** service
3. Click **Start** button for **MySQL** service
4. Verify both services show "Running" status (green background)

## Create Database using phpMyAdmin

### Step 1: Access phpMyAdmin
1. Open your web browser
2. Navigate to: `http://localhost/phpmyadmin`
3. You should see the phpMyAdmin interface

### Step 2: Create New Database
1. Click **"New"** in the left sidebar
2. Enter database name: `studentdb`
3. Leave **Collation** as default (`utf8mb4_general_ci`)
4. Click **"Create"** button

### Step 3: Create Students Table
1. Select the newly created `studentdb` database from the left sidebar
2. Click **"SQL"** tab at the top
3. Copy and paste the following SQL code:

```sql
CREATE TABLE student (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    major VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    address VARCHAR(100) NOT NULL,
);
```

## Deploy Application Files

### Step 1: Locate XAMPP htdocs Folder
1. Open **File Explorer** (Windows + E)
2. Navigate to: `C:\xampp\htdocs\`
3. This is where all web applications are stored in XAMPP

### Step 2: Create Project Folder
1. Right-click in the `htdocs` folder
2. Select **"New"** → **"Folder"**
3. Name the folder: `student-registration`
4. Press **Enter** to create the folder
5. Your full path should be: `C:\xampp\htdocs\student-registration\`

### Step 3: Copy Project Files
1. Copy all your project files into the `student-registration` folder
2. Your folder structure should look like this:

````
student-registration/
├── api/                       # API endpoints (PHP)
│   ├── add-student.php        # Add a new student
│   ├── get-student.php       # Fetch all students
│   ├── update-student.php     # Update a student
│   ├── delete-student.php     # Delete a student
│   └── db.php                 # Database connection
├── components/                # Vue.js components
│   ├── AddStudent.js          # Add student form
│   └── StudentList.js         # Student list display
├── index.html                 # Main HTML file
├── app.js                     # Vue.js main application
└── README.md                  
````

## Access the application
1. Open your web browser
2. Navigate to: `localhost/student-registration`
3. You should see the Student Registration interface