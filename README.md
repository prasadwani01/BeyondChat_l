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
```
### 2. Backend Setup (Laravel)
Open a terminal in the `backend` folder:
```bash
cd backend
composer install
cp .env.example .env
```
Run Migrations:
```bash
php artisan migrate
php artisan serve
```
3. Worker Setup (Node.js)
Open a new terminal in the worker folder:
```bash
cd worker
npm install
```
Action: Create a .env file in the worker folder and add your API key:
```Code snippet
GEMINI_API_KEY=your_google_gemini_api_key_here
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=beyondchats_db
```
4. Frontend Setup (React)
Open a new terminal in the frontend folder:

```bash
cd frontend
npm install
npm run dev
```
---

### **PART 3: Usage, Structure, & License**
*(Paste this at the very end)*

```markdown
---

## 📖 Usage
1.  Open the dashboard at `http://localhost:5173`.
2.  [cite_start]You will see a list of articles[cite: 9, 24].
3.  Click the **"⚡ Scan for New Articles"** button.
4.  Wait approx. **15-20 seconds**. The background worker is scraping Google and generating AI content.
5.  [cite_start]The dashboard will **automatically update** to show the enhanced content with citations[cite: 22, 25].

---
```
## 📂 Project Structure
```text
BeyondChats/
├── backend/          # Laravel API (Controller, Models, Migrations)
├── frontend/         # React.js Application (Vite, Axios)
├── worker/           # Node.js Script (Scraper, Gemini AI)
├── screenshots/      # Images for documentation
└── README.md         # Documentation

```

### **Final Step:**
After pasting all three parts, save the file. You are now ready to commit and push!
