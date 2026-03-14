Lire ceci en [Français](README.md) | Leer esto en [Español](README.es.md)

# 🎓 ESTIA Stage - Recruitment Platform

![Status](https://img.shields.io/badge/Status-Academic_Project-blue)
![Language](https://img.shields.io/badge/Backend-PHP-purple)
![Frontend](https://img.shields.io/badge/Frontend-TailwindCSS-teal)

## About this Project

**⚠️ Academic Context:**
This project was developed as part of an academic curriculum at **ESTIA**. It is a web application developed **locally** for educational purposes. It is not intended to be deployed in production as is, but serves as a demonstration of Fullstack development skills (PHP/JS/SQL).

The goal was to create a functional platform connecting students and recruiters, with an administration panel for moderation.

---

## Key Features

The application is divided into three distinct areas:

### Student Area
- **Dashboard:** Overview of applications and statistics.
- **Job Search:** Filtering by sector, contract type, and keyword search.
- **Application:** Apply to offers, manage favorites.
- **Messaging:** Real-time chat (simulation) with recruiters.
- **Reporting:** Ability to report suspicious offers to the administration.
- **Profile:** Manage personal information and CV uploads.

### Recruiter Area
- **Job Management:** Create, edit, and delete internship/job offers.
- **Candidate Tracking:** Manage statuses (Pending, Interview, Accepted, Rejected).
- **Messaging:** Direct contact with candidates.
- **Notifications:** Alerts upon receiving messages.

### Admin Area
- **Global Overview:** Statistics on users and offers.
- **User Management:** CRUD (Create, Read, Update, Delete) on students and recruiters.
- **Moderation:** Validation of pending offers and handling of reports (delete or dismiss).

---

## Tech Stack

* **Frontend:** HTML5, CSS3 (TailwindCSS via CDN), JavaScript (Vanilla ES6).
* **Backend:** PHP (Native, no framework).
* **Database:** MySQL.
* **Design:** FontAwesome (Icons), Google Fonts (Inter).

---

## Installation and Setup

This project is designed to run on a local server (WAMP, XAMPP, MAMP, or Laragon).

### Prerequisites
* A local server supporting PHP and MySQL.
* A modern web browser.

### Steps
1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/your-name/estia-stage-platform.git](https://github.com/your-name/estia-stage-platform.git)
    ```
2.  **Database:**
    * Open your DB manager (e.g., phpMyAdmin).
    * Create a database named `estia_stage` (or other).
    * Import the `estia_stage.sql` file located at the root.
3.  **Configuration:**
    * Open the `db.php` file.
    * Edit the PDO connection credentials if necessary (`host`, `dbname`, `username`, `password`).
4.  **Launch:**
    * Place the project folder in the `www` or `htdocs` directory of your server.
    * Access `http://localhost/estia-stage-platform/login.html`.

---

## Demo Accounts

To test the different roles, you can use these pre-configured accounts (if present in the imported SQL file):

| Role | Email | Password |
| :--- | :--- | :--- |
| **Student** | `marie@estia.fr` | `1234` |
| **Recruiter** | `rh@airbus.com` | `1234` |
| **Admin** | `admin@estia.fr` | `1234` |

---

## Project Structure

```text
/
├── admin.html       # Administrator Dashboard
├── login.html       # Login Page
├── recruiter.html   # Recruiter Dashboard
├── signup.html      # Registration Page
├── student.html     # Student Dashboard
├── style.css        # Custom CSS Styles
├── api.php          # Backend (PHP REST API)
├── db.php           # Database Connection
├── estia_stage.sql  # Database Structure and Data
└── README.md        # Documentation

Author : Samuel CHATARD
Date : 16/02/2026