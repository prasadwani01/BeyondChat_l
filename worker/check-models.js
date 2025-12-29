const axios = require('axios');
require('dotenv').config();

async function getAvailableModels() {
    const key = process.env.GEMINI_API_KEY;
    if (!key) {
        console.log("❌ Error: GEMINI_API_KEY is missing in .env");
        return;
    }

    console.log("📡 Connecting to Google API to list models...");
    const url = `https://generativelanguage.googleapis.com/v1beta/models?key=${key}`;

    try {
        const response = await axios.get(url);
        console.log("✅ CONNECTION SUCCESSFUL!");
        console.log("\n👇 THESE ARE THE EXACT MODEL NAMES YOU CAN USE:");
        
        const models = response.data.models || [];
        const generateModels = models.filter(m => m.supportedGenerationMethods.includes("generateContent"));

        if (generateModels.length === 0) {
            console.log("⚠️ No text-generation models found. Check your API Key permissions.");
        } else {
            generateModels.forEach(m => {
                // We strip 'models/' to show you exactly what to put in your code
                console.log(`   👉 "${m.name.replace('models/', '')}"`);
            });
        }

    } catch (error) {
        console.error("❌ FAILED TO LIST MODELS:");
        console.error(error.response ? error.response.data : error.message);
    }
}

getAvailableModels();