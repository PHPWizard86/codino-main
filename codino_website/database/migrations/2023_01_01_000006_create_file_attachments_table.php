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
        Schema::create('file_attachments', function (Blueprint \$table) {
            \$table->id();

            \$table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            \$table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // User who uploaded the file

            // File info from guide
            \$table->string('original_name');
            \$table->string('stored_name')->unique()->comment('e.g., a UUID based name or hashed name to prevent conflicts');
            \$table->string('file_path'); // Path relative to a storage disk root
            \$table->unsignedBigInteger('file_size')->comment('In bytes');
            \$table->string('mime_type');

            // Security fields from guide
            \$table->string('virus_scan_status')->default('pending'); // e.g., pending, clean, infected
            \$table->string('hash_checksum', 64)->nullable()->comment('e.g., SHA256 hash of the file content');

            \$table->timestamps(); // uploaded_at (created_at), updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_attachments');
    }
};
