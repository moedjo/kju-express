<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressDeliveryCosts extends Migration
{
    public function up()
    {
        Schema::create('kju_express_int_delivery_costs', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();

            // base cost
            // profit percentage

            

       });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_int_delivery_costs');
    }
}
