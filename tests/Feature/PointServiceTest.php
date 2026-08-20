<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientPointsException;
use App\Models\Event;
use App\Models\EventPoint;
use App\Models\Member;
use App\Services\PointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(): Member
    {
        return Member::create([
            'nip' => '199001012020121009',
            'name' => 'Anggota Service',
            'email' => 'anggotaservice@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00009/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
    }

    private function makeEvent(int $pointCost): Event
    {
        $event = Event::create([
            'title' => 'Workshop Service Test',
            'slug' => 'workshop-service-test-' . uniqid(),
            'body' => 'Deskripsi',
            'date' => now()->toDateString(),
            'enddate' => now()->addDay()->toDateString(),
            'participant' => 10,
            'place' => 'Jakarta',
            'link' => '-',
            'status' => 'active',
        ]);

        EventPoint::create(['event_id' => $event->id, 'point_cost' => $pointCost]);

        return $event;
    }

    public function test_balance_is_zero_with_no_transactions()
    {
        $member = $this->makeMember();

        $this->assertSame(0, app(PointService::class)->getBalance($member));
    }

    public function test_reward_increases_the_balance()
    {
        $member = $this->makeMember();
        $service = app(PointService::class);

        $service->reward($member, 30, 'Test reward', null);

        $this->assertSame(30, $service->getBalance($member));
    }

    public function test_redeem_for_event_decreases_the_balance()
    {
        $member = $this->makeMember();
        $service = app(PointService::class);
        $event = $this->makeEvent(pointCost: 15);

        $service->reward($member, 50, null, null);
        $service->redeemForEvent($member, $event);

        $this->assertSame(35, $service->getBalance($member));
    }

    public function test_redeem_for_a_free_event_is_a_no_op()
    {
        $member = $this->makeMember();
        $service = app(PointService::class);
        $event = $this->makeEvent(pointCost: 0);

        $service->reward($member, 50, null, null);
        $result = $service->redeemForEvent($member, $event);

        $this->assertNull($result);
        $this->assertSame(50, $service->getBalance($member));
    }

    public function test_redeem_throws_when_balance_is_insufficient()
    {
        $member = $this->makeMember();
        $service = app(PointService::class);
        $event = $this->makeEvent(pointCost: 100);

        $service->reward($member, 10, null, null);

        $this->expectException(InsufficientPointsException::class);
        $service->redeemForEvent($member, $event);
    }

    public function test_reward_rejects_a_non_positive_amount()
    {
        $member = $this->makeMember();
        $service = app(PointService::class);

        $this->expectException(\InvalidArgumentException::class);
        $service->reward($member, 0, null, null);
    }
}
