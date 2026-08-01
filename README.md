# SkillLink-Student-skill-Exchange-Platform-
A Web application for University student to Exchange Skills, learn from each other and collaborate through skill swapping.

# SkillLink - Student Skill Exchange Platform

**SkillLink** is an interactive, database-driven web application designed to empower university undergraduates to exchange skills, offer peer-to-peer learning sessions, and connect with fellow students based on shared academic and creative interests.

---

## Project Details & Academic Information

* **Course Name:** ICT 1209 - Web Technologies[cite: 1, 2]
* **Degree Program:** Bachelor of ICT (First Year, 2024 Batch)
* **Institution:** Rajarata University of Sri Lanka | Faculty of Technology (Department of ICT)[cite: 1, 2]
* **Project Type:** Pair Work (Mini Project)[cite: 1, 2]
* **Supervisor:** Ms. Piyumi N. Herath

###  Group Members
1. **A.A.D.K. Adhikari** — Student ID: `ITT/2024/008`
2. **M.A.N.D. Manamendra** — Student ID: `ITT/2024/065`[cite: 2]

---

## Key Features

1. **Skill Profiles & Exchange Dashboard:** Students can showcase their expertise, view requested skills, and track peer-learning progress[cite: 2].
2. **Smart Search & Filter System:** Search for specific skills (e.g., Python, Photography, Guitar, Public Speaking) and filter by category or skill level[cite: 2].
3. **Dynamic Skill Ticker & Matching Widget:** Client-side JavaScript-powered dynamic updates showcasing simulated live swaps and match percentages.
4. **Interactive Contact & Request System:** Submit queries or peer skill-swap requests directly through responsive contact forms[cite: 2].
5. **Secure Authentication System:** User registration and login functionality featuring password hashing (`PASSWORD_BCRYPT`) and clean session handling[cite: 1, 2].

---

##  Required Technology Stack

| Layer | Technologies |
| :--- | :--- |
| **Frontend Structure & Styling** | HTML5, CSS3, Bootstrap 5[cite: 1, 2] |
| **Client-Side Logic** | JavaScript (Vanilla)[cite: 1, 2] |
| **Backend Processing** | PHP 8[cite: 1, 2] |
| **Database** | MySQL (via XAMPP / WAMP)[cite: 1, 2] |
| **Version Control** | Git & GitHub[cite: 1, 2] |

---

##  Project Folder Structure

```text
SkillLink-Platform/
├── css/
│   └── style.css            # Custom CSS Variables & Component Styles
├── js/
│   └── script.js            # Dynamic Skill Ticker & Client-side Logic
├── images/                  # Profile avatars & platform assets
├── includes/
│   ├── db.php               # Database Connection (PDO/MySQLi)
│   └── functions.php        # Helper functions & validation
├── auth/
│   ├── register.php         # User Registration Form & Logic
│   ├── login.php            # Secure Login System
│   └── logout.php           # Session Destruction
├── index.php                # Platform Homepage & Hero Banner
├── skill.html / dashboard.php # User Skill Dashboard & Search
├── contact.php              # Contact Form & Submissions
├── database.sql             # Exported MySQL Database Structure
└── README.md                # Project Overview & Setup Documentation