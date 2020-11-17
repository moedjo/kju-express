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

            $table->string('delivery_route_code',12)->nullable();;
            $table->foreign('delivery_route_code')->references('code')->on('kju_express_delivery_routes')->onDelete('cascade');

            $table->string('service_code',10)->nullable();
            $table->foreign('service_code')->references('code')->on('kju_express_services')->onDelete('cascade');;

            $table->unique(['delivery_route_code','service_code'],'route_service_unique');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_delivery_costs');
    }
}
