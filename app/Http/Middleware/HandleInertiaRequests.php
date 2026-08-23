<?php

namespace App\Http\Middleware;

use App\Models\MemberNotification;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'session' => [
                'status'    => fn () => $request->session()->get('status'),
                'success'   => fn () => $request->session()->get('success'),
                'error'     => fn () => $request->session()->get('error'),
                'info'      => fn () => $request->session()->get('info'),
                'warning'   => fn () => $request->session()->get('warning'),
            ],
            //user authenticated
            'auth'  =>[
                'user'          => auth()->user() ?   auth()->user() : null,
                'member'          => auth()->guard('member')->user() ?   auth()->guard('member')->user() : null,
            ],
            'notifications' => fn () => $this->notificationsForNavbar(),
        ]);
    }

    /**
     * Unread count + latest few, for the bell icon in the member navbar.
     * Cheap indexed queries, only run when a member is actually logged in.
     */
    private function notificationsForNavbar(): array
    {
        $member = auth()->guard('member')->user();

        if (!$member) {
            return ['unreadCount' => 0, 'latest' => []];
        }

        $readIds = $member->notificationReads()->pluck('member_notification_id');

        return [
            'unreadCount' => MemberNotification::whereNotIn('id', $readIds)->count(),
            'latest' => MemberNotification::latest('id')->take(5)->get()->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'link' => $n->link,
                'is_read' => $readIds->contains($n->id),
                'created_at' => $n->created_at,
            ]),
        ];
    }
}
