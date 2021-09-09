<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DataflowsCreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alekseon_dataflow_schedule', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('profile_class');
            $table->string('schedule');
            $table->string('status');
            $table->smallInteger('is_forced')->default(0);
            $table->integer('priority')->default(0);
            $table->text('comment')->nullable();
            $table->text('semaphores')->nullable();
            $table->text('parameters')->nullable();
        });

        Schema::create('alekseon_dataflow_execution', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('schedule_id', false, true);
            $table->string('status');
            $table->timestamp('execute_at');
            $table->foreign('schedule_id')
                ->references('id')
                ->on('alekseon_dataflow_schedule')
                ->onDelete('cascade');
        });

        Schema::create('alekseon_dataflow_report', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('schedule_id',false, true);
            $table->string('user');
            $table->string('type');
            $table->string('status');
            $table->text('result')->nullable();
            $table->timestamp('executed_at')->default(\Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('finished_at')->nullable();;
            $table->foreign('schedule_id')
                ->references('id')
                ->on('alekseon_dataflow_schedule')
                ->onDelete('cascade');
        });

        Schema::create('alekseon_dataflow_report_log', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('report_id',false, true);
            $table->string('level');
            $table->text('message');
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('report_id')
                ->references('id')
                ->on('alekseon_dataflow_report')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alekseon_dataflow_report_log');
        Schema::dropIfExists('alekseon_dataflow_report');
        Schema::dropIfExists('alekseon_dataflow_execution');
        Schema::dropIfExists('alekseon_dataflow_schedule');
    }
}
