<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function index()
    {
        return response()->json(["data" => Comment::with('user')->get(), "status" => 200], 200);
    }
}
