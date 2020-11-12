<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressCustomers extends Migration
{
    public function up()
    {
        Schema::create('kju_express_customers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 100);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_customers');
    }
}
