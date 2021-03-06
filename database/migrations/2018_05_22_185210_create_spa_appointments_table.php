<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpaAppointmentsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spa_appointments', function (Blueprint $table) {
            //Columns
            $table->increments('id');
            $table->unsignedInteger('guest_id');
            $table->unsignedInteger('service_id');
            $table->unsignedInteger('treatment_type_id'); //pendiente de tabla
            $table->dateTime('day_hour');
            $table->date('order_date');
            $table->double('price');
            $table->enum('status', ['0', '1', '2']);
            $table->timestamps();

            //FK
            $table->foreign('guest_id')->references('id')->on('guests')->onDelete('cascade');
            $table->foreign('treatment_type_id')->references('id')->on('spa_treatment_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spa_appointments');
    }
}
