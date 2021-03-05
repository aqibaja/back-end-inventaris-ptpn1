<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model 
{
    protected $table = "rb_inventaris"; //name table di database
    //protected $fillable = ["id_kategori_produk", "nama_kategori", "kategori_seo", "gambar", "icon_kode" ,"icon_image", "urutan"];
    //protected $hidden = [];

    //query didalam sebuah method
    public function scopeWithid($query, $id){
        return $query->where('id', $id);
    }

    public function getName(){
        return $this->name_kategori;
    }

}