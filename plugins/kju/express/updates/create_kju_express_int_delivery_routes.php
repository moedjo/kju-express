<?php

namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateKjuExpressIntDeliveryRoutes extends Migration
{
    public function up()
    {
        Schema::create('kju_express_int_delivery_routes', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('code', 12);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            // $table->primary('code');

            $table->integer('src_region_id')->unsigned()->nullable();
            $table->foreign('src_region_id')->references('id')
                ->on('kju_express_regions')->onDelete('restrict');;

            $table->integer('dst_region_id')->unsigned()->nullable();
            $table->foreign('dst_region_id')->references('id')
                ->on('kju_express_regions')->onDelete('restrict');;

            $table->unique(['src_region_id', 'dst_region_id'], 'int_src_dst_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kju_express_int_delivery_routes');
    }
}
