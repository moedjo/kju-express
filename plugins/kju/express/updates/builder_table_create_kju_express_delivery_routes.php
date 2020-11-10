<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressDeliveryRoutes extends Migration
{
    public function up()
    {
        Schema::create('kju_express_delivery_routes', function($table)
        {
            $table->engine = 'InnoDB';
            // $table->increments('id')->unsigned();
            $table->string('code', 12);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('code');

            //source
            $table->integer('src_regency_id')->unsigned()->nullable();
            $table->foreign('src_regency_id')->references('id')->on('kju_express_regencies');

            //destination
            $table->integer('dst_district_id')->unsigned()->nullable();
            $table->foreign('dst_district_id')->references('id')->on('kju_express_districts');


            $table->unique(['src_regency_id','dst_district_id'],'src_dst_unique');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_delivery_routes');
    }
}
