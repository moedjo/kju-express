<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressIntDeliveryRoutes extends Migration
{
    public function up()
    {
        Schema::create('kju_express_int_delivery_routes', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('code', 12);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('code');

            $table->integer('origin_region_id')->unsigned()->nullable();
            $table->foreign('origin_region_id')->references('id')->on('kju_express_regions')->onDelete('restrict');;

            $table->integer('destination_region_id')->unsigned()->nullable();
            $table->foreign('destination_region_id')->references('id')->on('kju_express_regions')->onDelete('restrict');;

            $table->unique(['origin_region_id','destination_region_id'],'ori_dst_unique');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_int_delivery_routes');
    }
}
