<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressIntAddDeliveryCosts extends Migration
{
    public function up()
    {
        Schema::create('kju_express_int_add_delivery_costs', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

            $table->integer('add_cost_per_kg')->unsigned()->default(0); 

            $table->string('goods_type_code',10)->nullable();
            $table->foreign('goods_type_code')->references('code')->on('kju_express_goods_types')->onDelete('restrict');;

            $table->string('int_delivery_route_code',12)->nullable();;
            $table->foreign('int_delivery_route_code','add_costs_route_code_foreign')->references('code')->on('kju_express_int_delivery_routes')->onDelete('restrict');

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['int_delivery_route_code','goods_type_code'],'int_add_route_goods_type_unique');
  
       });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_int_add_delivery_costs');
    }
}
