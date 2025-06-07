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
        Schema::create('users', function (Blueprint \$table) {
            \$table->id(); // bigIncrements
            \$table->string('username')->unique()->comment('As per guide, though name was in previous schema. Assuming username is preferred for login.');
            // The guide mentioned "username, email, password_hash" as primary.
            // And "Profile: phone, avatar, timezone, language_preference"
            // The previous schema had 'name'. I will use 'username' as primary identifier and add 'name' for display/profile.
            \$table->string('name')->nullable()->comment('Display name or full name');
            \$table->string('email')->unique();
            \$table->timestamp('email_verified_at')->nullable();
            \$table->string('password'); // Laravel default, will be hashed by User model/controller

            // Security fields from guide
            \$table->integer('failed_login_attempts')->default(0);
            \$table->timestamp('last_login')->nullable();
            \$table->string('account_status')->default('active'); // e.g., active, suspended, pending_verification

            // Profile fields from guide
            \$table->string('phone')->nullable()->unique(); // Assuming phone is for profile, not primary login with email
            \$table->string('avatar')->nullable();
            \$table->string('timezone')->nullable();
            \$table->string('language_preference', 10)->default('en'); // e.g., en, fa

            // 2FA fields (as per plan)
            \$table->text('two_factor_secret')->nullable();
            \$table->text('two_factor_recovery_codes')->nullable();
            \$table->timestamp('two_factor_confirmed_at')->nullable(); // If using Laravel Fortify/Jetstream style for 2FA confirmation

            // Plan relationship
            \$table->foreignId('plan_id')->nullable()->constrained('plans')->onDelete('set null');

            \$table->rememberToken();
            \$table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
