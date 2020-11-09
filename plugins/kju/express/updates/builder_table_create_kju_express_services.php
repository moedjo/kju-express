<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressServices extends Migration
{
    public function up()
    {
        Schema::create('kju_express_services', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('code', 10);
            $table->string('name', 50);
            $table->string('description', 100);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->integer('weight_limit')->default(-1);

            $table->primary('code');
            $table->unique('name');

            
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_services');
    }
}
