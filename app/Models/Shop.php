<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    protected $table = 'shop';

            
    protected $fillable = [ 'name','image' ,'address' , 'email','status' ,'created_by' ,'created_at' ,'updated_by' ,'updated_at'];
}
