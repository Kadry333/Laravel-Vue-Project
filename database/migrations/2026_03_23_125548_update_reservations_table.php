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
            if (Schema::hasColumn('reservations', 'handled_by')) {
                $table->dropColumn('handled_by');
            }

            if (!Schema::hasColumn('reservations', 'status')) {
                $table->enum(
                    'status',
                    [
                        ReservationStatus::PENDING,
                        ReservationStatus::APPROVED,
                        ReservationStatus::CANCELLED,
                    ]
                )->default(ReservationStatus::PENDING)->after('paid_price');
            }

            if (!Schema::hasColumn('reservations', 'payment_session_id')) {
                $table->string('payment_session_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('reservations', 'status')) {
                $columnsToDrop[] = 'status';
            }

            if (Schema::hasColumn('reservations', 'payment_session_id')) {
                $columnsToDrop[] = 'payment_session_id';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            if (!Schema::hasColumn('reservations', 'handled_by')) {
                $table->foreignId('handled_by')->constrained('admins')->cascadeOnDelete();
            }
        });
    }
};
