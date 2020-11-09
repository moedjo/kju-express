<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressDeliveryRoutes extends Migration
{
    public function up()
    {
        Schema::create('kju_express_delivery_routes', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // source
            $table->string('branch_code',10)->nullable();
            $table->foreign('branch_code')->references('code')->on('kju_express_branches');

            //destination
            $table->integer('district_id')->unsigned()->nullable();
            $table->foreign('district_id')->references('id')->on('kju_express_districts');


            
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_delivery_routes');
    }
}
