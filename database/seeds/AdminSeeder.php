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
        $model = new \App\Admin();
        DB::statement('truncate table ' . $model->getTable());
        \App\Admin::create([
            'name' => 'admin',
            'password' => bcrypt('admin')
        ]);
    }
}
