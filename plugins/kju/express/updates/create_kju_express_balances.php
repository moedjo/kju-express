<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressBalances extends Migration
{
    public function up()
    {
        Schema::create('kju_express_balances', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->bigInteger('balance');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            
            $table->integer('owner_id')->unsigned()->nullable();
            $table->string('owner_type');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_balances');
    }
}
