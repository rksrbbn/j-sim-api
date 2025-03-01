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
            'code' => 200,
            'data' => $users,
            'message' => 'Berhasil Mendapatkan Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }

    public function getUserProfile(Request $request)
    {
        $user = $request->user();

        $hasil = [
            'code' => 200,
            'data' => $user,
            'message' => 'Berhasil Mendapatkan Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }

    public function getUserDetail(Request $request)
    {
        
        if (empty($request->id))
        {
            return response()->json(['code' => 400, 'data' => null, 'message' => 'Id tidak ditemukan'], 400);
        }
        $user = UserModel::find($request->id);

        $hasil = [
            'code' => 200,
            'data' => $user,
            'message' => 'Berhasil Mendapatkan Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }

    public function updateUser(Request $request)
    {
        
        $user = $request->user();

        $params = [
            'username' => $request->username,
            'money' => $request->money,
            'fame' => $request->fame,
            'type' => $request->type,
            'tier' => $request->tier,
            'days' => $request->days,
        ];

        $update = UserModel::where('id', $user->id)->update($params);

        // if (!$update)
        // {
        //     return response()->json( [
        //         'code' => 422,
        //         'data' => null,
        //         'message' => 'Gagal Mengupdate Data'
        //     ], 422);
        // }

        $hasil = [
            'code' => 200,
            'data' => $params,
            'message' => 'Berhasil Mengupdate Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }
}
