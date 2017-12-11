<?php

namespace App\Console\Commands;

use App\UserProfile;
use Carbon\Carbon;
use Illuminate\Console\Command;

class User extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user {startId=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startId = $this->argument('startId');
        $conn = \DB::connection('origin');

        $startTime = time();

        $conn->table('ims_member')
            ->where('id', '>', $startId)
            ->where('id', '<', 1000)
            ->orderBy('id')
            ->chunk(1000, function ($rows) use ($startTime) {
                \DB::transaction(function () use ($rows, $startTime) {
                    foreach ($rows as $row) {
                        $qq = trim($row->qq);
                        $mobile = trim($row->mobile);
                        $profileName = trim($row->username);
                        $profileRemark = $row->admin_remark;
                        $password = bcrypt('123456');

                        if (strpos($row->project, '删除') !== false) {
                            $type = 401;
                        } elseif ($row->project == '') {
                            $type = 401;
                        } elseif (strpos($row->project, '会员') !== false) {
                            $type = 402;
                        } elseif (strpos($row->project, '加群') !== false) {
                            $type = 403;
                        } else {
                            continue;
                        }

                        if (!preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $mobile)) {
                            continue;
                        }
                        $user = \App\User::where('mobile', $mobile)->first();
                        if ($user) {
                            continue;
                        }
                        $user = \App\User::where('qq', $qq)->first();
                        if ($user) {
                            continue;
                        }

                        $updated_at = $created_at = Carbon::createFromTimestamp($row->create_time);

                        $user = new \App\User();
                        $user->timestamps = false;
                        $user = $user->create([
                            'type' => $type,
                            'qq' => $qq,
                            'mobile' => $mobile,
                            'password' => $password,
                            'created_at' => $created_at,
                            'updated_at' => $updated_at
                        ]);

                        $userProfile = new UserProfile();
                        $userProfile->timestamps = false;
                        $userProfile->create([
                            'user_id' => $user->id,
                            'name' => $profileName,
                            'remark' => $profileRemark,
                            'created_at' => $created_at,
                            'updated_at' => $updated_at
                        ]);

                        $useTime = time() - $startTime;
                        echo $row->id . ' ' . $useTime . "\n";
                    }
                });
            });
    }
}
