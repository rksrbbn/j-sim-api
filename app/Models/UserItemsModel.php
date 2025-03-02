<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserItemsModel extends Model
{
    protected $guarded = [];
    protected $table = 'user_items';
    public $timestamps = false;
    protected $primaryKey = 'id'; // Pastikan Laravel tahu primary key-nya
    public $incrementing = false; // Matikan auto-increment
    protected $keyType = 'string'; // Karena UUID berupa string

    public function dataItems()
    {
        return $this->hasOne(ItemsModel::class, 'id', 'item_id');
    }
}
