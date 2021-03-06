<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['shop_id','name' ,'price','stock' ,'video','status' ,'created_at' ,'updated_by' ,'updated_at'];
}
