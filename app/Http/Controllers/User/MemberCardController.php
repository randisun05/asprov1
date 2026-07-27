<?php

namespace App\Http\Controllers\User;

use App\Models\Member;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProfileDataMain;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use F9WebLtd\QrCode\Facades\QrCode;


class MemberCardController extends Controller
{
    public function index()
    {
        if (auth()->guard('member')->check()) {
            $profile = Member::where('id', auth()->guard('member')->id())->first();
            $foto = ProfileDataMain::where('nip', $profile->nip)->first('image');

            // Check if the member exists and has a qr_link
            if ($profile && $profile->qr_link) {
                $qrLink = "https://asprosdma.id/identity-verification/" . $profile->qr_link;
            } elseif ($profile && !$profile->qr_link) {
                // Generate a new qr_link if the member does not have one
                // Buat QR Link
            $link = (string) Str::uuid();
            $profile->qr_link = $link;
            $profile->save();
            $qrLink = "https://asprosdma.id/identity-verification/" . $link;
            } else {
                // Handle the case where the member or qr_link is not found
                return response('QR link not found', 404);
            }

            // Generate the QR code in SVG format
            $qrCode = QrCode::format('svg')->size(75)->generate($qrLink);

            return inertia('User/MemberCard/Index', [
             'profile' => $profile,
             'qrCode' => (string) $qrCode, // Konversi menjadi string biasa
                'foto' => $foto,
                'qrLink' => $qrLink

            ]);
        }
    }

    public function generateQRCode()
    {

        // Retrieve the member where no_member matches the request text
        $member =  Member::where('id', auth()->guard('member')->id())->first();

        // Check if the member exists and has a qr_link
        if ($member && $member->qr_link) {
            $qrLink = "https://asprosdma.id/identity-verification/" . $member->qr_link;
        } elseif ($member && !$member->qr_link) {
            // Generate a new qr_link if the member does not have one
            $link = (string) Str::uuid();
            $member->qr_link = $link;
            $member->save();
            $qrLink = "https://asprosdma.id/identity-verification/" . $link;

        } else {
            // Handle the case where the member or qr_link is not found
            return response('QR link not found', 404);
        }

        // // Generate the QR code using the retrieved qr_link
        // $qrCode = QrCode::size(300)->generate($qrLink);

        // return response($qrCode, 200)->header('Content-Type', 'image/svg+xml');

        // Generate the QR code using the retrieved qr_link
        $qrCode = QrCode::format('png')->size(300)->generate($qrLink);

            return response($qrCode, 200)->header('Content-Type', 'image/png');
    }


    public function edit()
    {
        if (auth()->guard('member')->check()) {
            $profile = Member::where('id', auth()->guard('member')->id())->first();
            $foto = ProfileDataMain::where('nip', $profile->nip)->first('image');

             // Check if the member exists and has a qr_link
             if ($profile && $profile->qr_link) {
                $qrLink = "https://asprosdma.id/identity-verification/" . $profile->qr_link;
            } elseif ($profile && !$profile->qr_link) {
                // Generate a new qr_link if the member does not have one
                // Buat QR Link
             $link = (string) Str::uuid();
            $profile->qr_link = $link;
            $profile->save();
            $qrLink = "https://asprosdma.id/identity-verification/" . $link;
            } else {
                // Handle the case where the member or qr_link is not found
                return response('QR link not found', 404);
            }

            // Generate the QR code in SVG format
            $qrCode = QrCode::format('svg')->size(75)->generate($qrLink);


            return inertia('User/MemberCard/Edit', [
             'profile' => $profile,
                'foto' => $foto,
                'qrCode' => (string) $qrCode,

            ]);
        }
    }

    public function download()
    {
        if (auth()->guard('member')->check()) {
            $profile = Member::where('id', auth()->guard('member')->id())->first();
            $foto = ProfileDataMain::where('nip', $profile->nip)->first('image');

            // Check if the member exists and has a qr_link
            if ($profile && $profile->qr_link) {
                $qrLink = "https://asprosdma.id/identity-verification/" . $profile->qr_link;
            } elseif ($profile && !$profile->qr_link) {
                // Generate a new qr_link if the member does not have one
                $link = (string) \Illuminate\Support\Str::uuid();
                $profile->qr_link = $link;
                $profile->save();
                $qrLink = "https://asprosdma.id/identity-verification/" . $link;
            } else {
                // Handle the case where the member or qr_link is not found
                return response('QR link not found', 404);
            }

            // Generate the QR code using the retrieved qr_link
            $qrCode = QrCode::size(200)->generate($qrLink);

            // Render the view with the profile and qrCode
            return view('Layouts/Components/MemberCard', compact('profile', 'qrCode', 'foto'));
        }
    }

    public function saveMemberCard(Request $request)
    {
        $imageData = $request->input('image');
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageName = 'member-card-' . time() . '.png';

        Storage::disk('public')->put($imageName, base64_decode($imageData));

        return response()->json(['success' => true, 'message' => 'Image saved successfully']);
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

        return redirect()->route('user.card.index');
}

public function downloadBackcard()
{
    // Path ke file back-card.png
    $filePath = public_path('assets/images/back-card.png');

    // Periksa apakah file ada
    if (file_exists($filePath)) {
        return response()->download($filePath);
    }

    // Jika file tidak ditemukan, berikan respons 404
    return response('File not found', 404);
}

}
