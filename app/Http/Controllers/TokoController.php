<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\User;
use App\Models\FotoToko;
use Illuminate\Http\Request;

class TokoController extends Controller
{
    public function collection($toko, $dataCollection){
        $dataCollection->put('nama', $toko->nama);
        $dataCollection->put('alamat', $toko->alamat);
        $dataCollection->put('kota', $toko->kota);
        $dataCollection->put('provinsi', $toko->provinsi);
        $dataCollection->put('pemilik', $toko->user->name);

        $fotoArray = [];

        foreach ($toko->fotoToko as $foto) {
            $fotoCollection = collect([]);
            $fotoCollection->put('url', $foto->url);
            array_push($fotoArray, $fotoCollection);
        }
        $dataCollection->put('foto_toko', $fotoArray);
    }

    public function index(){
        $toko = Toko::all();
        
        $data = [];
        
        foreach ($toko as $toko) {
            $dataCollection = collect([]);

            $this->collection($toko, $dataCollection);
            
            array_push($data, $dataCollection);
        }

        $response = [
            'status' => 'Sukses!',
            'message' => 'Get list toko sukses!',
            'data' => $data
        ];

        return response($response, 200);
    }

    public function show($id){
        $toko = Toko::find($id);

        if (!$toko) {
            $response = [
                'status' => 'Gagal',
                'message' => 'Toko not found',
            ];
            return response($response, 404);
        }

        $dataCollection = collect([]);

        $this->collection($toko, $dataCollection);

        $response = [
            'status' => 'Sukses',
            'message' => 'Get toko detail sukses',
            'data' => $dataCollection
        ];
        return response($response, 200);
    }

    public function store(Request $request){
        $this->validate($request, [
            'nama' => ['required', 'string', 'max:50'],
            'alamat' => ['required', 'string'],
            'kota' => ['required', 'string', 'max:30'],
            'provinsi' => ['required', 'string', 'max:30'],
            'id_pemilik' => ['required', 'numeric'],
            'foto_toko' => ['required', 'array', 'max:3']
        ]);

        $checkIdUser = User::find($request->id_pemilik);
        
        if (!$checkIdUser) {
            $response = [
                'status' => 'Gagal',
                'message' => 'ID user tidak ditemukan'
            ];
            return response($response, 400);
        }

        $jumlahToko = $checkIdUser->toko;
        
        if (count($jumlahToko) >= 3) {
            $response = [
                'status' => 'Gagal',
                'message' => 'User ini telah memiliki 3 Toko'
            ];
            return response($response, 400);
        }
        
        $toko = Toko::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
            'id_pemilik' => $request->id_pemilik
        ]);

        /* Insert Foto toko && return response*/
        foreach ($request->foto_toko as $foto) {
            $fotoToko = FotoToko::create([
                'id_toko' => $toko->id,
                'url' => $foto
            ]);
        }

        $response = [
            'status' => 'Sukses!',
            'message' => 'Create toko sukses!',
            'data' => [
                'nama' => $toko->nama,
                'alamat' => $toko->alamat,
                'kota' => $toko->kota,
                'provinsi' => $toko->provinsi,
                'pemilik' => $toko->user->name,
                'foto_toko' => $request->foto_toko
            ]
        ];
        
        return response($response, 200);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'nama' => ['string', 'max:50'],
            'alamat' => ['string'],
            'kota' => ['string', 'max:30'],
            'provinsi' => ['string', 'max:30'],
            'id_pemilik' => ['numeric'],
            'foto_toko' => ['array', 'max:3']
        ]);

        $toko = Toko::find($id);

        if (!$toko) {
            $response = [
                'status' => 'Gagal',
                'message' => 'Toko not found',
            ];
            return response($response, 404);
        }

        if ($request->id_pemilik) {
            $checkIdUser = User::find($request->id_pemilik);
    
            if (!$checkIdUser) {
                $response = [
                    'status' => 'Gagal',
                    'message' => 'User not found!'
                ];
                return response($response, 400);
            }
    
            if (count($checkIdUser->toko) >= 3) {
                $response = [
                    'status' => 'Gagal',
                    'message' => 'User ini telah memiliki 3 Toko'
                ];
                return response($response, 400);
            }

            $toko->id_pemilik = $request->id_pemilik;
        }

        if ($request->foto_toko) {
            $foto_toko = FotoToko::where('id_toko', $id)->delete();

            foreach ($request->foto_toko as $foto) {
                $fotoToko = FotoToko::create([
                    'id_toko' => $toko->id,
                    'url' => $foto
                ]);
            }            
        }

        $toko->nama = $request->nama ? $request->nama : $toko->nama;
        $toko->alamat = $request->alamat ? $request->alamat : $toko->alamat;
        $toko->kota = $request->kota ? $request->kota : $toko->kota;
        $toko->provinsi = $request->provinsi ? $request->provinsi : $toko->provinsi;
        $toko->save();


        $response = [
            'status' => 'Sukses!',
            'message' => 'Edit toko sukses!',
            'data' => [
                'nama' => $toko->nama,
                'alamat' => $toko->alamat,
                'kota' => $toko->kota,
                'provinsi' => $toko->provinsi,
                'pemilik' => $toko->user->name,
                'foto_toko' => $toko->fotoToko
            ]
        ];

        return response($response, 200);
    }
}
