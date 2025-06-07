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
        Schema::create('messages', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            \$table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // User who sent the message (customer or agent)
            // \$table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('set null'); // If direct messages within ticket, or if system messages have a target. Guide mentioned it. For general ticket replies, sender_id is often enough.

            \$table->text('message_text');
            \$table->string('message_type')->default('text'); // e.g., text, internal_note, system_event

            // Attachments are in a separate table (file_attachments) linking back to message_id if needed, or directly to ticket_id as per guide.
            // The guide has File_Attachments.ticket_id, so attachments are per ticket, not per message.
            // If attachments are per message, we would add a nullable('attachment_path') here or a link to an attachments table.
            // Given the guide, I will assume File_Attachments links to tickets directly.

            \$table->boolean('is_read')->default(false);
            \$table->boolean('is_internal_note')->default(false);

            // Real-time fields from guide (sent_at is created_at by timestamps())
            // delivered_at and read_at for individual recipients might be too complex for this stage unless using a dedicated chat system.
            // For now, is_read covers the basics.
            \$table->timestamp('delivered_at')->nullable();
            \$table->timestamp('read_at')->nullable();

            \$table->timestamps(); // sent_at (created_at), updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
