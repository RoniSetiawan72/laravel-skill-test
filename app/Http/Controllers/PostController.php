<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    /* 4-1. posts.index */
    public function index()
    {
        $posts = Post::active()
            ->with('user')
            ->paginate(20);

        return response()->json($posts);
    }

    /* 4-2. posts.create */
    public function create()
    {
        return 'posts.create';
    }

    /* 4-3. posts.store */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'body'         => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        $post = $request->user()->posts()->create($data);

        return response()->json($post, 201);
    }

    /* 4-4. posts.show */
    public function show(Post $post)
    {
        if (
            is_null($post->published_at) ||
            $post->published_at->isFuture()
        ) {
            abort(404);
        }

        return response()->json(
            $post->load('user')
        );
    }

    /* 4-5. posts.edit */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return 'posts.edit';
    }

    /* 4-6. posts.update */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $data = $request->validate([
            'title'        => 'sometimes|required|string|max:255',
            'body'         => 'sometimes|required|string',
            'published_at' => 'nullable|date',
        ]);

        $post->update($data);

        return response()->json($post);
    }

    /* 4-7. posts.destroy */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(null, 204);
    }
}
