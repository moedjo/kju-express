<?php namespace Arcode\Vapp\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class AddNewFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('kju_express_delivery_orders', function($table)
        {

            $table->smallInteger('discount');
            $table->smallInteger('discount');
            
        });
    }

    public function down()
    {
        Schema::table('kju_express_delivery_orders', function($table)
        {
            $table->dropColumn(['discount',]);
        });
    }
}
