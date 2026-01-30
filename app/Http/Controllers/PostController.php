<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::with('user')
            ->active()
            ->latest('published_at')
            ->paginate(20);

        return response()->json($posts);
    }

    public function create(): string
    {
        return 'posts.create';
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $post = Auth::user()->posts()->create($validated);

        return redirect()->route('posts.show', $post);
    }

    public function show(Post $post): JsonResponse
    {
        if (!$post->isActive()) {
            abort(404);
        }

        $post->load('user');

        return response()->json($post);
    }

    public function edit(Post $post): string
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        return 'posts.edit';
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validated();

        $post->update($validated);

        return redirect()->route('posts.show', $post);
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('posts.index');
    }
}
