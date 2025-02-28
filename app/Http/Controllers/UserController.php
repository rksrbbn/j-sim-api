<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        $users = UserModel::all();

        $hasil = [
            'data' => $users
        ];
        return response()->json($hasil);
    }
}
