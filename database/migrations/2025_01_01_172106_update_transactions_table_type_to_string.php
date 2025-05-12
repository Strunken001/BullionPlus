<?php

use App\Constants\PaymentGatewayConst;
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
        Schema::table('transactions', function (Blueprint $table) {
            // Change the 'type' column from ENUM to STRING
            $table->string('type')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Revert the 'type' column back to ENUM
            $table->enum('type', [
                PaymentGatewayConst::TYPEADDMONEY,
                PaymentGatewayConst::TYPEMONEYOUT,
                PaymentGatewayConst::TYPEWITHDRAW,
                PaymentGatewayConst::TYPECOMMISSION,
                PaymentGatewayConst::TYPEBONUS,
                PaymentGatewayConst::TYPETRANSFERMONEY,
                PaymentGatewayConst::TYPEMONEYEXCHANGE,
                PaymentGatewayConst::TYPEADDSUBTRACTBALANCE,
                PaymentGatewayConst::TYPEMAKEPAYMENT,
                PaymentGatewayConst::TYPECAPITALRETURN,
                PaymentGatewayConst::GIFTCARD,
                PaymentGatewayConst::MOBILETOPUP,
            ])->change();
        });
    }
};
