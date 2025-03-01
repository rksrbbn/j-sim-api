<?php

namespace App\Http\Controllers;

use App\Models\LogActivitiesModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LogActivitiesController extends Controller
{
    public function getUserLogs(Request $request)
    {
        $user = $request->user();

        $logs = LogActivitiesModel::where('user_id', $user->id)->orderBy('time', 'asc')->get();

        $hasil = [
            'code' => 200,
            'data' => $logs,
            'message' => 'Berhasil Mendapatkan Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }

    public function saveLog(Request $request)
    {

        $params = [
            'id' => Str::uuid(),
            'user_id' => $request->user_id,
            'activity' => $request->activity,
            'time' => Carbon::now('Asia/Jakarta')
        ];

        $create =LogActivitiesModel::create($params);

       if (!$create)
        {
            return response()->json( [
                'code' => 422,
                'data' => null,
                'message' => 'Gagal Menyimpan Data'
            ], 422);
        }

        $hasil = [
            'code' => 200,
            'data' => $params,
            'message' => 'Berhasil Menyimpan Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }
}
