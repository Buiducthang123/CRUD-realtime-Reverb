<?php

namespace App\Jobs;

use App\Events\CreatePost;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreatePostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Post $post)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        CreatePost::dispatch([
            "id"=>$this->post->id,
            "user_id"=>$this->post->user_id,
            "title"=>$this->post->title,
            "content"=>$this->post->content,
            "time"=>$this->post->time,
        ]);

    }
}
