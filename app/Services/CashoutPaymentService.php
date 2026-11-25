<?php

namespace App\Services;

use App\Models\Cashout;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashoutPaymentService
{
    public function markAsPaid(string $id, ?int $userId, ?string $description = null): Cashout
    {
        return DB::transaction(function () use ($id, $userId, $description) {
            $cashout = Cashout::lockForUpdate()->findOrFail($id);

            if ($cashout->status === 'paid') {
                if ($description !== null && $description !== $cashout->description) {
                    $cashout->update(['description' => $description]);
                }

                return $cashout->refresh();
            }

            if ($cashout->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya cashout pending yang dapat ditandai sebagai paid.',
                ]);
            }

            $cashout->update([
                'status' => 'paid',
                'paid_at' => now(),
                'updated_by' => $userId,
                'description' => $description ?? $cashout->description,
            ]);

            return $cashout->refresh();
        });
    }
}
