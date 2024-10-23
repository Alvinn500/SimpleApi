<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseFormatSame;

class CommentController extends Controller implements HasMiddleware
{

    public static function middleware()
    {

        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(["data" => Comment::all(), "status" => 200], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'comment' => 'required|max:255|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => "comments are required",
                'status' => 400
            ], 400);
        }

        $fields = $request->validate(
            [
                'comment' => 'required|max:255|min:1',
            ]
        );

        $comment = $request->user()->comment()->create($fields);

        return response()->json([
            "massage" => "Comment has been create",
            "data" => $comment,
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        return response()->json(['data' => $comment, "status" => 200], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {

        Gate::authorize('modify', $comment);

        $validator = Validator::make($request->all(), [
            "comment" => "required|max:255|min:1"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "massage" => "Comment are required",
                "status" => 400
            ], 400);
        };

        $field = $request->validate([
            'comment' => "required|max:255|min:1"
        ]);

        $comment->update($field);

        return response()->json([
            "message" => "Commant has been edited",
            "data" => $comment,
            "status" => 201
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {

        Gate::authorize('modify', $comment);

        $comment->delete();

        return response()->json([
            "message" => "The comment was deleted",
            "status" => 200
        ], 200);
    }
}
