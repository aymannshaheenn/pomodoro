

document.addEventListener("DOMContentLoaded", function () {
  const timerDisplay = document.getElementById("timer");
  const startBtn = document.getElementById("startBtn");
  const stopBtn = document.getElementById("stopBtn");
  const sessionButtons = document.querySelectorAll(".session-btn");
  const taskForm = document.getElementById("taskForm");
  const taskInput = document.getElementById("taskTitle");
  const taskList = document.getElementById("taskList");
  const messageBox = document.getElementById("message");
  const darkModeToggle = document.getElementById("darkModeToggle");
  const tickSound = new Audio("tick.mp3");
  const endSound = new Audio("session-end.mp3");
  const workInput = document.getElementById("work-minutes");
  const breakInput = document.getElementById("break-minutes");

  let timer;
  let timeRemaining = 25 * 60;
  let isRunning = false;
  let currentSession = "work";
  let currentTaskId = 1;
  const userId = 1; 

  function updateTimerDisplay() {
    const minutes = String(Math.floor(timeRemaining / 60)).padStart(2, "0");
    const seconds = String(timeRemaining % 60).padStart(2, "0");
    if (timerDisplay) timerDisplay.textContent = `${minutes}:${seconds}`;
  }

  function applyCustomDurations() {
    const workVal = parseInt(workInput?.value);
    const breakVal = parseInt(breakInput?.value);
    if (!isNaN(workVal) && currentSession === "work") {
      timeRemaining = workVal * 60;
    } else if (!isNaN(breakVal) && currentSession !== "work") {
      timeRemaining = breakVal * 60;
    }
  }

  function resetTimeBySession(type) {
    currentSession = type;
    if (type === "short_break") {
      timeRemaining = 5 * 60;
      document.body.className = "break-theme";
    } else if (type === "long_break") {
      timeRemaining = 15 * 60;
      document.body.className = "long-break-theme";
    } else {
      timeRemaining = 25 * 60;
      document.body.className = "work-theme";
    }
    applyCustomDurations();
    updateTimerDisplay();
    if (messageBox) messageBox.textContent = "";
  }

  function startTimer() {
    if (isRunning) return;
    isRunning = true;
    startBtn.disabled = true;
    stopBtn.disabled = false;
    if (messageBox) messageBox.textContent = "";

    timer = setInterval(() => {
      if (timeRemaining > 0) {
        timeRemaining--;
        if (timeRemaining <= 5 && timeRemaining > 0) {
          tickSound.play().catch(() => {});
        }
        updateTimerDisplay();
      } else {
        clearInterval(timer);
        isRunning = false;
        startBtn.disabled = false;
        stopBtn.disabled = true;
        endSound.play();

        if (messageBox) {
          messageBox.textContent =
            currentSession === "work"
              ? "🎉 Great job! Time for a break!"
              : "🧠 Break over! Ready to get back to work?";
        }

        saveSessionToDB(currentTaskId, currentSession, getDuration(), getCustomWork(), getCustomBreak());
        unlockAchievementDB(1);
        displayAchievementsFromDB();
      }
    }, 1000);
  }

  function stopTimer() {
    clearInterval(timer);
    isRunning = false;
    startBtn.disabled = false;
    stopBtn.disabled = true;
    if (messageBox) messageBox.textContent = "⏹ Timer stopped.";
  }

  function getDuration() {
    if (currentSession === "work") {
      return parseInt(workInput?.value || 25);
    } else {
      return parseInt(breakInput?.value || 5);
    }
  }

  function getCustomWork() {
    return parseInt(workInput?.value || 25);
  }

  function getCustomBreak() {
    return parseInt(breakInput?.value || 5);
  }

  function saveSessionToDB(taskId, sessionType, duration, customWork, customBreak) {
    fetch("save_session.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        user_id: userId,
        task_id: taskId,
        session_type: sessionType,
        duration: duration,
        custom_work: customWork,
        custom_break: customBreak
      })
    });
  }

  function unlockAchievementDB(id) {
    fetch("unlock_achievement.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: id, user_id: userId })
    });
  }

  function displayAchievementsFromDB() {
    fetch(`get_achievements.php?user_id=${userId}`)
      .then(res => res.json())
      .then(achievements => {
        const list = document.getElementById("achievementList");
        if (!list) return;
        list.innerHTML = "";
        achievements.forEach(a => {
          const item = document.createElement("div");
          item.className = `achievement ${a.Unlocked ? "unlocked" : ""}`;
          item.innerHTML = `<h3>${a.Title}</h3><p>${a.Description}</p>`;
          list.appendChild(item);
        });
      });
  }

  sessionButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      sessionButtons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      const session = btn.getAttribute("data-session");
      resetTimeBySession(session);
      stopTimer();
    });
  });

  if (taskForm) {
    taskForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const taskText = taskInput.value.trim();
      if (taskText === "") return;

      const li = document.createElement("li");
      li.classList.add("task-item");
      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.addEventListener("change", () => {
        li.classList.toggle("completed");
      });
      li.appendChild(checkbox);
      li.appendChild(document.createTextNode(taskText));
      taskList.appendChild(li);
      taskInput.value = "";

      if (messageBox) {
        messageBox.textContent = "✅ Task added!";
        setTimeout(() => (messageBox.textContent = ""), 2000);
      }
    });
  }

  if (startBtn) startBtn.addEventListener("click", startTimer);
  if (stopBtn) stopBtn.addEventListener("click", stopTimer);

  if (darkModeToggle) {
    darkModeToggle.addEventListener("change", () => {
      document.body.classList.toggle("dark", darkModeToggle.checked);
    });
  }

  resetTimeBySession(currentSession);
  displayAchievementsFromDB();
});
function fetchTasks() {
  fetch("tasks.php")
    .then(res => res.json())
    .then(tasks => {
      taskList.innerHTML = "";
      tasks.forEach(task => {
        const li = document.createElement("li");
        li.className = "task-item";
        
        if (task.Is_completed == 1) li.classList.add("completed");

       
        if (task.Priority === "High") {
          li.style.borderLeft = "4px solid #e74c3c";
        } else if (task.Priority === "Medium") {
          li.style.borderLeft = "4px solid #f39c12";
        } else if (task.Priority === "Low") {
          li.style.borderLeft = "4px solid #2ecc71";
        }

        const label = document.createElement("label");
        label.textContent = `[${task.Priority}] ${task.Title} (${task.Estimated_sessions} sessions, ${task.Estimated_time} min)`;
        li.appendChild(label);

        taskList.appendChild(li);
      });
    });
}

window.onload = fetchTasks;
