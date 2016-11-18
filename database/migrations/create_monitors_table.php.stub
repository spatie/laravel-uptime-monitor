<?php

use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->unique();

            $table->boolean('uptime_check_enabled')->default(true);
            $table->string('look_for_string')->default('');
            $table->string('uptime_check_interval_in_minutes')->default(5);
            $table->string('uptime_status')->default(UptimeStatus::NOT_YET_CHECKED);
            $table->string('uptime_check_failure_reason')->default('');
            $table->integer('uptime_check_times_failed_in_a_row')->default(0);
            $table->timestamp('uptime_status_last_change_date')->nullable();
            $table->timestamp('uptime_last_check_date')->nullable();
            $table->timestamp('uptime_check_failed_event_fired_on_date')->nullable();
            $table->string('uptime_check_method')->default('get');

            $table->boolean('certificate_check_enabled')->default(false);
            $table->string('certificate_status')->default(CertificateStatus::NOT_YET_CHECKED);
            $table->timestamp('certificate_expiration_date')->nullable();
            $table->string('certificate_issuer')->nullable();
            $table->string('certificate_check_failure_reason')->default('');

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
        Schema::drop('monitors');
    }
}
