<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressIntDeliveryCosts extends Migration
{
    public function up()
    {
        Schema::create('kju_express_int_delivery_costs', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('min_range_weight');
            $table->integer('max_range_weight');
            $table->integer('base_cost_per_kg')->unsigned()->default(0);
            $table->integer('profit_percentage')->unsigned()->default(0);   
            
            $table->string('int_delivery_route_code',12)->nullable();;
            $table->foreign('int_delivery_route_code','costs_route_code_foreign')->references('code')->on('kju_express_int_delivery_routes')->onDelete('restrict');

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['int_delivery_route_code','min_range_weight','max_range_weight'],'int_route_weight_unique');
       });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_int_delivery_costs');
    }
}
