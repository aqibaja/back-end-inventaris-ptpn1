<?php
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Generator;
//use SimpleSoftwareIO\QrCode\Facade as QrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//mengambil key app
$router->get('/key', function(){
    $key = Str::random(32);
    return $key;
});

$router->get('/qrcode-with-image', function () {
    $image = QrCode::format('png')
                    ->merge('./ptpn1.png', 0.5, true)
                    ->size(500)->errorCorrection('H')
                    ->generate('A simple example of QR code!');
 return response($image)->header('Content-type','image/png');
});

Route::get('generate-qr-code', function(){
    $image = QrCode::format('png')
                    ->merge('./ptpn1.png', 0.5, true)
                    ->size(150)->errorCorrection('H')
                    ->generate('A simple example of QR code!');
    $output_file = './upload/qr/img-' . time() . '.png';
    //Storage::disk('local')->put($output_file, $image);
   // QrCode::generate('My First QR code', public_path('qrcodes/qrcode.svg'));
   // $qr = QrCode::size(150)->generate('A basic example of QR code!');
    return view('qrcode.index', [
        'qr' => $image 
    ]);
});

//rest api
$router->group(['prefix' => 'api'], function () use ($router) {

    //mengambil kategori dari database
    $router->get('inventaris',  ['uses' => 'InventarisController@showAllInventaris']);
  
    $router->post('inventaris-detail', ['uses' => 'InventarisController@showSpecificInventaris']);
  
    $router->post('inventaris', ['uses' => 'InventarisController@insertInventaris']);
  
    $router->delete('inventaris/{id}', ['uses' => 'KategoriController@delete']);
  
    $router->put('inventaris/{id}', ['uses' => 'KategoriController@update']);

  

  });