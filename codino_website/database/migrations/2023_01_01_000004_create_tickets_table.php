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
        Schema::create('tickets', function (Blueprint \$table) {
            \$table->id(); // Or \$table->bigIncrements('id');
            \$table->string('unique_id')->unique()->comment('User-friendly unique ID, e.g., TICKET-YYYYMMDD-XXXX');

            \$table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // User who submitted the ticket
            \$table->foreignId('website_id')->constrained('websites')->onDelete('cascade'); // Website the ticket is for

            \$table->string('title');
            \$table->text('description');
            \$table->string('category')->nullable();
            \$table->string('priority')->default('medium'); // e.g., low, medium, high, critical

            \$table->foreignId('status_id')->constrained('ticket_statuses');
            \$table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // User (admin/agent) assigned to the ticket

            \$table->text('resolution_notes')->nullable();
            \$table->timestamp('resolved_at')->nullable();
            \$table->integer('response_time')->nullable()->comment('In minutes or seconds, store actual response time after first reply');

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
        Schema::dropIfExists('tickets');
    }
};
