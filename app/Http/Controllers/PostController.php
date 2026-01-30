<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use AuthorizesRequests;

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
        $this->authorize('create', Post::class);

        return 'posts.create';
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $this->authorize('create', Post::class);

        $validated = $request->validated();

        $post = Auth::user()->posts()->create($validated);

        $post->load('user');

        return response()->json($post, 201);
    }

    public function show(Post $post): JsonResponse
    {
        $this->authorize('view', $post);

        $post->load('user');

        return response()->json($post);
    }

    public function edit(Post $post): string
    {
        $this->authorize('update', $post);

        return 'posts.edit';
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validated();

        $post->update($validated);

        $post->load('user');

        return response()->json($post, 200);
    }

    public function destroy(Post $post): Response
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->noContent();
    }
}
