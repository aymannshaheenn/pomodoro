<?php
include 'db.php';

$tasks = [];
$result = $conn->query("SELECT Task_ID, Title FROM task");
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $task[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pomodoro Timer - Dashboard</title>
  <link rel="stylesheet" href="styles.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
  <div class="container">
    
    <main class="main-content">
      <header>
        <h1>Pomodoro Timer</h1>
        <div class="dark-mode-toggle">
          <label class="switch">
            <input type="checkbox" id="darkModeToggle" />
            <span class="slider round"></span>
          </label>
          <span>Dark Mode</span>
        </div>
      </header>

      
      <section class="timer-section">
        <div class="buttons-vertical-group" id="sessionTypeButtons">
          <button class="session-btn active" data-session="work">Work (25 min)</button>
          <button class="session-btn" data-session="short_break">Short Break (5 min)</button>
          <button class="session-btn" data-session="long_break">Long Break (15 min)</button>
        </div>

      
        <div class="task-selector">
          <label for="taskSelect">Select Task:</label>
          <select id="taskSelect">
            <option value="">-- No Task Selected --</option>
            <?php foreach ($tasks as $task): ?>
              <option value="<?= $task['Task_ID'] ?>"><?= htmlspecialchars($task['Title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="timer-main">
          <div id="timer">25:00</div>
          <div class="controls">
            <button id="startBtn">Start</button>
            <button id="stopBtn" disabled>Stop</button>
          </div>
          <div id="message" class="message-section"></div>
        </div>
      </section>
    </main>
  </div>

  <script>
    const startBtn = document.getElementById("startBtn");
    const taskSelect = document.getElementById("taskSelect");
    const messageDiv = document.getElementById("message");

    startBtn.addEventListener("click", () => {
      const taskID = taskSelect.value;
      const sessionType = document.querySelector(".session-btn.active").dataset.session;
      const duration = document.getElementById("timer").innerText;

      if (!taskID) {
        messageDiv.innerText = "⚠️ Please select a task!";
        return;
      }

      
      fetch('save_session.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
          task_id: taskID,
          user_id: 1, 
          session_type: sessionType,
          duration: duration,
          custom_work: 25,
          custom_break: 5
        })
      })
      .then(res => res.text())
      .then(data => {
        messageDiv.innerText = data;
      });
    });
  </script>
</body>
</html>
