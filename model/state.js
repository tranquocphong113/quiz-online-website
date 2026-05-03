export const appState = {
  currentUser: null,

  currentQuiz: {
    title: "",
    timeLimit: 5,
    questions: [],
  },

  currentRoom: {
    code: "",
    players: [],
  },

  game: {
    currentQuestionIndex: 0,
    score: 0,
    selected: false,
    timeLeft: 0,
    timerId: null,
    isFinished: false,
  },
};
