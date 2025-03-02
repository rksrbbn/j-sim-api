<?php

namespace App\Http\Controllers;

use App\Models\LogActivitiesModel;
use App\Models\UserItemsModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserItemsController extends Controller
{
    public function getUserItems(Request $request)
    {
        $userId = $request->user_id;

        if (empty($userId)) {
            $hasil = [
                'code' => 400,
                'data' => null,
                'message' => 'User ID Tidak Ditemukan.'
            ];
            return response()->json($hasil, $hasil['code']);
        }

        $items = UserItemsModel::where('user_id', $userId)->with('dataItems')->get();

        $hasil = [
            'code' => 200,
            'data' => $items,
            'message' => 'Berhasil Mendapatkan Data User Items.'
        ];
        return response()->json($hasil, $hasil['code']);
    }
}
