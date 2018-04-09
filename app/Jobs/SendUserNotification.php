<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendUserNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userSelect;

    protected $data;

    protected $notification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userSelect, $data, $notification)
    {
        $this->onQueue('send_notification');
        $this->userSelect = $userSelect;
        $this->data = $data;
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->userSelect) {
            case 1:
                $query = User::query();
                break;
            case 2:
                $query = User::where('type', $this->data);
                break;
            case 3:
                $idStr = $this->data;
                $ids = explode(',', $idStr) ?: [];

                $query = User::whereIn('id', $ids);
                break;
            case 4:
                $mobileStr = $this->data;
                $mobiles = explode(',', $mobileStr) ?: [];

                $query = User::whereIn('mobile', $mobiles);
                break;
        }
        $query->chunk(1000, function ($notifiables) {
            \Notification::send($notifiables, $this->notification);
        });
    }
}
