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
            $table->string('siret')->nullable()->default(null);
            $table->string('logo')->nullable()->default(null);
            $table->string('address_street_number')->nullable()->default(null);
            $table->string('address_street_name')->nullable()->default(null);
            $table->string('address_zip_code')->nullable()->default(null);
            $table->string('address_city')->nullable()->default(null);
            $table->string('address_country')->nullable()->default(null);
            $table->string('address_billing')->nullable()->default(null);
            $table->string('tva_number')->nullable()->default(null);
            $table->string('website')->nullable()->default(null);
            $table->string('source')->nullable()->default(null);
            $table->string('referent_name')->nullable()->default(null);
            $table->string('referent_email')->nullable()->default(null);
            $table->string('referent_number')->nullable()->default(null);
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
