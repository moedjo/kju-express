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
            $table->string('phone_number', 20);
            $table->string('email', 100);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->string('branch_code',10)->nullable();
            $table->foreign('branch_code')->references('code')->on('kju_express_branches')->onDelete('restrict');
            
            $table->unique(['phone_number','branch_code']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_customers');
    }
}
