const USER_KEY = "quiz_users";
const ROOM_KEY = "quiz_room";
const SCORE_KEY = "quiz_scores";

function readStorage(key, defaultValue) {
  const data = localStorage.getItem(key);
  return data ? JSON.parse(data) : defaultValue;
}

function writeStorage(key, value) {
  localStorage.setItem(key, JSON.stringify(value));
}

function createRoomCode() {
  return Math.random().toString(36).substring(2, 8).toUpperCase();
}

export const api = {
  register(name, email, password) {
    const users = readStorage(USER_KEY, []);

    const existed = users.find((user) => user.email === email);

    if (existed) {
      return {
        success: false,
        message: "Email đã tồn tại",
      };
    }

    users.push({
      name,
      email,
      password,
    });

    writeStorage(USER_KEY, users);

    return {
      success: true,
    };
  },

  login(email, password) {
    const users = readStorage(USER_KEY, []);

    const user = users.find(
      (user) => user.email === email && user.password === password,
    );

    if (!user) {
      return {
        success: false,
        message: "Sai email hoặc mật khẩu",
      };
    }

    return {
      success: true,
      user,
    };
  },

  createRoom(quiz, hostName) {
    const room = {
      code: createRoomCode(),
      quiz: JSON.parse(JSON.stringify(quiz)),
      players: [hostName],
    };

    writeStorage(ROOM_KEY, room);

    return {
      success: true,
      room,
    };
  },

  joinRoom(code, playerName) {
    const room = readStorage(ROOM_KEY, null);

    if (!room || room.code !== code) {
      return {
        success: false,
        message: "Không tìm thấy phòng",
      };
    }

    if (!room.players.includes(playerName)) {
      room.players.push(playerName);
    }

    writeStorage(ROOM_KEY, room);

    return {
      success: true,
      room,
    };
  },

  saveScore(data) {
    const scores = readStorage(SCORE_KEY, []);

    const percent =
      data.total > 0 ? Math.round((data.score / data.total) * 100) : 0;

    scores.push({
      id: Date.now(),
      name: data.name,
      email: data.email,
      roomCode: data.roomCode,
      quizTitle: data.quizTitle,
      score: data.score,
      total: data.total,
      percent: percent,
      time: new Date().toLocaleString("vi-VN"),
    });

    writeStorage(SCORE_KEY, scores);
  },

  getScoresByRoom(roomCode) {
    const scores = readStorage(SCORE_KEY, []);
    return scores.filter((item) => item.roomCode === roomCode);
  },

  getHistoryByUser(email) {
    const scores = readStorage(SCORE_KEY, []);
    return scores.filter((item) => item.email === email);
  },
};
