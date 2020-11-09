<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressRegencies extends Migration
{
    public function up()
    {
        Schema::create('kju_express_regencies', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('id')->unsigned();
            $table->string('name', 100);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');

            $table->integer('province_id')->unsigned()->nullable();
            $table->foreign('province_id')->references('id')->on('kju_express_provinces');
          
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_regencies');
    }
}
