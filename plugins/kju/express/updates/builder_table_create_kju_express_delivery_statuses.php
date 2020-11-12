<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressDeliveryStatuses extends Migration
{
    public function up()
    {
        Schema::create('kju_express_delivery_statuses', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_delivery_statuses');
    }
}
