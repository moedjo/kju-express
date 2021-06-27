<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressTransactions extends Migration
{
    public function up()
    {
        Schema::create('kju_express_transactions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->bigInteger('current_balance');
            $table->bigInteger('last_balance');
            $table->string('description');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->integer('created_user_id')->unsigned()->nullable();
            $table->foreign('created_user_id')->references('id')
                ->on('backend_users')->onDelete('restrict'); 

            $table->bigInteger('amount');

            $table->integer('balance_id')->unsigned()->nullable();
            $table->foreign('balance_id')->references('id')
                ->on('kju_express_balances')->onDelete('restrict');

            $table->integer('transactionable_id')->unsigned()->nullable();
            $table->string('transactionable_type');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_transactions');
    }
}
