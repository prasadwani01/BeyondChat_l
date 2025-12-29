require('dotenv').config();
const { GoogleGenerativeAI } = require("@google/generative-ai");

async function checkModels() {
    console.log("🔑 Checking API Key...");
    
    if (!process.env.GEMINI_API_KEY) {
        console.error("❌ ERROR: Missing GEMINI_API_KEY in .env file");
        return;
    }

    try {
        const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);
        // This is a special fallback model string that usually works for testing
        const model = genAI.getGenerativeModel({ model: "gemini-1.5-flash" });

        console.log("🤖 Attempting to generate text with 'gemini-1.5-flash'...");
        const result = await model.generateContent("Test connection.");
        const response = await result.response;
        console.log("✅ SUCCESS! 'gemini-1.5-flash' is working.");
        console.log("📝 Response:", response.text());

    } catch (error) {
        console.error("\n❌ MODEL FAILED. Details:");
        console.error(error.message);
        
        console.log("\n🕵️ TRYING ALTERNATIVE MODELS...");
        
        const alternatives = ["gemini-pro", "gemini-1.5-pro", "gemini-1.0-pro"];
        
        for (const name of alternatives) {
            try {
                process.stdout.write(`   👉 Testing '${name}'... `);
                const altModel = genAI.getGenerativeModel({ model: name });
                await altModel.generateContent("Test");
                console.log("✅ WORKS!");
                console.log(`\n🎉 ACTION ITEM: Change your index.js to use: "${name}"`);
                return;
            } catch (err) {
                console.log("❌ Failed");
            }
        }
    }
}

checkModels();