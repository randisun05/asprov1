<?php

namespace Tests\Feature\Admin;

use App\Models\WhatsappLog;
use App\Models\WhatsappSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappSettingsManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_index_is_restricted_to_administrator()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');

        $this->actingAs($keanggotaan)->get('/admin/whatsapp')->assertForbidden();
    }

    public function test_index_never_exposes_the_stored_token_but_reports_whether_one_is_set()
    {
        $admin = $this->makeAdminUser('administrator');
        WhatsappSetting::current()->update(['enabled' => true, 'api_token' => 'super-secret-token']);

        $response = $this->actingAs($admin)->get('/admin/whatsapp');

        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Whatsapp/Index')
            ->where('settings.enabled', true)
            ->where('settings.has_token', true)
            ->missing('settings.api_token')
        );
    }

    public function test_administrator_can_turn_the_feature_on_and_set_a_token()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->put('/admin/whatsapp/settings', [
            'enabled' => true,
            'api_token' => 'my-fonnte-token',
        ]);

        $response->assertSessionHas('success');
        $settings = WhatsappSetting::current();
        $this->assertTrue($settings->enabled);
        $this->assertSame('my-fonnte-token', $settings->api_token);
    }

    public function test_submitting_an_empty_token_keeps_the_existing_token()
    {
        $admin = $this->makeAdminUser('administrator');
        WhatsappSetting::current()->update(['enabled' => true, 'api_token' => 'existing-token']);

        $this->actingAs($admin)->put('/admin/whatsapp/settings', [
            'enabled' => false,
            'api_token' => '',
        ]);

        $settings = WhatsappSetting::current();
        $this->assertFalse($settings->enabled);
        $this->assertSame('existing-token', $settings->api_token);
    }

    public function test_unrelated_role_cannot_update_settings()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');

        $response = $this->actingAs($keanggotaan)->put('/admin/whatsapp/settings', [
            'enabled' => true,
            'api_token' => 'x',
        ]);

        $response->assertForbidden();
    }

    public function test_index_shows_summary_counts_and_supports_status_filter()
    {
        $admin = $this->makeAdminUser('administrator');
        WhatsappLog::start('registration_approved', '081234567890')->markSent();
        WhatsappLog::start('registration_rejected', '081234567891')->markFailed('nomor tidak valid');
        WhatsappLog::start('forgot_password', '081234567892');

        $response = $this->actingAs($admin)->get('/admin/whatsapp');

        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Whatsapp/Index')
            ->where('summary.total', 3)
            ->where('summary.sent', 1)
            ->where('summary.failed', 1)
            ->where('summary.pending', 1)
        );

        $failedOnly = $this->actingAs($admin)->get('/admin/whatsapp?status=failed');
        $failedOnly->assertInertia(fn ($page) => $page
            ->where('whatsappLogs.data', fn ($rows) => count($rows) === 1)
        );
    }
}
