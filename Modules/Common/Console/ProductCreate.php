<?php

namespace Modules\Common\Console;

use Modules\Common\Entities\Product;
use Illuminate\Console\Command;

class ProductCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:create';

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
        $data = [
            [
                'id' => 1,
                'name' => 'excel_month',
                'group' => 'excel',
                'title' => 'EXCEL包月',
                'type' => 3,
                'duration' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                'amount' => 500
            ],
            [
                'id' => 2,
                'name' => 'trier_auth_year',
                'group' => 'user_auth',
                'title' => '试客认证按年',
                'type' => 2,
                'duration' => [1],
                'amount' => 5000
            ],
            [
                'id' => 3,
                'name' => 'manager_auth_year',
                'group' => 'user_auth',
                'title' => '主持认证按年',
                'type' => 2,
                'duration' => [1],
                'amount' => 5000
            ],
            [
                'id' => 4,
                'name' => 'merchant_auth_year',
                'group' => 'user_auth',
                'title' => '商家认证按年',
                'type' => 2,
                'duration' => [1],
                'amount' => 5000
            ],
        ];

        foreach ($data as $item) {
            Product::updateOrCreate([
                'id' => $item['id']
            ], $item);
        }
    }
}
