<?php

use App\Cores\General\Enums\ReservationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['handled_by']);
            $table->dropColumn('handled_by');
            $table->enum(
                'status',
                [
                    ReservationStatus::PENDING,
                    ReservationStatus::APPROVED
                ]
            )->default(ReservationStatus::PENDING)->after('paid_price');
            $table->string('payment_session_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('status');

            $table->foreignId('handled_by')->constrained('admins')->cascadeOnDelete();
        });
    }
};
