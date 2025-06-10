# ⏱️ Pomodoro Task Tracker

A personalized task and time management web app built around the Pomodoro Technique. It helps users focus better, manage tasks efficiently, and keep track of their productivity — all with the flexibility of custom settings and detailed session logs.

---

## 📌 What This Project Does

- 👤 **User Accounts** – Sign up, log in securely, and get your own workspace.
- 📝 **Task Management** – Add, prioritize, and mark tasks as completed.
- 🍅 **Pomodoro Timer Sessions** – Track focused work, short breaks, and long breaks.
- 📈 **Session History** – Every Pomodoro is logged for progress tracking.
- 🎯 **Goal Setting** – Set long-term productivity goals and update their status.
- ⚙️ **Custom Settings** – Adjust work/break durations and toggle dark mode.

---

## 🧱 Tech Stack

- **Frontend**: HTML, CSS, JavaScript  
- **Backend**: PHP  
- **Database**: MySQL  
- **Authentication**: Secure password hashing  
- **Data Handling**: Fully normalized relational schema (3NF)

---

## 🗂️ Database Design

The app uses a **normalized MySQL database**, designed to reduce redundancy and improve data integrity. Here's a quick breakdown:

### `User`
Basic user account information.

| Column        | Type        | Notes              |
|---------------|-------------|--------------------|
| `user_ID`     | int (PK)    | Auto-incremented   |
| `username`    | varchar     | Unique             |
| `password_hash`| varchar     | Hashed             |
| `signup_date` | datetime    | Default: now       |

---

### `Task`
User-created tasks with estimated effort.

| Column        | Type        | Notes                          |
|---------------|-------------|--------------------------------|
| `Task_ID`     | int (PK)    | Auto-incremented               |
| `user_ID`     | int (FK)    | Linked to User                 |
| `Title`       | varchar     | Task title                     |
| `Estimated_sessions` | int | Optional estimate               |
| `Priority`    | enum        | Low, Medium, High              |
| `Is_completed`| boolean     | Mark when done                 |

---

### `Session`
Live Pomodoro session records.

| Column        | Type        | Notes                          |
|---------------|-------------|--------------------------------|
| `Session_ID`  | int (PK)    | Auto-incremented               |
| `user_ID`     | int (FK)    | Linked to User                 |
| `Task_ID`     | int (FK)    | Linked to Task                 |
| `Session_Type`| enum        | work, short_break, long_break |
| `Duration`    | int         | In minutes                     |
| `Date`        | datetime    | When it happened               |

---

### `History`
Archived records of all completed sessions.

| Column        | Type        | Notes              |
|---------------|-------------|--------------------|
| `Id`          | int (PK)    | Auto-incremented   |
| `user_ID`     | int (FK)    | Linked to User     |
| `Task_ID`     | int (FK)    | Linked to Task     |
| `Session_type`| enum        | Matches Session    |
| `Duration`    | int         | In minutes         |
| `Date`        | datetime    | When it happened   |

---

### `Settings`
Custom Pomodoro preferences (per user).

| Column        | Type        | Default Value |
|---------------|-------------|----------------|
| `user_ID`     | int (PK, FK)|                |
| `Work_time`   | int         | 25             |
| `Short_break` | int         | 5              |
| `Long_break`  | int         | 15             |
| `Dark_mode`   | boolean     | 0 (off)        |

---

### `Goals`
User-defined long-term goals.

| Column        | Type        | Notes              |
|---------------|-------------|--------------------|
| `Id`          | int (PK)    | Auto-incremented   |
| `user_ID`     | int (FK)    | Linked to User     |
| `Goal_Text`   | text        | Description        |
| `Status`      | enum        | Active, Achieved, Failed |
| `Created_Date`| datetime    | Default: now       |
| `Achieved_date`| datetime   | Nullable           |
Sure! Here’s a concise and professional paragraph you can include in your README to explain how this project relates to your **Database Systems (DBS) course**:

---

### 🎓 About This Project

This project was developed as part of a **Database Systems course** to demonstrate practical application of relational database design, normalization principles (up to 3NF), and integration with a full-stack web application. It focuses on building a real-world system that involves **user data management**, **task tracking**, **session logging**, and **custom settings**, all powered by a **MySQL backend**. The goal was to not only design an efficient and normalized database schema, but also to implement it in a meaningful and functional way that supports dynamic user interactions.




