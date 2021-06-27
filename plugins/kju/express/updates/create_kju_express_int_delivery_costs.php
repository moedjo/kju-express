<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateKjuExpressIntDeliveryCosts extends Migration
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

            $table->double('profit_percentage',5,2)->default(0);
            
            $table->integer('int_delivery_route_id')->unsigned()->nullable();
            $table->foreign('int_delivery_route_id','int_del_costs_route_foreign')->references('id')
                ->on('kju_express_int_delivery_routes')->onDelete('restrict');


            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['int_delivery_route_id','min_range_weight','max_range_weight']
                ,'int_route_weight_unique');
       });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_int_delivery_costs');
    }
}
