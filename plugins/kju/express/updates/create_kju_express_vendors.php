<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressVendors extends Migration
{
    public function up()
    {
        Schema::create('kju_express_vendors', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 100);
            $table->string('slug', 100)->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('sort_order')->default(0);

            $table->unique('name');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_vendors');
    }
}
