<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        ray(session('last_comment_read'))->blue();
        $comments = Comment::with('user')
            // last_comment_read 키의 값을 가져오되, 해당 키가 없을 경우 0을 기본값으로 반환합니다.
            ->where('id', '>', session('last_comment_read', 0))
            ->latest()
            ->get();

        if ($comments->isEmpty()) {
            return response()->noContent();
        }
        ray(session('last_comment_read'))->green();

        session(['last_comment_read' => $comments->first()->id]);

        ray(session('last_comment_read'))->purple();

        return view('stream', ['comments' => $comments])->fragment('comments');

    }

    public function store(Request $request)
    {
        $validated = $request->validate(['text' => 'string|required']);
        $request->user()->comments()->create($validated);

        if ($request->hasHeader('hx-request')) {
            return view('stream')->fragment('comment-form');
        }

        return response()->back();
    }
}
