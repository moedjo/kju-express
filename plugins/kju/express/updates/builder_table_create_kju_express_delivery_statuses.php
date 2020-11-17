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

            $table->string('status_type_code',10)->nullable();
            $table->foreign('status_type_code')->references('code')->on('kju_express_status_types');

            $table->string('description', 100);

            $table->integer('updated_user_id')->unsigned()->nullable();
            $table->foreign('updated_user_id')->references('id')->on('backend_users')->onDelete('cascade');;

            $table->integer('created_user_id')->unsigned()->nullable();
            $table->foreign('created_user_id')->references('id')->on('backend_users')->onDelete('cascade');;
            
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_delivery_statuses');
    }
}
