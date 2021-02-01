<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateKjuExpressDeliveryOrders extends Migration
{
    public function up()
    {
        Schema::create('kju_express_int_delivery_orders', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('code', 20);

            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('kju_express_customers')->onDelete('restrict');

            $table->string('branch_code',10)->nullable();
            $table->foreign('branch_code')->references('code')->on('kju_express_branches')->onDelete('restrict');;

            $table->integer('origin_region_id')->unsigned()->nullable();
            $table->foreign('origin_region_id')->references('id')->on('kju_express_regions')->onDelete('restrict');

            $table->integer('consignee_region_id')->unsigned()->nullable();
            $table->foreign('consignee_region_id')->references('id')->on('kju_express_regions')->onDelete('restrict');
            $table->string('consignee_name', 100);
            $table->string('consignee_phone_number', 20);
            $table->text('consignee_address',2000);
            $table->string('consignee_postal_code', 10);
           
            $table->string('goods_description', 10);
            $table->integer('goods_amount');
            $table->double('goods_weight', 5, 2);

            $table->double('goods_volume_weight',5,2);

            $table->integer('goods_height');
            $table->integer('goods_width');
            $table->integer('goods_length');

            $table->bigInteger('original_total_cost');
            
            $table->bigInteger('base_cost');
            $table->bigInteger('add_cost');

            $table->bigInteger('total_cost');
            
            $table->bigInteger('base_profit')->unsigned()->default(0); 
            $table->bigInteger('profit')->unsigned()->default(0); 

            $table->bigInteger('fee')->unsigned()->default(0); ;
            $table->bigInteger('fee_percentage')->unsigned()->default(0); ;

            $table->smallInteger('discount');
            $table->enum('payment_status', ['paid', 'unpaid'])->default('paid');
            $table->string('payment_description');


            $table->enum('payment_method', ['cash', 'transfer'])->default('cash');
    
            $table->enum('status', ['pickup', 'process', 'transit','received','failed']);

            $table->string('int_delivery_route_code')->nullable();;
            $table->foreign('int_delivery_route_code','orders_route_code_foreign')->references('code')->on('kju_express_int_delivery_routes')->onDelete('restrict');

            $table->integer('min_range_weight');
            $table->integer('max_range_weight');
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
        Schema::dropIfExists('kju_express_int_delivery_orders');
    }
}
