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
        Schema::create('vtpass_api_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('service')->string()->comment('The service name for which the discount applies');
            $table->decimal('api_discount_percentage', 5, 2)->default(0.00)->comment('The percentage discount to be applied');
            $table->string('type')->comment('The type of service, e.g., utility_bill, airtime');
            $table->string('service_id')->comment('The unique identifier for the service');
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
        Schema::dropIfExists('vtpass_api_discounts');
    }
};
