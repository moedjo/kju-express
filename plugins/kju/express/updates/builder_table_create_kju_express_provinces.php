<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

// https://www.geopostcodes.com/Indonesia

class BuilderTableCreateKjuExpressProvinces extends Migration
{
    public function up()
    {
        Schema::create('kju_express_provinces', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('id')->unsigned();
            $table->string('name', 100);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary('id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_provinces');
    }
}
