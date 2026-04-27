<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private function formatPost($post, $authId)
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'body' => $post->body,
            'views' => $post->views,
            'user_id' => $post->user_id,
            'user' => $post->user,
            'likes_count' => $post->likes->count(),
            'liked_by_me' => $post->likes->contains('user_id', $authId),
            'comments_count' => $post->comments->count(),
            'comments' => $post->comments->map(function ($comment) use ($authId) {
                return [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'user_id' => $comment->user_id,
                    'user' => $comment->user,
                    'likes_count' => $comment->likes->count(),
                    'liked_by_me' => $comment->likes->contains('user_id', $authId),
                    'replies_count' => $comment->replies->count(),
                    'replies' => $comment->replies->map(function ($reply) use ($authId) {
                        return [
                            'id' => $reply->id,
                            'body' => $reply->body,
                            'user_id' => $reply->user_id,
                            'user' => $reply->user,
                            'likes_count' => $reply->likes->count(),
                            'liked_by_me' => $reply->likes->contains('user_id', $authId),
                            'created_at' => $reply->created_at,
                        ];
                    }),
                    'created_at' => $comment->created_at,
                ];
            }),
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
        ];
    }

    public function index()
    {
        $authId = auth()->id();
        $posts = Post::with([
            'user', 'likes', 
            'comments.user', 'comments.likes', 
            'comments.replies.user', 'comments.replies.likes'
        ])->latest()->get();
        return response()->json($posts->map(fn($post) => $this->formatPost($post, $authId)));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);
        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'body' => $request->body,
            'views' => 0,
        ]);
        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    public function show(Post $post)
    {
        $authId = auth()->id();
        $post->load([
            'user', 'likes',
            'comments.user', 'comments.likes',
            'comments.replies.user', 'comments.replies.likes'
        ]);
        return response()->json($this->formatPost($post, $authId));
    }

    public function incrementView(Post $post)
    {
        $post->increment('views');
        return response()->json(['views' => $post->views]);
    }

    public function update(Request $request, Post $post)
    {
        if (auth()->id() !== $post->user_id && auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $post->update($request->only('title', 'body'));
        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    public function destroy(Post $post)
    {
        if (auth()->id() !== $post->user_id && auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $post->delete();
        return response()->json(['message' => 'Post deleted successfully']);
    }
}
