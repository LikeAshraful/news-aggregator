<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller {

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
     * @OA\Get(
     *     path="/articles",
     *     summary="Get a list of articles",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword to search for",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Category of article",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Source of article",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Date of article",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",     *         
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     */
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

        if ($request->has('page')) {
            $articles = $articles->paginate($request->page);
        } else {
            $articles = $articles->get();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Articles retrieved successfully',
            'data'    => $articles,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/articles/{id}",
     *     summary="Get an article",
     *     tags={"Articles"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of article to return",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",     *         
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
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
