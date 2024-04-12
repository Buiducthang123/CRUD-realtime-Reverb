<?php

namespace App\Http\Controllers;

use App\Events\DeletePost;
use App\Jobs\CreatePostJob;
use App\Jobs\DeletePostJob;
use App\Models\Post;
use Illuminate\Http\Request;
use Laravel\Reverb\Loggers\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Hiển thị các bài post
        $posts = Post::all();
        return response()->json($posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Trả về giao diện form tạo Post
        return view('post.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Hàm thuực hiện thêm Post
        $post = Post::create(
            [
                "user_id"=>auth()->id(),
                "content"=>$request->content,
                "title"=>$request->title,
            ]
        );

        if ($post) {
            // Nếu thành công, bạn có thể chuyển hướng người dùng đến một trang khác hoặc thực hiện hành động khác
            CreatePostJob::dispatch($post);
            // return redirect()->route('posts.index')->with('success', 'Bài viết đã được tạo thành công!');
        } else {
            // Nếu không thành công, bạn có thể trả về một thông báo lỗi
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi tạo bài viết. Vui lòng thử lại sau!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
        $result = $post->delete();
        if ($result){
            DeletePostJob::dispatch($result);
        }
    }
}
