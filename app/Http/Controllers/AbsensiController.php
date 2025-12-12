<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function index()
    {
        return view('backend.absensi.index');
    }

    public function getData(Request $request)
    {
        $keyword = $request->keyword;

        $data = Absensi::with('user')
            ->whereHas('user', function($q) use ($keyword){
                if ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                }
            })
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'foto'     => 'required',
            'latitude' => 'required',
            'longitude'=> 'required',
        ]);

        // simpan foto (base64)
        $image = $request->foto;
        $imageName = time().".png";
        Storage::put("public/absensi/".$imageName, base64_decode(explode(",", $image)[1]));

        Absensi::updateOrCreate(
            ['id' => $request->id],
            [
                'foto'      => $imageName,
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'user_id'   => auth()->user()->id ?? 1,
                'datetime'  => now(),
            ]
        );

        return response()->json(['status' => true]);
    }

    public function delete(Request $request)
    {
        Absensi::find($request->id)->delete();

        return response()->json([
            'responCode' => 1,
            'respon' => 'Absensi berhasil dihapus'
        ]);
    }
}

