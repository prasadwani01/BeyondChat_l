<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Article;

class ArticleController extends Controller
{
    // 1. Fetch all articles
    public function index()
    {
        return Article::all();
    }

    // 2. Fetch only pending articles for the Worker
    // 🔴 RENAMED from 'pending' to 'getPending' to match your api.php file
    public function getPending()
    {
        return response()->json(Article::where('status', 'pending')->get());
    }

    // 3. Save the AI enhanced content
    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        $article->update([
            'enhanced_content' => $request->enhanced_content,
            'status' => 'completed'
        ]);

        return response()->json(['message' => 'Article updated successfully']);
    }
// 4. Trigger the Node.js Worker (FIXED with Full Path)
public function triggerScan()
    {
        $workerPath = "E:\\internship\\BeyondChats\\worker";
        $nodePath = "C:\\nvm4w\\nodejs\\node.exe";

        // COMMAND EXPLANATION:
        // 1. "cd /d" -> Move to worker folder
        // 2. "start /B" -> Launch Node in background
        // 3. "> NUL 2>&1" -> Silence output so PHP doesn't listen to it
        // We wrap the whole thing in pclose(popen(...)) to cut the connection immediately.
        
        $command = "cd /d {$workerPath} && start /B \"\" \"{$nodePath}\" index.js > NUL 2>&1";

        pclose(popen($command, "r"));

        return response()->json([
            'message' => 'Scan started in background.',
        ]);
    }
}