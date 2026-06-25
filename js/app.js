import { showScreen, setupNavigation } from "./ui.js";
import { setupAuth } from "./auth.js";
import { setupQuizCreate } from "./quiz-create.js";
import { setupRoom, stopPlayerPolling, stopRoomStatusPolling } from "./room.js";
import { setupGame, stopQuizTimer, goToPlayQuiz } from "./gameplay.js";
import { setupResult, showResult, stopLeaderboardPolling } from "./result.js";
import { api } from "../model/api.js";
import { appState } from "../model/state.js";

function cleanupAll() {
  stopPlayerPolling();
  stopRoomStatusPolling();
  stopLeaderboardPolling();
  stopQuizTimer();
}

setupNavigation(cleanupAll);
setupAuth(cleanupAll);
setupQuizCreate();
setupRoom();
setupGame(showResult);
setupResult();

showScreen("screen-auth");

let selectedPresetQuiz = null;

async function loadPresetQuizzes() {
  const container = document.getElementById("presetQuizList");
  if (!container) return;

  const result = await api.getPresetQuizzes();
  if (!result.success || !result.quizzes || result.quizzes.length === 0) {
    return;
  }

  container.innerHTML = "";

  const gradients = [
    "linear-gradient(135deg, #4F46E5, #7C3AED)",
    "linear-gradient(135deg, #10B981, #059669)",
    "linear-gradient(135deg, #F59E0B, #D97706)",
    "linear-gradient(135deg, #EC4899, #E11D48)",
  ];

  result.quizzes.forEach((quiz, index) => {
    const div = document.createElement("div");
    div.className = "card";
    div.style.background = gradients[index % gradients.length];
    div.style.color = "white";
    div.innerHTML = `<span style="text-shadow: 1px 1px 3px rgba(0,0,0,0.2);">${quiz.title}</span>`;

    div.addEventListener("click", () => {
      selectedPresetQuiz = quiz;

      document.getElementById("confirmQuizTitle").textContent = quiz.title;
      document.getElementById("confirmQuizCount").textContent =
        quiz.questionCount || quiz.questions.length;
      document.getElementById("confirmQuizTime").textContent =
        quiz.time_limit || quiz.timeLimit || 0;

      document.getElementById("confirmModal").classList.remove("hidden");
    });

    container.appendChild(div);
  });
}

const btnCancel = document.getElementById("btnCancelPreset");
const btnConfirm = document.getElementById("btnConfirmPreset");

if (btnCancel) {
  btnCancel.addEventListener("click", () => {
    document.getElementById("confirmModal").classList.add("hidden");
    selectedPresetQuiz = null;
  });
}

if (btnConfirm) {
  btnConfirm.addEventListener("click", () => {
    if (selectedPresetQuiz) {
      document.getElementById("confirmModal").classList.add("hidden"); // Tắt popup

      appState.currentQuiz = selectedPresetQuiz;
      appState.currentRoom = null;

      cleanupAll();

      if (typeof goToPlayQuiz === "function") {
        goToPlayQuiz();
      } else {
        showScreen("screen-play-quiz");
        console.warn("Chưa import hàm goToPlayQuiz từ gameplay.js");
      }
    }
  });
}

loadPresetQuizzes();
