<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\Produk;
use App\Models\FotoProduk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function collection($produk, $dataCollection){
        $dataCollection->put('nama_toko', $produk->toko->nama);
        $dataCollection->put('nama', $produk->nama);
        $dataCollection->put('harga', $produk->harga);
        $dataCollection->put('nomor_rak', $produk->nomor_rak);

        $fotoArray = [];

        foreach ($produk->fotoProduk as $foto) {
            $fotoCollection = collect([]);
            $fotoCollection->put('url', $foto->url);
            array_push($fotoArray, $fotoCollection);
        }
        $dataCollection->put('foto_produk', $fotoArray);
    }

    public function index(){
        $produk = Produk::all();

        $data = [];

        foreach ($produk as $produk) {
            $dataCollection = collect([]);

            $this->collection($produk, $dataCollection);

            array_push($data, $dataCollection);
        }

        $response = [
            'status' => 'Sukses!',
            'message' => 'Get list produk sukses!',
            'data' => $data
        ];

        return response($response, 200);
    }

    public function show($id){
        $produk = Produk::find($id);

        if (!$produk) {
            $response = [
                'status' => 'Gagal',
                'message' => 'Produk not found',
            ];
            return response($response, 404);
        }

        $dataCollection = collect([]);

        $this->collection($produk, $dataCollection);

        $response = [
            'status' => 'Sukses',
            'message' => 'Get produk detail sukses',
            'data' => $dataCollection
        ];
        return response($response, 200);
    }

    public function show_produk_toko($id){
        $produk = Produk::where('id_toko', $id)->get();
        
        if (count($produk) < 1) {
            $response = [
                'status' => 'Gagal',
                'message' => 'Toko not found',
            ];
            return response($response, 404);
        }

        $data = [];

        foreach ($produk as $produk) {
            $dataCollection = collect([]);

            $this->collection($produk, $dataCollection);

            array_push($data, $dataCollection);
        }

        $response = [
            'status' => 'Sukses!',
            'message' => 'Get list produk suatu toko sukses!',
            'data' => $data
        ];

        return response($response, 200);
    }

    public function store(Request $request){
        $this->validate($request, [
            'id_toko' => ['required', 'numeric'],
            'nama' => ['required', 'string', 'max:50'],
            'harga' => ['required', 'numeric'],
            'stok' => ['required', 'numeric'],
            'nomor_rak' => ['required', 'numeric'],
            'foto_produk' => ['required', 'array', 'max:3']
        ]);

        $checkIdToko = Toko::find($request->id_toko);

        if (!$checkIdToko) {
            $response = [
                'status' => 'Gagal',
                'message' => 'ID toko tidak ditemukan'
            ];
            return response($response, 400);
        }

        $jumlahProdukToko = count($checkIdToko->produk);

        if ($jumlahProdukToko >= 100) {
            $response = [
                'status' => 'Gagal',
                'message' => 'Toko ini telah memiliki 100 Produk'
            ];
            return response($response, 400);
        }

        $produk = Produk::create([
            'id_toko' => $request->id_toko,
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'nomor_rak' => $request->nomor_rak
        ]);

        foreach ($request->foto_produk as $foto) {
            $fotoProduk = FotoProduk::create([
                'id_produk' => $produk->id,
                'url' => $foto,
            ]);
        }

        $response = [
            'status' => 'Sukses!',
            'message' => 'Create produk sukses!',
            'data' => [
                'nama_toko' => $produk->toko->nama,
                'nama' => $request->nama,
                'harga' => $request->harga,
                'stok' => $request->stok,
                'nomor_rak' => $request->nomor_rak,
                'foto_produk' => $request->foto_produk
            ]
        ];

        return response($response, 200);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'id_toko' => ['numeric'],
            'nama' => ['string', 'max:50'],
            'harga' => ['numeric'],
            'stok' => ['numeric'],
            'nomor_rak' => ['numeric'],
            'foto_produk' => ['array', 'max:3']
        ]);

        $produk = Produk::find($id);

        if (!$produk) {
            $response = [
                'status' => 'Gagal',
                'message' => 'Produk not found',
            ];
            return response($response, 404);
        }

        if ($request->id_toko) {
            $checkIdToko = Toko::find($request->id_toko);

            if (!$checkIdToko) {
                $response = [
                    'status' => 'Gagal',
                    'message' => 'Toko not found!'
                ];
                return response($response, 400);
            }

            if(count($checkIdToko->produk) >=100){
                $response = [
                    'status' => 'Gagal',
                    'message' => 'Toko ini telah memiliki 100 Produk'
                ];
                return response($response, 400);
            }

            $produk->id_toko = $request->id_toko;
        }

        if ($request->foto_produk) {
            $foto_produk = FotoProduk::where('id_produk', $id)->delete();

            foreach ($request->foto_produk as $foto) {
                $fotoProduk = FotoProduk::create([
                    'id_produk' => $produk->id,
                    'url' => $foto
                ]);
            }            
        }        

        $produk->nama = $request->nama ? $request->nama : $produk->nama;
        $produk->harga = $request->harga ? $request->harga : $produk->harga;
        $produk->stok = $request->stok ? $request->stok : $produk->stok;
        $produk->nomor_rak = $request->nomor_rak ? $request->nomor_rak : $produk->nomor_rak;
        $produk->save();

        $response = [
            'status' => 'Sukses!',
            'message' => 'Edit produk sukses!',
            'data' => [
                'nama_toko' => $produk->toko->nama,
                'nama' => $produk->nama,
                'harga' => $produk->harga,
                'stok' => $produk->stok,
                'nomor_rak' => $produk->nomor_rak,
                'foto_produk' => $produk->fotoProduk
            ]
        ];

        return response($response, 200);
    }
}
