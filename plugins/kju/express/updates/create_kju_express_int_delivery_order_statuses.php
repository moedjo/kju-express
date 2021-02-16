<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateKjuExpressDeliveryOrderStatuses extends Migration
{
    public function up()
    {
        Schema::create('kju_express_int_delivery_order_statuses', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

            $table->enum('status', ['pending', 'process','export','reject','failed']);
           
            $table->string('int_delivery_order_code',20)->nullable();;
            $table->foreign('int_delivery_order_code','statuses_order_code_foreign')->references('code')->on('kju_express_int_delivery_orders')->onDelete('restrict');

            
            $table->integer('region_id')->unsigned()->nullable();
            $table->foreign('region_id')->references('id')->on('kju_express_regions');

            $table->string('description', 100);

            $table->integer('updated_user_id')->unsigned()->nullable();
            $table->foreign('updated_user_id')->references('id')->on('backend_users')->onDelete('restrict');;

            $table->integer('created_user_id')->unsigned()->nullable();
            $table->foreign('created_user_id')->references('id')->on('backend_users')->onDelete('restrict');;    

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_int_delivery_order_statuses');
    }
}
