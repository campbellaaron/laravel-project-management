<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable  extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // The user who performed the activity
            $table->string('description'); // Description of the activity
            $table->timestamps();

            // Foreign key constraint to link the activity to the user
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activities');
    }
};
