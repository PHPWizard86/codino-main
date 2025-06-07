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
        Schema::create('plans', function (Blueprint \$table) {
            \$table->id(); // Alias for bigIncrements('id')
            \$table->string('name', 50)->unique();
            \$table->integer('ticket_limit')->nullable()->comment('NULL for unlimited');
            \$table->decimal('price', 10, 2)->default(0.00);
            \$table->text('features')->nullable();
            \$table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
};
