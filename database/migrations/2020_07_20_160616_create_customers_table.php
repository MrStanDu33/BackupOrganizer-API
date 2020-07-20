<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('siren');
            $table->string('logo');
            $table->string('address_street_number');
            $table->string('address_street_name');
            $table->string('address_zip_code');
            $table->string('address_city');
            $table->string('address_country');
            $table->string('address_billing');
            $table->string('tva_number');
            $table->string('website');
            $table->string('source');
            $table->string('referent_name');
            $table->string('referent_email');
            $table->string('referent_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
