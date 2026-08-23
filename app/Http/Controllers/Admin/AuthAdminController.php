<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $users = User::
        when(request()->q, function($query) {
            $query->where('name', 'like', '%' . request()->q . '%');
        })
        ->latest()
        ->paginate(10);

        $users->appends(['q' => request()->q]);


        return inertia('Admin/Setting/Index', [
            'users' => $users,
         ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return inertia('Admin/Setting/Create', [

         ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request including file validation
      $request->validate([
        'nip' => 'required|string|unique:users,nip',
        'name' => 'required|',
        'email' => 'required|email|unique:users,email',
        'role' => ['required', Rule::in(array_keys(config('roles')))],
        'password' => 'required|confirmed',
        'position' => 'required',
    ]);

        $password = Hash::make($request->password);
        User::create([
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => $request->email,
            'role' =>  $request->role,
            'password' => $password,
            'position' => $request->position,
            'ref' => in_array($request->position, ['kabid', 'bendahara', 'sekretaris']) ? 1 : ($request->position === 'anggota' ? 2 : 3),
        ]);


     //redirect
     return redirect()->route('admin.setting.index')->with('success', 'Akun admin berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return inertia('Admin/Setting/Edit', [
            'user' => $user,
         ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        // Validate request including file validation
      $request->validate([
        'nip' => ['required', 'string', Rule::unique('users', 'nip')->ignore($id)],
        'name' => 'required|',
        'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
        'role' => ['required', Rule::in(array_keys(config('roles')))],
        'password' => 'nullable|confirmed',
        'position' => 'required',
    ]);

        $data = [
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => $request->email,
            'role' =>  $request->role,
            'position' => $request->position,
            'ref' => in_array($request->position, ['kabid', 'bendahara', 'sekretaris']) ? 1 : ($request->position === 'anggota' ? 2 : 3),
        ];

        // Only touch the password when a new one was actually submitted -
        // otherwise every profile edit forced a password reset.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        User::where('id', $id)->update($data);


     //redirect
     return redirect()->route('admin.setting.index')->with('success', 'Akun admin berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->route('admin.setting.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        //redirect
        return redirect()->route('admin.setting.index')->with('success', 'Akun admin berhasil dihapus.');
    }
}
