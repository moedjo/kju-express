<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressBranches extends Migration
{
    public function up()
    {
        Schema::create('kju_express_branches', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 100);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('sort_order')->default(0);

            $table->integer('district_id')->unsigned()->nullable();
            $table->foreign('district_id')->references('id')->on('kju_express_districts');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_branches');
    }
}
