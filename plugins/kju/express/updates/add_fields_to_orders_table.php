<?php namespace Arcode\Vapp\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class AddFieldsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('kju_express_delivery_orders', function($table)
        {
            $table->bigInteger('original_total_cost');
            $table->smallInteger('discount');
            $table->enum('payment_status', ['paid', 'unpaid'])->default('paid');
            $table->string('payment_description');
            
        });
    }

    public function down()
    {
        Schema::table('kju_express_delivery_orders', function($table)
        {
            $table->dropColumn(['discount','paid','description']);
        });
    }
}
