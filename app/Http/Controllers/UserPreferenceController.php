<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller {   

    /**
     * @OA\SecurityScheme(
     *     securityScheme="sanctum",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="Use Sanctum-generated Bearer Token for authentication"
     * )
     */

    /**
     * @OA\PathItem(
     *     path="/user/preferences"
     * )
     */

    /**
     * @OA\Get(
     *     path="/user/preferences",
     *     summary="Get user preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User preferences retrieved successfully",
     *     )
     * )
     */
    public function index() {
        $user        = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->first();

        return response()->json([
            'status'  => true,
            'message' => 'User preferences retrieved successfully',
            'data'    => $preferences,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/user/preferences",
     *     summary="Update user preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="preferred_sources", type="array", items=@OA\Items(type="string"), description="List of preferred sources"),
     *             @OA\Property(property="preferred_categories", type="array", items=@OA\Items(type="string"), description="List of preferred categories"),
     *             @OA\Property(property="preferred_authors", type="array", items=@OA\Items(type="string"), description="List of preferred authors")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User preferences updated successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input or validation error"
     *     )
     * )
     */
    public function store(Request $request) {
        $user = Auth::user();

        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_sources'    => $request->input('preferred_sources', []),
                'preferred_categories' => $request->input('preferred_categories', []),
                'preferred_authors'    => $request->input('preferred_authors', [])
            ]
        );

        return response()->json([
            'status'  => true,
            'message' => 'User preferences updated successfully',
            'data'    => $preferences,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/user/feed",
     *     summary="Get personalized news feed based on user preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Personalized feed retrieved successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User preferences not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error retrieving feed"
     *     )
     * )
     */
    public function personalizedFeed() {
        $user        = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->first();

        if (!$preferences) {
            return response()->json([
                'status'  => false,
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
            'status'  => true,
            'message' => 'Personalized feed retrieved successfully',
            'data'    => $paginatedArticles,
        ], 200);
    }

}
