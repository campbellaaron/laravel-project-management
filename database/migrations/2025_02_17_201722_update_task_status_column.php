<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Step 1: Drop the existing 'status' column
            $table->dropColumn('status');
        });

        Schema::table('tasks', function (Blueprint $table) {
            // Step 2: Add the new 'status' column with updated ENUM values
            $table->enum('status', [
                'Not Started',
                'In Progress',
                'Under Review',
                'Completed',
                'On Hold',
                'Cancelled'
            ])->default('Not Started')->after('due_date'); // Adjust position if needed
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('status'); // Remove the new column if rolling back
        });

        Schema::table('tasks', function (Blueprint $table) {
            // Restore the original 'status' column (optional)
            $table->enum('status', ['pending', 'in-progress', 'completed'])->default('pending');
        });
    }
};
