<?php

use Spatie\UptimeMonitor\Models\Enums\SslCertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url');
            $table->boolean('enabled')->default(true);
            $table->string('look_for_string')->default('');
            $table->string('uptime_status')->default(UptimeStatus::NOT_YET_CHECKED);
            $table->string('last_failure_reason')->default('');
            $table->integer('times_failed_in_a_row')->default(0);
            $table->timestamp('last_uptime_status_change_on')->nullable();
            $table->timestamp('uptime_last_checked_on')->nullable();
            $table->boolean('check_ssl_certificate')->default(false);
            $table->string('ssl_certificate_status')->default(SslCertificateStatus::NOT_YET_CHECKED);
            $table->timestamp('ssl_certificate_expiration_date')->nullable();
            $table->string('ssl_certificate_issuer')->nullable();
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
