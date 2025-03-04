<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BondingsModel extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'bondings';
    public $timestamps = false;
    protected $primaryKey = 'id'; // Pastikan Laravel tahu primary key-nya
    public $incrementing = false; // Matikan auto-increment
    protected $keyType = 'string'; // Karena UUID berupa string

}
