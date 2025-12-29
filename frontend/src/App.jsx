import { useState, useEffect } from 'react';
import axios from 'axios';
import './App.css';

function App() {
  const [articles, setArticles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedArticle, setSelectedArticle] = useState(null);
  const [isScanning, setIsScanning] = useState(false);

  useEffect(() => {
    axios.get('http://127.0.0.1:8000/api/articles')
      .then(response => {
        setArticles(response.data);
        setLoading(false);
      })
      .catch(error => console.error("Error:", error));
  }, []);
const handleScan = async () => {
    // 1. Alert works, so we know this runs
    alert("🚀 Starting AI Scan...");
    setIsScanning(true);

    try {
      console.log("📡 Attempting to connect to backend...");
      
      // 2. We use 'fetch' instead of 'axios'
      const response = await fetch('http://127.0.0.1:8000/api/trigger-scan', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      });

      console.log("✅ Server responded with status:", response.status);

      if (!response.ok) {
        throw new Error(`Server Error: ${response.status}`);
      }

      // 3. Refresh the articles immediately after
      const data = await response.json();
      console.log("📄 Output:", data);
      
      alert("✅ Scan signal sent! Waiting for results...");
      
      // Wait 5 seconds, then refresh the list
      setTimeout(async () => {
         window.location.reload(); // Simple refresh to see new data
      }, 5000);

    } catch (error) {
      console.error("❌ CONNECTION FAILED:", error);
      alert(`Connection Failed: ${error.message}`);
    } finally {
      setIsScanning(false);
    }
  };

  return (
    <div className="container">
      <header>
        <h1>Content Intelligence Dashboard</h1>
        <p>Automated Research & Enhancement Engine</p>
        <div style={{ textAlign: 'center', marginBottom: '20px' }}>
        <button 
          onClick={handleScan} 
          disabled={isScanning}
          style={{
            padding: '10px 20px',
            fontSize: '16px',
            backgroundColor: isScanning ? '#ccc' : '#007bff',
            color: 'white',
            border: 'none',
            borderRadius: '5px',
            cursor: isScanning ? 'not-allowed' : 'pointer'
          }}
        >
          {isScanning ? '🤖 Scanning...' : '⚡ Scan for New Articles'}
        </button>
      </div>
      </header>

      {loading ? (
        <div style={{textAlign: 'center', marginTop: '50px'}}>Loading...</div>
      ) : (
        <div className="grid">
          {articles.map(article => (
            <div key={article.id} className="card" onClick={() => setSelectedArticle(article)}>
              <div className="card-header-gradient"></div>
              <div className="card-body">
                <span className={`status ${article.status}`}>
                </span>
                <h3>{article.title}</h3>
                <div className="card-footer">
                  View Analysis &rarr;
                </div>
              </div>
            </div>
          ))}
        </div>
      )}

      {selectedArticle && (
        <div className="modal-overlay" onClick={() => setSelectedArticle(null)}>
          <div className="modal-content" onClick={e => e.stopPropagation()}>
            
            <div className="modal-header">
              <h2>{selectedArticle.title}</h2>
              <button className="close-btn" onClick={() => setSelectedArticle(null)}>Close</button>
            </div>

            <div className="modal-body">
              {/* Left Side: Original */}
              <div className="split-view original">
                <h3>Original Source</h3>
                <div className="text-content">
                  {selectedArticle.original_content 
                    ? selectedArticle.original_content 
                    : "No content available."}
                </div>
              </div>

              {/* Right Side: Enhanced */}
              <div className="split-view enhanced">
                <h3>✨ Enhanced Version</h3>
                <div className="text-content">
                  {selectedArticle.enhanced_content 
                    ? selectedArticle.enhanced_content 
                    : "Waiting for AI processing..."}
                </div>
              </div>
            </div>

          </div>
        </div>
      )}
    </div>
  );
}

export default App;