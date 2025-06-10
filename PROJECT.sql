CREATE DATABASE pomodoro_db;
USE pomodoro_db;
CREATE TABLE [user] (
    user_ID INT PRIMARY KEY IDENTITY(1,1),
    username NVARCHAR(200) NOT NULL,
    password_hash NVARCHAR(255) NOT NULL,
    signup_date DATETIME NOT NULL DEFAULT GETDATE()
);
CREATE TABLE task (
    Task_ID INT PRIMARY KEY IDENTITY(1,1),
    user_ID INT NOT NULL,
    Title NVARCHAR(255) NOT NULL,
    Estimated_sessions INT DEFAULT 1,
    Estimated_time INT DEFAULT 25,
    Priority NVARCHAR(10) DEFAULT 'Medium' CHECK (Priority IN ('Low', 'Medium', 'High')),
    Is_completed BIT DEFAULT 0,
    FOREIGN KEY (user_ID) REFERENCES [user](user_ID)
);
CREATE TABLE settings (
    user_ID INT PRIMARY KEY,
    Work_time INT DEFAULT 25,
    Short_break INT DEFAULT 5,
    Long_break INT DEFAULT 15,
    Dark_mode BIT DEFAULT 0,
    FOREIGN KEY (user_ID) REFERENCES [user](user_ID)
);


CREATE TABLE sessions (
    Session_ID INT PRIMARY KEY IDENTITY(1,1),
    user_ID INT NOT NULL,
    Task_ID INT NOT NULL,
    Session_Type NVARCHAR(20) NOT NULL CHECK (Session_Type IN ('work', 'short_break', 'long_break')),
    Duration INT NOT NULL,
    Custom_work INT DEFAULT NULL,
    Custom_break INT DEFAULT NULL,
    [Date] DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (user_ID) REFERENCES [user](user_ID),
    FOREIGN KEY (Task_ID) REFERENCES task(Task_ID)
);


CREATE TABLE history (
    Id INT PRIMARY KEY IDENTITY(1,1),
    user_ID INT NOT NULL,
    Task_ID INT NOT NULL,
    Session_type NVARCHAR(20) NOT NULL CHECK (Session_type IN ('work', 'short_break', 'long_break')),
    Duration INT NOT NULL,
    [Date] DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (user_ID) REFERENCES [user](user_ID),
    FOREIGN KEY (Task_ID) REFERENCES task(Task_ID)
);


CREATE TABLE goals (
    Id INT PRIMARY KEY IDENTITY(1,1),
    user_ID INT NOT NULL,
    Goal_Text NVARCHAR(MAX) NOT NULL,
    Status NVARCHAR(10) DEFAULT 'Active' CHECK (Status IN ('Active', 'Achieved', 'Failed')),
    Created_Date DATETIME DEFAULT GETDATE(),
    Achieved_date DATETIME DEFAULT NULL,
    FOREIGN KEY (user_ID) REFERENCES [user](user_ID)
);
