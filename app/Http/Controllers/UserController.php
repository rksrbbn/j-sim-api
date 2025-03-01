<?php

namespace App\Http\Controllers;

use App\Models\LogActivitiesModel;
use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function actionWork(Request $request)
    {
        $user = $request->user();

        if (empty($user->id))
        {
            return response()->json(['code' => 400, 'data' => null, 'message' => 'Id tidak ditemukan'], 400);
        }

        $randomPoints = rand(1,10);

        $update = UserModel::find($user->id)->update(['money' => DB::raw('money +'.$randomPoints), 'days' => DB::raw('days +1')]);

         // insert log
        $params = [
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'activity' => 'Kamu Bekerja di Tempat Kerja, Mendapatkan (' . $randomPoints . ') 48points!',
            'time' => Carbon::now('Asia/Jakarta')
        ];

        $create =LogActivitiesModel::create($params);

        if (!$create)
        {
            return response()->json( [
                'code' => 422,
                'data' => null,
                'message' => 'Gagal Menyimpan Data Log'
            ], 422);
        }

        $hasil = [
            'code' => 200,
            'data' => null,
            'message' => 'Berhasil Mendapatkan (' . $randomPoints . ') 48points!'
        ];
        return response()->json($hasil, $hasil['code']);
    }
}
