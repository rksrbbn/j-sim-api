<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemsModel extends Model
{
    protected $guarded = [];
    protected $table = 'items';
    public $timestamps = false;
    protected $primaryKey = 'id'; // Pastikan Laravel tahu primary key-nya
    public $incrementing = false; // Matikan auto-increment
    protected $keyType = 'string'; // Karena UUID berupa string
}
