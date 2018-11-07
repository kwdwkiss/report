<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $model = new \Modules\Common\Entities\Admin();
        DB::statement('truncate table ' . $model->getTable());
        \Modules\Common\Entities\Admin::create([
            'name' => 'admin',
            'password' => bcrypt('admin')
        ]);
    }
}
