<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressDeliveryOrders extends Migration
{
    public function up()
    {
        Schema::create('kju_express_delivery_orders', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('code', 20);
            $table->integer('src_district_id');
            $table->integer('dst_district_id');
            $table->text('pickup_address');
            $table->dateTime('pickup_date');
            $table->string('pickup_postal_code', 10);
            $table->string('last_status', 10);
            $table->integer('customer_id');
            $table->bigInteger('total_cost');
            $table->integer('weight');
            $table->integer('pickup_driver_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_user_id');
            $table->integer('created_user_id');
            $table->string('service_code', 10);
            $table->string('route_code', 10);
            $table->integer('cost');
            $table->integer('add_cost');
            $table->integer('weight_limit');
            $table->integer('min_lead_time');
            $table->integer('max_lead_time');
            $table->boolean('pickup_request');
            $table->primary(['code']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_delivery_orders');
    }
}
