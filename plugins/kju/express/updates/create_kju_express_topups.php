<?php

namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateKjuExpressTopups extends Migration
{
    public function up()
    {
        Schema::create('kju_express_topups', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->bigInteger('amount');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('status');
            $table->string('bank_name');
            $table->string('bank_account');
            $table->string('bank_owner_name');

            $table->string('branch_id', 10)->nullable();
            $table->foreign('branch_id')->references('id')
                ->on('kju_express_branches')->onDelete('restrict');

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')
                ->on('backend_users')->onDelete('restrict');;
        });
    }

    public function down()
    {
        Schema::dropIfExists('kju_express_topups');
    }
}
