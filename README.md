# 🚀 BeyondChats - Full Stack AI Assignment

A full-stack application that scrapes articles, enhances them using **Google Gemini AI**, and displays them in a responsive React dashboard.

## 🌟 Features
- **Laravel Backend**: REST API to manage articles.
- **Node.js Worker**: Background service that scrapes web content and uses LLMs to rewrite articles.
- **React Frontend**: Modern dashboard with real-time "Scan" functionality.
- **AI Integration**: Uses Google Gemini 2.0 Flash for content generation.
- **Architecture**: Decoupled background processing for better performance.

## 🛠️ Tech Stack
- **Frontend:** React + Vite, Axios
- **Backend:** Laravel 11, PHP 8.2+
- **Database:** MySQL
- **AI/ML:** Google Gemini API
- **Scraping:** Cheerio, Axios

---

## ⚙️ Local Setup Instructions

### Prerequisites
- PHP 8.2+ & Composer
- Node.js & NPM
- MySQL

### 1. Clone the Repository
```bash
git clone https://github.com/prasadwani01/BeyondChat_l.git
cd BeyondChat_l

2. Backend Setup (Laravel)
Open a terminal in the backend folder:

Bash

cd backend
composer install
cp .env.example .env
Action: Open .env and configure your Database settings (DB_DATABASE, DB_USERNAME, etc.).

Run Migrations:

Bash

php artisan migrate
php artisan serve
(Keep this terminal running)

3. Worker Setup (Node.js)
Open a new terminal in the worker folder:

Bash

cd worker
npm install
Action: Create a .env file in the worker folder and add your API key:

Code snippet

GEMINI_API_KEY=your_google_gemini_api_key_here
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=beyondchats_db
4. Frontend Setup (React)
Open a new terminal in the frontend folder:

Bash

cd frontend
npm install
npm run dev
📖 Usage
Open the dashboard at http://localhost:5173.

You will see a list of articles.

Click the "⚡ Scan for New Articles" button.

Wait approx. 15-20 seconds. The background worker is scraping Google and generating AI content.

The dashboard will automatically update to show the enhanced content with citations.

📂 Project Structure
Plaintext

BeyondChats/
├── backend/          # Laravel API (Controller, Models, Migrations)
├── frontend/         # React.js Application (Vite, Axios)
├── worker/           # Node.js Script (Scraper, Gemini AI)
├── screenshots/      # Images for documentation
└── README.md         # Documentation
🛡️ License
This project is submitted for evaluation purposes.


2.  **Paste it** into your `README.md`.
3.  **Manually Edit the Links:** After pasting, look for `YOUR_YOUTUBE_OR_LOOM_LIN
