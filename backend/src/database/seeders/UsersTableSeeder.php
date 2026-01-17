<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => 'テスト太郎',
            'email' => 'taro@example.com',
            'password' => bcrypt('tarotaro')
        ];
        DB::table('users')->insert($param);
        $param = [
            'name' => 'テスト次郎',
            'email' => 'jiro@example.com',
            'password' => bcrypt('jirojiro')
        ];
        DB::table('users')->insert($param);
    }
}
