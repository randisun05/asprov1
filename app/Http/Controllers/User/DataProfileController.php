<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\instansi;
use App\Models\Member;
use App\Models\ProfileDataMain;
use App\Models\ProfileDataPosition;
use App\Models\refCity;
use App\Models\refProvince;
use FontLib\Table\Type\name;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Intervention\Image\ImageManager;
use Intervention\Image\Laravel\Facades\Image;


class DataProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (auth()->guard('member')->check()) {

        $main = ProfileDataMain::where('nip',auth()->guard('member')->user()->nip)
        ->first();

        $data = ProfileDataPosition::with('main')
        ->where('main_id',$main->id)
        ->first();

        return inertia('User/DataProfile/Index', [
           'data' => $data,
        ]);

        } else {
            return redirect()->route('login');
        }
    }

    public function indexIndiv()
    {
        if (!auth()->guard('member')->check())
        {
            return redirect()->route('login');
        }
        $main = ProfileDataMain::where('nip',auth()->guard('member')->user()->nip)
        ->first();

        $data = ProfileDataPosition::with('main')
        ->where('main_id',$main->id)
        ->first();

        return inertia('User/DataProfile/IndexIndiv', [
           'data' => $data,
        ]);
    }

    public function indexPosition()
    {
        if (!auth()->guard('member')->check())
        {
            return redirect()->route('login');
        }

        $main = ProfileDataMain::where('nip',auth()->guard('member')->user()->nip)
        ->first();

        $data = ProfileDataPosition::with('main')
        ->where('main_id',$main->id)
        ->first();

        return inertia('User/DataProfile/IndexPosition', [
           'data' => $data,
        ]);
    }

    public function indexOther()
    {
        if (!auth()->guard('member')->check())
        {
            return redirect()->route('login');
        }

        $main = ProfileDataMain::where('nip',auth()->guard('member')->user()->nip)
        ->first();

        $data = ProfileDataPosition::with('main')
        ->where('main_id',$main->id)
        ->first();

        return inertia('User/DataProfile/IndexOther', [
           'data' => $data,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        if (!auth()->guard('member')->check())
        {
            return redirect()->route('login');
        }

        $main = ProfileDataMain::where('nip',auth()->guard('member')->user()->nip)
        ->first();

        $data = ProfileDataPosition::with('main')
        ->where('main_id',$main->id)
        ->first();

        $instansis = instansi::get();
        return inertia('User/DataProfile/Edit', [
           'data' => $data,
           'instansis' => $instansis,
        ]);
    }

    public function editPosition()
    {
        if (!auth()->guard('member')->check())
        {
            return redirect()->route('login');
        }

        $main = ProfileDataMain::where('nip',auth()->guard('member')->user()->nip)
        ->first();

        $data = ProfileDataPosition::with('main')
        ->where('main_id',$main->id)
        ->first();
        $instansis = instansi::get();

        return inertia('User/DataProfile/EditPosition', [
           'data' => $data,
           'instansis' => $instansis,

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        if (!auth()->guard('member')->check())
        {
            return redirect()->route('login');
        }

          $request->validate([
                'nip' => ['required','string', 'regex:/^\d{18}$/'],
                'name' => 'required',
                'leveledu' => 'required',
                'lastedu' => 'required',
                'place' => 'required',
                'dob' => 'required',
                // 'docid' => 'required',
                // 'nodocid' => 'required',
                'email' => 'required|email|',
                'contact' => 'required|string|',
                'gender' => 'required',
                // 'address' => 'required',
                // 'villages' => 'required',
                // 'district' => 'required',
                // 'regency' => 'required',
                // 'province' => 'required',
                'religion' => 'required',
                'agency' => 'required',
        ],[
            'nip.regex' => 'NIP harus terdiri dari 18 angka.',
            // 'nip.unique' => 'Data NIP sudah digunakan.',
            // 'email.unique' => 'Data email sudah digunakan.',
            // 'contact.unique' => 'Data kontak sudah digunakan.',
            'nip.required' => 'NIP harus diisi.',
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'contact.required' => 'Kontak harus diisi.',
            'agency.required' => 'Instansi harus diisi.',
            'gender.required' => 'Jenis kelamin harus diisi.',
            'leveledu.required' => 'Jenjang pendidikan harus diisi.',
            'lastedu.required' => 'Pendidikan terakhir harus diisi.',
            'place.required' => 'Tempat lahir harus diisi.',
            'dob.required' => 'Tanggal lahir harus diisi.',
            // 'docid.required' => 'Jenis dokumen identitas harus diisi.',
            // 'nodocid.required' => 'Nomor dokumen identitas harus diisi.',
            // 'address.required' => 'Alamat harus diisi.',
            // 'villages.required' => 'Desa/Kelurahan harus diisi.',
            // 'district.required' => 'Kecamatan harus diisi.',
            // 'regency.required' => 'Kabupaten/Kota harus diisi.',
            // 'province.required' => 'Provinsi harus diisi.',
            'religion.required' => 'Agama harus diisi.',
        ]);

        $main = ProfileDataMain::where('nip',auth()->guard('member')->user()->nip)
        ->first();

        //update data main
        $main->update([
                'nip' => $request->nip,
                'name' => $request->name,
                'fname' => $request->fname,
                'lname' => $request->lname,
                'leveledu' => $request->leveledu,
                'lastedu' => $request->lastedu,
                'place' => $request->place,
                'dob' => $request->dob,
                // 'docid' => $request->docid,
                // 'nodocid' => $request->nodocid,
                'email' => $request->email,
                'contact' => $request->contact,
                'gender' => $request->gender,
                // 'address' => $request->address,
                // 'villages' => $request->villages,
                // 'district' => $request->district,
                // 'regency' => $request->regency,
                // 'province' => $request->province,
                'religion' => $request->religion,

        ]);

        Member::where('id',auth()->guard('member')->user()->id)
        ->update([
            'name' => $request->name,
            'email' => $request->email,
            'agency' => $request->agency,
        ]);


        $position = ProfileDataPosition::where('main_id',$main->id)
        ->first();

        $position->update([
                'agency' => $request->agency,
             ]);

             return redirect()->route('user.profile')->with('success','data berhasil diupdate');
    }

    public function updatePosition(Request $request)
    {
        if (!auth()->guard('member')->check())
        {
            return redirect()->route('login');
        }

          $request->validate([
                'type' => 'required',
                'status' => 'required',
                'agency' => 'required',
                'unit' => 'required',
                'subunit' => 'required',
                'position' => 'required',
                'level' => 'required',
                'location' => 'required',
                'tmtpos' => 'required',
                'golru' => 'required',
                'tmtgolru' => 'required',

        ],[
                'type.required' => 'Jenis ASN harus diisi.',
                'status.required' => 'Status Kepegawaian harus diisi.',
                'agency.required' => 'Instansi harus diisi.',
                'unit.required' => 'Unit organisasi harus diisi.',
                'subunit.required' => 'Sub unit organisasi harus diisi.',
                'position.required' => 'Jabatan harus diisi.',
                'level.required' => 'Jenjang harus diisi.',
                'location.required' => 'Penempatan harus diisi.',
                'tmtpos.required' => 'TMT jabatan harus diisi.',
                'golru.required' => 'Golru harus diisi.',
                'tmtgolru.required' => 'TMT golru harus diisi.',

        ]);

        $main = ProfileDataMain::where('nip',auth()->guard('member')->user()->nip)
        ->first();

        $position = ProfileDataPosition::where('main_id',$main->id)
        ->first();

        $position->update([
                'agency' => $request->agency,
                'type' => $request->type,
                'status' => $request->status,
                'unit' => $request->unit,
                'subunit' => $request->subunit,
                'position' => $request->position,
                'level' => $request->level,
                'location' => $request->location,
                'tmtpos' => $request->tmtpos,
                'golru' => $request->golru,
                'tmtgolru' => $request->tmtgolru,
                // 'wyear' => $request->wyear,
                // 'wmonth' => $request->wmonth,
             ]);

             return redirect()->route('user.profile.jabatan');
    }

        public function updateImage(Request $request)
        {
            if (!auth()->guard('member')->check())
        {
            return redirect()->route('login');
        }

            $image = $request->file('image');
            if ($image) {
                $image = $request->file('image')->storePublicly('/images');
                // Proceed with storing or processing the uploaded file
            };
            $main = ProfileDataMain::where('nip',auth()->guard('member')->user()->nip)
            ->first();
            //update data main
            $main->update([
                    'image' => $image,
            ]);

            return Inertia::location('/user/profile/edit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
