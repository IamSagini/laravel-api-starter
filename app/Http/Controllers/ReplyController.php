<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Reply;
use App\Models\ReplyLike;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function store(Request $request, Comment $comment)
    {
        $request->validate(['body' => 'required|string']);
        $reply = Reply::create([
            'user_id' => auth()->id(),
            'comment_id' => $comment->id,
            'body' => $request->body,
        ]);
        return response()->json($reply->load('user'), 201);
    }

    public function destroy(Comment $comment, Reply $reply)
    {
        if (auth()->id() !== $reply->user_id && auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $reply->delete();
        return response()->json(['message' => 'Reply deleted']);
    }

    public function toggleLike(Comment $comment, Reply $reply)
    {
        $user = auth()->user();
        $like = ReplyLike::where('user_id', $user->id)->where('reply_id', $reply->id)->first();
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            ReplyLike::create(['user_id' => $user->id, 'reply_id' => $reply->id]);
            $liked = true;
        }
        return response()->json([
            'liked' => $liked,
            'likes_count' => ReplyLike::where('reply_id', $reply->id)->count(),
        ]);
    }
}
