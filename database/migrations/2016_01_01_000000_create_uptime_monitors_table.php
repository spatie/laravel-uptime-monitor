<?php

use App\Models\PingMonitor;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUptimeMonitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uptime_monitors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('url');
            $table->string('status')->default(PingMonitor::STATUS_NEVER_CHECKED);
            $table->string('last_failure_reason')->default('');
            $table->string('look_for_string')->default('');
            $table->integer('times_failed_in_a_row')->default(0);
            $table->timestamp('last_status_change_on')->nullable();
            $table->timestamp('last_checked_on')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ping_monitors');
    }
}
