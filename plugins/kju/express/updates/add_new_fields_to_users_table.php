<?php namespace Arcode\Vapp\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class AddNewFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('backend_users', function($table)
        {

            $table->string('branch_code',10)->nullable();
            $table->foreign('branch_code','backend_users_branch_foreign')->references('code')->on('kju_express_branches')->onDelete('restrict');;

        });
    }

    public function down()
    {
        Schema::table('backend_users', function($table)
        {
            $table->dropForeign('backend_users_branch_foreign');
            $table->dropColumn(['branch_code',]);
        });
    }
}
