<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreblogRequest;
use App\Http\Requests\UpdateblogRequest;
use App\Http\Resources\CommonResource;
use App\Models\Blog;
use Illuminate\Support\Facades\App;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        App::setLocale(request()->get('lang', 'en'));
        laradump()->showQueries();
        $someBlogResults = Blog::with('comments')->select(['id', 'title'])->get();
        return new CommonResource($someBlogResults);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request)
    {
        App::setLocale(request()->get('lang', 'en'));
        $newBlog = Blog::create($request->validated());
        return new CommonResource($newBlog);
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, blog $blog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        //
    }
}
