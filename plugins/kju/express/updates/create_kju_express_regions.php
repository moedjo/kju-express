<?php

namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateKjuExpressRegions extends Migration
{
    public function up()
    {
        Schema::create('kju_express_regions', function ($table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->unsigned();
            $table->string('name', 100);
            $table->enum('type', ['province', 'regency', 'district']);
            $table->integer('parent_id')->unsigned()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary('id');
        });

        Schema::table('kju_express_regions', function ($table) {
          
            $table->foreign('parent_id')->references('id')->on('kju_express_regions')->onDelete('restrict');;
        });
    }

    public function down()
    {
        Schema::dropIfExists('kju_express_regions');
    }
}
