# 🏗️ System Architecture

This project follows a **Microservices-inspired** architecture using Laravel, Node.js, and React.

```mermaid
graph TD
    User((User))
    Frontend[react-frontend]
    Backend[laravel-backend]
    DB[(MySQL Database)]
    Worker[node-worker]
    Google[Google Search]
    Gemini[Gemini AI]

    User -- "1. Views Dashboard" --> Frontend
    User -- "2. Clicks Scan" --> Frontend
    
    Frontend -- "3. API Request" --> Backend
    Backend -- "4. Store Articles" --> DB
    Backend -- "5. Trigger Process" --> Worker
    
    Worker -- "6. Fetch Pending" --> DB
    Worker -- "7. Scrape Context" --> Google
    Worker -- "8. Generate Content" --> Gemini
    Worker -- "9. Update Article" --> DB
    
    Frontend -- "10. Poll for Updates" --> Backend
    Backend -- "11. Return Data" --> Frontend