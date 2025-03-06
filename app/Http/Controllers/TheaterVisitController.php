<?php

namespace App\Http\Controllers;

use App\Models\LogActivitiesModel;
use App\Models\TheaterVisitModel;
use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TheaterVisitController extends Controller
{
    public function checkTheaterApply(Request $request)
    {
        $user = $request->user();
        $data = TheaterVisitModel::where('user_id', $user->id)->where('day', $user->days);

        $exist = $data->count() > 0;

        $hasil = [
            'code' => 200,
            'data' => $exist,
            'message' => 'Berhasil Mendapatkan Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }

    public function saveTheaterApply(Request $request)
    {
        
        $user = $request->user();
        $params = [
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'day' => $user->days,
            'show_name' => $request->show_name,
            'roulette' => 0,
            'bd_member' => null,
            'status' => $request->status,

        ];

        if ($params['status'] == 'VERIFIED')
        {
            // kurangi 200 48points dan tambah 1 hari
            $update = UserModel::find($user->id)->update(['money' => DB::raw('money - 200'), 'days' => DB::raw('days + 1')]);
            if (!$update)
            {
                return response()->json( [
                    'code' => 400,
                    'data' => null,
                    'message' => 'Gagal Menyimpan Data'
                ], 400);
            }

            // insert log
            $paramsLog = [
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'activity' => $request->activity,
                'time' => Carbon::now('Asia/Jakarta'),
                'sim_date' => $request->sim_date
            ];

            $insertLog = LogActivitiesModel::create($paramsLog);
            if (!$insertLog)
            {
                return response()->json( [
                    'code' => 400,
                    'data' => null,
                    'message' => 'Gagal Menyimpan Data Log'
                ], 400);
            }
        }

        $save = TheaterVisitModel::create($params);
        if (!$save)
        {
            return response()->json( [
                'code' => 400,
                'data' => null,
                'message' => 'Gagal Menyimpan Data'
            ], 400);
        }

        $hasil = [
            'code' => 200,
            'data' => $params,
            'message' => 'Berhasil menyimpan Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }

    public function historyTheaterApply(Request $request)
    {
        $userId = $request->user_id;
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;
        
        $data = TheaterVisitModel::where('user_id', $userId)
        ->where('status', '<>', 'NOT_APPLIED');
        $count = $data->count();

        $data = $data
            ->offset($offset)
            ->limit($limit)
            ->orderBy('day', 'desc')
            ->get();

        $hasil = [
            'code' => 200,
            'data' => $data,
            'total' => $count,
            'message' => 'Berhasil Mendapatkan Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }

}
