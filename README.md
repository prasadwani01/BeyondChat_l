# 🚀 BeyondChats - Full Stack AI Assignment

A full-stack web application that aggregates articles, enhances them using **Google Gemini 2.0 AI**, and displays them in a real-time reactive dashboard.

Built as part of the Full Stack Web Developer Intern assignment at BeyondChats.

---

## 🎥 Live Demo
> **[Click here to watch the Project Demo Video](YOUR_YOUTUBE_OR_LOOM_LINK_HERE)**  
> *(Or access the live app here: [YOUR_NGROK_LINK_HERE])*

---

## 🏗️ System Architecture

This project follows a decoupled **Microservices-inspired architecture** to handle heavy background processing without blocking the user interface.

![Architecture Diagram](./screenshots/diagram.png)  
*(See `ARCHITECTURE.md` for detailed data flow)*

### 🔄 The Workflow
1. **User** clicks "Scan" on the React Dashboard.
2. **Laravel API** receives the request and triggers a background Node.js worker.
3. **Node.js Worker**:
   - Fetches pending articles from MySQL.
   - Scrapes context from Google Search.
   - Rewrites content using **Google Gemini 2.0 Flash**.
   - Updates the database.
4. **React Frontend** polls the server and automatically updates the UI when the AI finishes.

---

## 🛠️ Tech Stack

| Component | Technology | Role |
|---------|------------|------|
| **Frontend** | React.js + Vite | Responsive Dashboard UI |
| **Backend** | Laravel 11 (PHP 8.2) | REST API & Process Management |
| **Worker** | Node.js | Web Scraping & AI Integration |
| **Database** | MySQL | Data Persistence |
| **AI Model** | Google Gemini 2.0 Flash | Content Generation |
| **Tools** | Axios, Cheerio, Dotenv | HTTP Requests & Environment Mgmt |

---

## ⚙️ Installation & Setup Guide

Follow these steps to run the project locally.

### Prerequisites
- Node.js & NPM
- PHP 8.2+ & Composer
- MySQL Server

---

### 1. Clone the Repository

```bash
git clone https://github.com/prasadwani01/BeyondChat_l.git
cd BeyondChat_l
2. Backend Setup (Laravel)
Open a terminal in the backend folder:

bash
Copy code
cd backend
composer install
cp .env.example .env
Action:
Open .env and configure your database settings:

env
Copy code
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
Run migrations and start the server:

bash
Copy code
php artisan migrate
php artisan serve
(Keep this terminal running)

3. Worker Setup (Node.js)
Open a new terminal in the worker folder:

bash
Copy code
cd worker
npm install
Action: Create a .env file in the worker folder:

env
Copy code
GEMINI_API_KEY=your_google_gemini_api_key_here
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=beyondchats_db
4. Frontend Setup (React)
Open a new terminal in the frontend folder:

bash
Copy code
cd frontend
npm install
npm run dev
📖 Usage
Open the dashboard at http://localhost:5173

You will see a list of articles.

Click the ⚡ Scan for New Articles button.

Wait approximately 15–20 seconds while the background worker scrapes Google and generates AI-enhanced content.

The dashboard will automatically update with enhanced content and citations.

📂 Project Structure
text
Copy code
BeyondChats/
├── backend/          # Laravel API (Controllers, Models, Migrations)
├── frontend/         # React.js Application (Vite, Axios)
├── worker/           # Node.js Script (Scraper, Gemini AI)
├── screenshots/      # Images for documentation
└── README.md         # Project Documentation
🛡️ License
This project is submitted strictly for evaluation purposes.

markdown
Copy code

If you want, I can also:
- Optimize it for **GitHub readability**
- Add **badges** (Node, Laravel, React, MySQL)
- Rewrite it to sound more **internship / resume-ready**
- Add a **Features** or **Future Improvements** section

Just tell me 👍
