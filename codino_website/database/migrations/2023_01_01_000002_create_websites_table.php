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
        Schema::create('websites', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            \$table->string('domain')->comment('Domain name, e.g., example.com');
            \$table->string('title')->nullable()->comment('User-friendly title for the website');
            \$table->text('description')->nullable();
            \$table->string('category')->nullable(); // As per guide

            // Technical fields from guide
            \$table->text('server_info')->nullable();
            \$table->string('cms_type')->nullable(); // e.g., WordPress, Joomla, Custom
            \$table->string('php_version')->nullable();

            // Status fields from guide
            \$table->boolean('is_active')->default(true);
            \$table->string('verification_status')->default('pending'); // e.g., pending, verified, failed
            \$table->timestamp('last_checked')->nullable();

            // SEO fields from guide
            \$table->text('meta_description')->nullable();
            \$table->text('keywords')->nullable(); // Storing as text, could be JSON or separate table for many keywords
            \$table->text('analytics_code')->nullable(); // e.g., Google Analytics tracking code snippet

            \$table->timestamps(); // created_at, updated_at

            // Add index for user_id and domain for quicker lookups if a user tries to re-register
            \$table->unique(['user_id', 'domain']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('websites');
    }
};
