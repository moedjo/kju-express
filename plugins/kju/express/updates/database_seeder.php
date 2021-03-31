<?php

namespace Kju\Express\Updates;

use Backend\Models\User;
use Illuminate\Support\Facades\DB;
use Kju\Express\Models\Balance;
use Kju\Express\Models\Branch;
use October\Rain\Database\Updates\Seeder as UpdatesSeeder;

class DatabaseSeeder extends UpdatesSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $branchs = Branch::all();
        foreach ($branchs as $branch) {
            // trace_log($branch);

            $balance = $branch->balance;
            if (empty($balance)) {
                $new_balance = new Balance();
                $new_balance->balance = 0;
                $new_balance->owner()->associate($branch);
                $new_balance->save();
            }
        }

        $users = User::all();
        foreach ($users as $user) {
            // trace_log($branch);

            $balance = $user->balance;
            if (empty($balance)) {
                $new_balance = new Balance();
                $new_balance->balance = 0;
                $new_balance->owner()->associate($user);
                $new_balance->save();
            }
        }
    }
}
