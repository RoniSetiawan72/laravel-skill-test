<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    // 4-1 posts.index
    public function index()
    {
        $posts = Post::active()
            ->with('user')
            ->paginate(20);

        return PostResource::collection($posts);
    }

    // 4-2 posts.create
    public function create()
    {
        return 'posts.create';
    }

    // 4-3 posts.store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = $request->user()->posts()->create($validated);

        return response()->json([
            'message' => 'Post created successfully',
            'data' => new PostResource($post),
        ], 201);
    }

    // 4-4 posts.show
    public function show(Post $post)
    {
        if (! $post->active()->where('id', $post->id)->exists()) {
            abort(404);
        }

        return new PostResource($post->load('user'));
    }

    // 4-5 posts.edit
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return 'posts.edit';
    }

    // 4-6 posts.update
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($validated);

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => new PostResource($post),
        ]);
    }

    // 4-7 posts.destroy
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
}
