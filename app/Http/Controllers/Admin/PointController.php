<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\PointService;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function __construct(private PointService $points)
    {
    }

    /**
     * Give a point reward to a member.
     */
    public function reward(Request $request, $id)
    {
        if (! in_array(auth()->user()->role, ['keanggotaan', 'administrator'], true)) {
            return redirect()->route('admin.dashboard')->with('error', 'anda tidak memiliki akses ke halaman tersebut');
        }

        $request->validate([
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $member = Member::findOrFail($id);

        $this->points->reward($member, (int) $request->amount, $request->description, auth()->user());

        return redirect()->back()->with('success', 'Poin berhasil diberikan kepada anggota.');
    }

    /**
     * Show the bulk reward form (paste a list of NIPs).
     */
    public function rewardGroupForm()
    {
        if (! in_array(auth()->user()->role, ['keanggotaan', 'administrator'], true)) {
            return redirect()->route('admin.dashboard')->with('error', 'anda tidak memiliki akses ke halaman tersebut');
        }

        return inertia('Admin/Points/RewardGroup');
    }

    /**
     * Give the same point reward to a batch of members, identified by NIP.
     * NIPs are accepted separated by comma and/or newline.
     */
    public function rewardGroup(Request $request)
    {
        if (! in_array(auth()->user()->role, ['keanggotaan', 'administrator'], true)) {
            return redirect()->route('admin.dashboard')->with('error', 'anda tidak memiliki akses ke halaman tersebut');
        }

        $request->validate([
            'nips' => 'required|string',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $nips = collect(preg_split('/[,\r\n]+/', $request->nips))
            ->map(fn ($nip) => trim($nip))
            ->filter()
            ->unique()
            ->values();

        $rewarded = [];
        $notFound = [];

        foreach ($nips as $nip) {
            $member = Member::where('nip', $nip)->first();

            if (! $member) {
                $notFound[] = $nip;
                continue;
            }

            $this->points->reward($member, (int) $request->amount, $request->description, auth()->user());
            $rewarded[] = $nip;
        }

        $message = count($rewarded) . ' anggota berhasil diberi poin.';

        if ($notFound) {
            return redirect()->route('admin.members.index')
                ->with('success', $message)
                ->with('warning', 'NIP tidak ditemukan: ' . implode(', ', $notFound));
        }

        return redirect()->route('admin.members.index')->with('success', $message);
    }
}
