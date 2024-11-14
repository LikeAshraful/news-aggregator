<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\UserPreference;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->first();

        return response()->json([
            'status' => true,
            'message' => 'User preferences retrieved successfully',
            'data' => $preferences,
        ], 200);
        
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_sources' => $request->input('preferred_sources', []),
                'preferred_categories' => $request->input('preferred_categories', []),
                'preferred_authors' => $request->input('preferred_authors', [])
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'User preferences updated successfully',
            'data' => $preferences,
        ], 200);
    }


    public function personalizedFeed()
    {
        $user = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->first();

        if (!$preferences) {
            return response()->json([
                'status' => false,
                'message' => 'User preferences not found',
            ], 404);
        }

        $articles = Article::query();

        if ($preferences->preferred_sources) {
            $articles->whereIn('source', $preferences->preferred_sources);
        }

        if ($preferences->preferred_categories) {
            $articles->whereIn('category', $preferences->preferred_categories);
        }

        if ($preferences->preferred_authors) {
            $articles->whereIn('author', $preferences->preferred_authors);
        }

        $paginatedArticles = $articles->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Personalized feed retrieved successfully',
            'data' => $paginatedArticles,
        ], 200);
    }
    
}
