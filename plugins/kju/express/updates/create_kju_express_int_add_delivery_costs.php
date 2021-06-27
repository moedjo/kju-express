<?php

namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateKjuExpressIntAddDeliveryCosts extends Migration
{
    public function up()
    {
        Schema::create('kju_express_int_add_delivery_costs', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

            $table->integer('add_cost_per_kg')->unsigned()->default(0);

            $table->integer('goods_type_id')->unsigned()->nullable();
            $table->foreign('goods_type_id', 'int_add_del_costs_goods_type_foreign')
                ->references('id')
                ->on('kju_express_goods_types')->onDelete('restrict');

            $table->integer('int_delivery_route_id')->unsigned()->nullable();
            $table->foreign('int_delivery_route_id', 'int_add_del_costs_route_foreign')
                ->references('id')
                ->on('kju_express_int_delivery_routes')->onDelete('restrict');




            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(
                ['int_delivery_route_id', 'goods_type_id'],
                'kju_express_int_add_delivery_costs_unique'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('kju_express_int_add_delivery_costs');
    }
}
