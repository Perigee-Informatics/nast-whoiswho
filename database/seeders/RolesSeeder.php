<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['id' => 1,'name' => 'super_admin','field_name' => 'Super Administrator', 'guard_name' => 'web','created_at'=>Carbon::now()->toDateTimeString(),'updated_at'=>Carbon::now()->toDateTimeString()],
            ['id' => 2,'name' => 'central_admin','field_name' => 'Central Admin', 'guard_name' => 'web','created_at'=>Carbon::now()->toDateTimeString(),'updated_at'=>Carbon::now()->toDateTimeString()],
            ['id' => 3,'name' => 'central_operator','field_name' => 'Central Operator', 'guard_name' => 'web','created_at'=>Carbon::now()->toDateTimeString(),'updated_at'=>Carbon::now()->toDateTimeString()],
            ['id' => 4,'name' => 'central_viewer','field_name' => 'Central Viewer', 'guard_name' => 'web','created_at'=>Carbon::now()->toDateTimeString(),'updated_at'=>Carbon::now()->toDateTimeString()],
            ['id' => 5,'name' => 'locallevel_admin','field_name' => 'Local Level - Admin', 'guard_name' => 'web','created_at'=>Carbon::now()->toDateTimeString(),'updated_at'=>Carbon::now()->toDateTimeString()],
            ['id' => 6,'name' => 'locallevel_operator','field_name' => 'Local Level - Operator', 'guard_name' => 'web','created_at'=>Carbon::now()->toDateTimeString(),'updated_at'=>Carbon::now()->toDateTimeString()],
        ]); 
    }
}
