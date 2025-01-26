<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDueDateColumnInTasksTable extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Modify the 'due_date' column to be of type DATETIME
            $table->dateTime('due_date')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Revert the column back to a previous type (e.g., string or text)
            $table->string('due_date')->nullable()->change();
        });
    }
}
