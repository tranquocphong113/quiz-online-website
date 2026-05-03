const API_URL = "./api.php";

async function request(action, data = {}) {
  try {
    const response = await fetch(API_URL, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ action, ...data }),
    });

    if (!response.ok) {
      return {
        success: false,
        message: `Lỗi máy chủ: ${response.status}`,
      };
    }

    return await response.json();
  } catch (error) {
    console.error("API request error:", error);
    return {
      success: false,
      message: error.message || "Lỗi kết nối máy chủ",
    };
  }
}

export const api = {
  async register(name, email, password) {
    return request("register", { name, email, password });
  },

  async login(email, password) {
    return request("login", { email, password });
  },

  async createRoom(quiz, hostName) {
    return request("createRoom", { quiz, hostName });
  },

  async joinRoom(code, playerName) {
    return request("joinRoom", { code, playerName });
  },

  async saveScore(data) {
    return request("saveScore", data);
  },

  async getScoresByRoom(roomCode) {
    const result = await request("getScoresByRoom", { roomCode });
    return result.success ? result.scores : [];
  },

  async getHistoryByUser(email) {
    const result = await request("getHistoryByUser", { email });
    return result.success ? result.history : [];
  },
};
