<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressDeliveryOrderStatuses extends Migration
{
    public function up()
    {
        Schema::create('kju_express_delivery_order_statuses', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

            $table->enum('status', ['pick_up', 'process', 'transit','received','failed']);
           
            
            $table->string('delivery_order_code',20)->nullable();;
            $table->foreign('delivery_order_code')->references('code')->on('kju_express_delivery_orders')->onDelete('cascade');

            $table->integer('region_id')->unsigned()->nullable();
            $table->foreign('region_id')->references('id')->on('kju_express_regions');

            $table->string('description', 100);

            $table->integer('updated_user_id')->unsigned()->nullable();
            $table->foreign('updated_user_id')->references('id')->on('backend_users')->onDelete('cascade');;

            $table->integer('created_user_id')->unsigned()->nullable();
            $table->foreign('created_user_id')->references('id')->on('backend_users')->onDelete('cascade');;    

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_delivery_order_statuses');
    }
}
