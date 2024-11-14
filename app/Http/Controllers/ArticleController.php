<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller {
    public function index(Request $request) {

        $articles = Article::query();

        if ($request->has('keyword')) {
            $articles->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('content', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('category')) {
            $articles->where('category', $request->category);
        }

        if ($request->has('source')) {
            $articles->where('source', $request->source);
        }

        if ($request->has('date')) {
            $articles->whereDate('created_at', $request->date);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Articles retrieved successfully',
            'data'    => $articles->get(),
        ], 200);
    }

    public function show($id) {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'status'  => false,
                'message' => 'Article not found',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Article retrieved successfully',
            'data'    => $article,
        ], 200);
    }

}
