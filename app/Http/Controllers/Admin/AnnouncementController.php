<?php

namespace App\Http\Controllers\Admin;

use App\Models\Announcement;
use App\Models\MemberNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::when(request()->q, function ($query) {
            $query->where('title', 'like', '%' . request()->q . '%');
        })
            ->latest()
            ->paginate(10);

        $announcements->appends(['q' => request()->q]);

        return inertia('Admin/Announcements/Index', [
            'announcements' => $announcements,
        ]);
    }

    public function create()
    {
        return inertia('Admin/Announcements/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'link' => 'nullable|string',
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'body' => $request->body,
            'link' => $request->link,
            'is_active' => true,
        ]);

        MemberNotification::broadcast(
            'announcement',
            'Pengumuman: ' . $announcement->title,
            $announcement->body,
            $announcement->link ?: '/user/notifications',
            $announcement
        );

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);

        return inertia('Admin/Announcements/Edit', [
            'announcement' => $announcement,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'link' => 'nullable|string',
        ]);

        $announcement = Announcement::findOrFail($id);
        $announcement->update([
            'title' => $request->title,
            'body' => $request->body,
            'link' => $request->link,
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function status($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update(['is_active' => !$announcement->is_active]);

        return redirect()->route('admin.announcements.index')->with('success', 'Status pengumuman berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Announcement::findOrFail($id)->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
