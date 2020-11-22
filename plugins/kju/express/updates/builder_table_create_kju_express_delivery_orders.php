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

            $table->string('branch_code',10)->nullable();
            $table->foreign('branch_code')->references('code')->on('kju_express_branches')->onDelete('cascade');;

            $table->integer('branch_region_id')->unsigned()->nullable();
            $table->foreign('branch_region_id')->references('id')->on('kju_express_regions')->onDelete('cascade');

            $table->integer('pickup_region_id')->unsigned()->nullable();
            $table->foreign('pickup_region_id')->references('id')->on('kju_express_regions')->onDelete('cascade');

            $table->integer('consignee_region_id')->unsigned()->nullable();
            $table->foreign('consignee_region_id')->references('id')->on('kju_express_regions')->onDelete('cascade');

            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('kju_express_customers')->onDelete('cascade');

            $table->boolean('pickup_request');
            $table->dateTime('pickup_date')->nullable();
            $table->text('pickup_address',2000);
            $table->string('pickup_postal_code', 10);

            $table->text('consignee_address',2000);
            $table->string('consignee_postal_code', 10);
            $table->string('consignee_phone_number', 20);

            $table->string('service_code',10)->nullable();
            $table->foreign('service_code')->references('code')->on('kju_express_services')->onDelete('cascade');;
           
            $table->string('goods_description', 10);
            $table->string('goods_amount', 10);
            $table->integer('goods_weight');
            $table->bigInteger('total_cost');
    
            $table->string('status_type_code',10)->nullable();
            $table->foreign('status_type_code')->references('code')->on('kju_express_status_types')->onDelete('cascade');
            
            $table->integer('pickup_courier_user_id')->unsigned()->nullable();
            $table->foreign('pickup_courier_user_id')->references('id')->on('backend_users')->onDelete('cascade');;

            $table->integer('updated_user_id')->unsigned()->nullable();
            $table->foreign('updated_user_id')->references('id')->on('backend_users')->onDelete('cascade');;

            $table->integer('created_user_id')->unsigned()->nullable();
            $table->foreign('created_user_id')->references('id')->on('backend_users')->onDelete('cascade');;

            $table->string('delivery_route_code')->nullable();;
            $table->foreign('delivery_route_code')->references('code')->on('kju_express_delivery_routes')->onDelete('cascade');

            $table->integer('cost');
            $table->integer('add_cost');
            $table->integer('weight_limit');
            $table->integer('min_lead_time');
            $table->integer('max_lead_time');

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
