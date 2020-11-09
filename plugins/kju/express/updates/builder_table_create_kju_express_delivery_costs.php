<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressDeliveryCosts extends Migration
{
    public function up()
    {
        Schema::create('kju_express_delivery_costs', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

            $table->integer('cost')->unsigned()->default(0);
            $table->integer('add_cost')->unsigned()->default(0);

            $table->smallInteger('min_lead_time')->unsigned()->default(0);
            $table->smallInteger('max_lead_time')->unsigned()->default(0);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->integer('delivery_route_id')->unsigned()->nullable();;
            $table->foreign('delivery_route_id')->references('id')->on('kju_express_delivery_routes')->onDelete('cascade');

            $table->string('service_code',10)->nullable();
            $table->foreign('service_code')->references('code')->on('kju_express_services');

            $table->unique(['delivery_route_id','service_code']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_delivery_costs');
    }
}
