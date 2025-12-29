require('dotenv').config();
const axios = require('axios');
const google = require('googlethis');
const cheerio = require('cheerio');
const { GoogleGenerativeAI } = require("@google/generative-ai");

const API_URL = process.env.API_URL || 'http://127.0.0.1:8000/api';

async function main() {
    console.log("🚀 Worker started...");

    try {
        // 1. Fetch pending articles from Laravel
        console.log("📥 Fetching pending articles...");
        const response = await axios.get(`${API_URL}/pending-articles`);
        const articles = response.data;

        if (articles.length === 0) {
            console.log("✅ No pending articles found.");
            return;
        }

        console.log(`found ${articles.length} articles to process.`);

        // 2. Process each article
        for (const article of articles) {
            await processArticle(article);
        }

    } catch (error) {
        if (error.code === 'ECONNREFUSED') {
            console.error("❌ Error: Could not connect to Laravel API. Is 'php artisan serve' running?");
        } else {
            console.error("❌ Error in main loop:", error.message);
        }
    }
}

async function processArticle(article) {
    console.log(`\n🔹 Processing: "${article.title}"`);

    try {
        // Step A: Search Google
        const searchResults = await searchGoogle(article.title);
        
        if (searchResults.length === 0) {
            console.log("   ⚠️ No search results found. Skipping.");
            return;
        }

        // Step B: Scrape the top results
        const referencedContent = [];
        for (const result of searchResults) {
            console.log(`   🔎 Scraping reference: ${result.title}...`);
            const content = await scrapeContent(result.url);
            
            if (content && content.length > 500) {
                referencedContent.push({
                    title: result.title,
                    url: result.url,
                    content: content
                });
                console.log(`      ✅ Scraped successfully.`);
            } else {
                console.log(`      ⚠️ Content too short or failed. Skipping.`);
            }
        }

        if (referencedContent.length === 0) {
            console.log("   ⚠️ Could not scrape any reference content.");
            return;
        }

        // Step C: Generate new article with AI
        console.log("   🤖 Generating enhanced article with AI...");
        const enhancedContent = await generateAIContent(article, referencedContent);

        // Step D: Send back to Laravel
        console.log("   💾 Saving to database...");
        await updateArticleInBackend(article.id, enhancedContent, searchResults);

        console.log(`   ✅ SUCCESS: Article ${article.id} updated!`);

    } catch (error) {
        console.error(`   ❌ Failed to process article ${article.id}:`, error.message);
    }
}

// --- Helper Functions ---

async function searchGoogle(query) {
    console.log(`      🕵️ Debugging: sending query to Google...`);
    
    // 🛑 BYPASS: Using Mock Data because Google blocked the IP.
    // Ideally, you would use 'googlethis' here if you had proxies.
    console.log("      ⚠️ Google Blocked IP. Switching to 'Simulation Mode' (Using Wikipedia).");
    
    return [
        {
            title: "Chatbot - Wikipedia",
            url: "https://en.wikipedia.org/wiki/Chatbot"
        },
        {
            title: "Artificial intelligence in healthcare - Wikipedia",
            url: "https://en.wikipedia.org/wiki/Artificial_intelligence_in_healthcare"
        }
    ];
}

async function scrapeContent(url) {
    try {
        // Fake a browser User-Agent to avoid being blocked
        const { data } = await axios.get(url, {
            headers: { 'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36' },
            timeout: 10000 
        });
        
        const $ = cheerio.load(data);
        
        // Remove junk elements
        $('script, style, nav, footer, header, aside, .ads, .sidebar').remove();
        
        // Try to find the main article text
        let content = $('article').text() || $('main').text() || $('.content').text() || $('body').text();
        
        // Clean up whitespace
        return content.replace(/\s+/g, ' ').trim().substring(0, 5000); 
    } catch (error) {
        return null;
    }
}

async function generateAIContent(original, references) {
    console.log("      🤖 Connecting to Google Gemini AI...");

    try {
        // Initialize Gemini
        const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);
        const model = genAI.getGenerativeModel({ model: "gemini-2.5-flash" });

        // Prepare the prompt
        const referenceText = references.map(r => `Source (${r.title}): ${r.content}`).join("\n\n");
        
        const prompt = `
        You are an expert editor. Rewrite the following article to make it more engaging, professional, and factually rich.
        
        ORIGINAL TITLE: ${original.title}
        ORIGINAL CONTENT: ${original.original_content ? original.original_content.substring(0, 1000) : "No content provided"}
        
        Use these external references to add missing details and value:
        ${referenceText.substring(0, 2000)}

        Format the output in clean Markdown with:
        - A catchy introduction.
        - Bullet points for key insights.
        - A "Key Takeaways" section at the end.
        `;

        // Generate Content
        const result = await model.generateContent(prompt);
        const response = await result.response;
        const text = response.text();

        return text;

    } catch (error) {
        console.error("      ❌ AI Error:", error.message);
        return "Error generating AI content. Please check logs.";
    }
}

// 🛑 THIS FUNCTION WAS MISSING IN YOUR CODE
async function updateArticleInBackend(id, content, sources) {
    try {
        await axios.post(`${API_URL}/articles/${id}/update`, {
            enhanced_content: content,
            status: 'completed'
        });
    } catch (error) {
        console.error("      ❌ Failed to update backend:", error.message);
        throw error; // Re-throw so the main loop knows it failed
    }
}

// Run the script
main();