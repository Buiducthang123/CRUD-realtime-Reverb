<?php

namespace App\Jobs;

use App\Events\DeletePost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeletePostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $status)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        DeletePost::dispatch([
            'status'=>$this->status
        ]);
    }
}
