<?php

namespace App\Services;

use App\Exceptions\InsufficientPointsException;
use App\Models\Event;
use App\Models\Member;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PointService
{
    /**
     * Current point balance is the balance_after of the member's most recent
     * transaction (same running-balance pattern as the Jurnal ledger), not a
     * separately stored/mutable total.
     */
    public function getBalance(Member $member): int
    {
        return (int) (PointTransaction::where('member_id', $member->id)
            ->orderByDesc('id')
            ->value('balance_after') ?? 0);
    }

    /**
     * Admin gives point reward to a member.
     */
    public function reward(Member $member, int $amount, ?string $description, ?User $admin): PointTransaction
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Jumlah poin harus lebih dari 0.');
        }

        return DB::transaction(function () use ($member, $amount, $description, $admin) {
            // Lock the member row to serialize concurrent point operations
            // for this member (mirrors the fix used for the registration
            // approval race condition elsewhere in this app).
            Member::whereKey($member->id)->lockForUpdate()->first();

            $balance = $this->getBalance($member);

            return PointTransaction::create([
                'member_id' => $member->id,
                'type' => 'reward',
                'amount' => $amount,
                'balance_after' => $balance + $amount,
                'description' => $description,
                'created_by' => $admin?->id,
            ]);
        });
    }

    /**
     * Deduct points when a member joins an event that has a point cost.
     * Returns null if the event is free (no point_cost configured).
     *
     * @throws InsufficientPointsException
     */
    public function redeemForEvent(Member $member, Event $event): ?PointTransaction
    {
        $cost = $event->point_cost;

        if ($cost <= 0) {
            return null;
        }

        return DB::transaction(function () use ($member, $event, $cost) {
            Member::whereKey($member->id)->lockForUpdate()->first();

            $balance = $this->getBalance($member);

            if ($balance < $cost) {
                throw new InsufficientPointsException(
                    "Poin tidak cukup untuk mengikuti kegiatan ini. Saldo Anda {$balance}, dibutuhkan {$cost} poin."
                );
            }

            return PointTransaction::create([
                'member_id' => $member->id,
                'type' => 'redeem',
                'amount' => $cost,
                'balance_after' => $balance - $cost,
                'description' => "Mengikuti kegiatan: {$event->title}",
                'event_id' => $event->id,
            ]);
        });
    }
}
