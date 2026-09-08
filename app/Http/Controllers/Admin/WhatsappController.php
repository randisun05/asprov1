<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappLog;
use App\Models\WhatsappSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhatsappController extends Controller
{
    /**
     * Pengaturan on/off + token Fonnte, dan monitoring status pengiriman
     * WhatsApp (berhasil/gagal/pending) - mengikuti pola halaman Monitoring
     * Email yang sudah ada.
     */
    public function index()
    {
        $settings = WhatsappSetting::current();

        $whatsappLogs = WhatsappLog::when(request()->q, function ($query) {
                $q = request()->q;
                $query->where('to_phone', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%");
            })
            ->when(request()->status, function ($query) {
                $query->where('status', request()->status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = WhatsappLog::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return inertia('Admin/Whatsapp/Index', [
            'settings' => [
                'enabled' => $settings->enabled,
                'has_token' => filled($settings->api_token),
            ],
            'whatsappLogs' => $whatsappLogs,
            'summary' => [
                'total' => (int) $summary->sum('total'),
                'sent' => (int) ($summary->get(WhatsappLog::STATUS_SENT)->total ?? 0),
                'pending' => (int) ($summary->get(WhatsappLog::STATUS_PENDING)->total ?? 0),
                'failed' => (int) ($summary->get(WhatsappLog::STATUS_FAILED)->total ?? 0),
            ],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'enabled' => 'required|boolean',
            'api_token' => 'nullable|string',
        ]);

        $settings = WhatsappSetting::current();
        $settings->enabled = $request->boolean('enabled');

        // An empty field means "leave the existing token alone" - the
        // current token is never sent back to the browser (see index()),
        // so there is nothing for the admin to intentionally blank out to.
        if (filled($request->api_token)) {
            $settings->api_token = $request->api_token;
        }

        $settings->save();

        return redirect()->route('admin.whatsapp.index')->with('success', 'Pengaturan WhatsApp berhasil disimpan.');
    }
}
