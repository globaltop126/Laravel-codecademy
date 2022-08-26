<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'invoice_payments', function (Blueprint $table){
            $table->id();
            $table->string('transaction_id',500);
            $table->unsignedBigInteger('invoice_id');
            $table->float('amount', 15, 2);
            $table->date('date');
            $table->unsignedBigInteger('payment_id');
            $table->string('payment_type', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_payments');
    }
}
