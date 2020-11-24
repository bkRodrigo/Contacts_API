<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactPhoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_phone', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained();
            $table->foreignId('phone_id')->constrained('phones');
            $table->timestamps();

            $table->index(['contact_id', 'phone_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_phone');
    }
}
