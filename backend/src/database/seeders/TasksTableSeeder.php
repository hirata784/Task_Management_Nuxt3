<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'user_id' => 1,
            'title' => '買い物に行く',
            'is_done' => false
        ];
        DB::table('tasks')->insert($param);
        $param = [
            'user_id' => 1,
            'title' => '洗濯をする',
            'is_done' => true
        ];
        DB::table('tasks')->insert($param);
                $param = [
            'user_id' => 2,
            'title' => 'ランニングする',
            'is_done' => false
        ];
        DB::table('tasks')->insert($param);
        $param = [
            'user_id' => 2,
            'title' => '料理をする',
            'is_done' => true
        ];
        DB::table('tasks')->insert($param);
    }
}
