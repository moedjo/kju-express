<?php

namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateKjuExpressBranches extends Migration
{
    public function up()
    {
        Schema::create('kju_express_branches', function ($table) {
            $table->engine = 'InnoDB';

            $table->string('code', 10);
            $table->string('name', 100);

            $table->double('dom_fee_percentage', 5, 2)->default(0);
            $table->double('int_fee_percentage', 5, 2)->default(0);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('sort_order')->default(0);

            $table->primary('code');

            $table->unique('name');

            $table->integer('region_id')->unsigned()->nullable();
            $table->foreign('region_id')->references('id')
                ->on('kju_express_regions')->onDelete('restrict');

            $table->integer('balance_id')->unsigned()->nullable();
            $table->foreign('balance_id')->references('id')
                ->on('kju_express_balances')->onDelete('restrict');;
        });
    }

    public function down()
    {
        Schema::dropIfExists('kju_express_branches');
    }
}
