<?php

use App\Models\Package;
use App\Models\User;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->float('total_price');
            $table->enum('status', [1, 2, 3])->comment('1 = Sedang Diproses, 2 = Siap Diambil, 3 = Selesai');
            $table->enum('payment_type', ['Tunai', 'Nontunai'])->nullable();
            $table->enum('payment_status', ['Tertunda', 'Selesai', 'Dibatalkan'])->nullable();
            $table->foreignUuid('customer_id');
            $table->foreignUuid('created_by');
            $table->foreignUuid('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
