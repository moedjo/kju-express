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
            $table->integer('src_regency_id')->unsigned()->nullable();
            $table->foreign('src_regency_id')->references('id')->on('kju_express_regencies');

            $table->integer('dst_district_id')->unsigned()->nullable();
            $table->foreign('dst_district_id')->references('id')->on('kju_express_districts');

            $table->dateTime('pickup_date');
            $table->text('pickup_address',2000);
            $table->string('pickup_postal_code', 10);

            $table->string('status_type_code',10)->nullable();
            $table->foreign('status_type_code')->references('code')->on('kju_express_status_types');

            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('kju_express_customers');

            $table->bigInteger('total_cost');
        
            $table->integer('weight');

            // note going to relation
            $table->integer('pickup_courier_user_id')->unsigned()->nullable();
            $table->foreign('pickup_courier_user_id')->references('id')->on('backend_users');


            $table->integer('updated_user_id')->unsigned()->nullable();
            $table->foreign('updated_user_id')->references('id')->on('backend_users');

            $table->integer('created_user_id')->unsigned()->nullable();
            $table->foreign('created_user_id')->references('id')->on('backend_users');

            $table->string('service_code',10)->nullable();
            $table->foreign('service_code')->references('code')->on('kju_express_services');

            $table->string('delivery_route_code')->nullable();;
            $table->foreign('delivery_route_code')->references('code')->on('kju_express_delivery_routes')->onDelete('cascade');

            $table->integer('cost');
            $table->integer('add_cost');
            $table->integer('weight_limit');
            $table->integer('min_lead_time');
            $table->integer('max_lead_time');

            $table->boolean('pickup_request');

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary(['code']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_delivery_orders');
    }
}
