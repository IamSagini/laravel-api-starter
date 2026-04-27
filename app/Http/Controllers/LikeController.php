<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Post $post)
    {
        $user = auth()->user();
        $like = Like::where('user_id', $user->id)->where('post_id', $post->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create(['user_id' => $user->id, 'post_id' => $post->id]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => Like::where('post_id', $post->id)->count(),
        ]);
    }
}
