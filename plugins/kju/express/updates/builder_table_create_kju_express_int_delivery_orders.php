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

            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('kju_express_customers')->onDelete('restrict');

            $table->string('branch_code',10)->nullable();
            $table->foreign('branch_code')->references('code')->on('kju_express_branches')->onDelete('restrict');;

            $table->integer('branch_region_id')->unsigned()->nullable();
            $table->foreign('branch_region_id')->references('id')->on('kju_express_regions')->onDelete('restrict');

            $table->integer('consignee_region_id')->unsigned()->nullable();
            $table->foreign('consignee_region_id')->references('id')->on('kju_express_regions')->onDelete('restrict');
            $table->string('consignee_name', 100);
            $table->string('consignee_phone_number', 20);
            $table->text('consignee_address',2000);
            $table->string('consignee_postal_code', 10);
           
            $table->string('goods_description', 10);
            $table->integer('goods_amount');
            $table->integer('goods_weight');

            $table->integer('goods_height');
            $table->integer('goods_width');
            $table->integer('goods_length');

            $table->bigInteger('total_cost');
    
            $table->enum('status', ['pickup', 'process', 'transit','received','failed']);

            $table->string('int_delivery_route_code')->nullable();;
            $table->foreign('int_delivery_route_code')->references('code')->on('kju_express_int_delivery_routes')->onDelete('restrict');

            $table->integer('weight');
            $table->integer('base_cost_per_kg')->unsigned()->default(0);
            $table->integer('profit_percentage')->unsigned()->default(0); 

            $table->integer('add_cost_per_kg')->unsigned()->default(0); 
            $table->string('goods_type_code',10)->nullable();
            $table->foreign('goods_type_code')->references('code')->on('kju_express_goods_types')->onDelete('restrict');;

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->timestamp('process_at')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->integer('updated_user_id')->unsigned()->nullable();
            $table->foreign('updated_user_id')->references('id')->on('backend_users')->onDelete('restrict');;

            $table->integer('created_user_id')->unsigned()->nullable();
            $table->foreign('created_user_id')->references('id')->on('backend_users')->onDelete('restrict');;

            $table->integer('deleted_user_id')->unsigned()->nullable();
            $table->foreign('deleted_user_id')->references('id')->on('backend_users')->onDelete('restrict');;

            $table->primary(['code']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_delivery_orders');
    }
}
