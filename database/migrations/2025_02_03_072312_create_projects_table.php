<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['open', 'in-progress', 'completed']);
            $table->timestamps();
            $table->unsignedBigInteger('project_lead_id')->nullable()->index('projects_project_lead_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
