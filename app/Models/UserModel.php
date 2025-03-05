<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'users';
    public $timestamps = false;
    protected $primaryKey = 'id'; // Pastikan Laravel tahu primary key-nya
    public $incrementing = false; // Matikan auto-increment
    protected $keyType = 'string'; // Karena UUID berupa string
    protected $hidden = ['password', 'ip'];

    public function getTheaterVisitAttribute()
    {
        return TheaterVisitModel::where('user_id', $this->id)->where('status', 'VERIFIED')->count();
    }

    public function getTotalTwoShotAttribute()
    {
        return TwoShotModel::where('user_id', $this->id)->count();
    }
}
