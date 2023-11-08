<?php

use App\Models\TransactionType;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->double('ammount', 16, 2);
            $table->text('note')->nullable();
            $table->text('image')->nullable();
            $table->foreignIdFor(TransactionType::class)->index()->constrained('transaction_types')->onDelete("CASCADE");
            $table->foreignIdFor(User::class)->index()->constrained('users')->onDelete("CASCADE");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
