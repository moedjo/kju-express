<?php

namespace Arcode\Vapp\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class AddNewFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('backend_users', function ($table) {

            $table->integer('branch_id')->unsigned()->nullable();
            $table->foreign('branch_id')->references('id')
                ->on('kju_express_branches')->onDelete('restrict');;

            $table->integer('balance_id')->unsigned()->nullable();
            $table->foreign('balance_id')->references('id')
                ->on('kju_express_balances')->onDelete('restrict');;
        });
    }

    public function down()
    {
        Schema::table('backend_users', function ($table) {
            $table->dropColumn(['branch_id','balance_id']);
        });
    }
}
