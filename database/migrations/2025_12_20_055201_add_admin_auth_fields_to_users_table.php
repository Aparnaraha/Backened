<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone')->unique()->after('email');
        $table->string('otp')->nullable();
        $table->timestamp('otp_expires_at')->nullable();
        $table->boolean('is_verified')->default(false);
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'phone',
            'otp',
            'otp_expires_at',
            'is_verified'
        ]);
    });
}

};
