<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Inventaris; // model database
use Illuminate\Http\Request;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Generator;
use Illuminate\Support\Facades\Storage;

class InventarisController extends Controller
{
    public function showAllInventaris()
    {
        $all = DB::select("SELECT * FROM rb_inventaris");
        
        return response()->json($all);
    }

    public function showSpecificInventaris(Request $request)
    {
        $nomor_barang = $request->input('nomor_barang');
        $product= Inventaris::WHERE('rb_inventaris.nomor_barang', $nomor_barang)->first();
        if($product){
            return response()->json($product);
        }
        else {
            return response()->json(['success' => 'error', 'message' => "Nomor Inventaris Tidak Ditemukan"], 400);
        }
    } 

    public function insertInventaris(Request $request)
    {
        $nama_barang = $request->input('nama_barang');
        $nomor_barang = $request->input('nomor_barang');
        $tanggal_pembukuan = $request->input('tanggal_pembukuan');
        $lokasi = $request->input('lokasi');
        $longitude = $request->input('longitude');
        $latitude= $request->input('latitude');
        
        $nomor_exist= DB::table('rb_inventaris')->where('nomor_barang',$nomor_barang)->first();

        if($nomor_exist){
            return $this->responseRequestError('Nomor sudah terdaftar');

        } else{
        if ($request->hasFile('image')) {
            $original_filename = $request->file('image')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = './upload/user/';
            $destinationQr_path = './upload/qr/';
            $image = 'U-' . time() . '.' . $file_ext;
            
            $imageQr = QrCode::format('png')
                    ->merge('./ptpn1.png', 0.5, true)
                    ->size(200)->errorCorrection('H')
                    ->generate($nomor_barang);
            //return response($image)->header('Content-type','image/png');
            $nameQr = 'img-' . $nomor_barang . '.png';
            $output_file = './upload/qr/' . $nameQr;
            Storage::disk('public')->put($output_file, $imageQr);
            

            if ($request->file('image')->move($destination_path, $image)) {
                //$user_profile ->foto= '/upload/user/' . $image;
                DB::table('rb_inventaris')->insert(
                    ['image' => '/upload/user/' . $image, 
                    'nama_barang' => $nama_barang,
                    'nomor_barang' => $nomor_barang,
                    'tanggal_pembukuan' => $tanggal_pembukuan,
                    'lokasi' => $lokasi,
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'image_qrcode' => '/storage/app/public/upload/qr/'.$nameQr
                    ]
                
                );

                $user_profile2 = DB::table('rb_inventaris')->where('nomor_barang',$nomor_barang)->first();
                return $this->responseRequestSuccess($user_profile2);
            } else {
                return $this->responseRequestError('Cannot upload file');
            }
        } else if ($nomor_barang != null){

            $imageQr = QrCode::format('png')
            ->merge('./ptpn1.png', 0.5, true)
            ->size(200)->errorCorrection('H')
            ->generate($nomor_barang);
            //return response($image)->header('Content-type','image/png');
            $nameQr = 'img-' . $nomor_barang . '.png';
            $output_file = './upload/qr/' . $nameQr;
            Storage::disk('public')->put($output_file, $imageQr);
            //$user_p$user_profilerofile ->foto= '/upload/user/' . $image;
            DB::table('rb_inventaris')->insert(
                ['nama_barang' => $nama_barang,
                'nomor_barang' => $nomor_barang,
                'tanggal_pembukuan' => $tanggal_pembukuan,
                'lokasi' => $lokasi,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'image_qrcode' => '/storage/app/public/'.$nameQr
                ]
            
            );
            $user_profile2 = DB::table('rb_inventaris')->where('nomor_barang',$nomor_barang)->first();
            return $this->responseRequestSuccess($user_profile2);
        } 
        else {
            
            return $this->responseRequestError('File not found');
        }
    }
    }

    protected function responseRequestSuccess($ret)
    {
        return response()->json(['success' => "true",'message' => 'Success!'], 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    protected function responseRequestError($message = 'Bad request', $statusCode = 400)
    {
        return response()->json(['success' => 'error', 'message' => $message], $statusCode)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
    

}
