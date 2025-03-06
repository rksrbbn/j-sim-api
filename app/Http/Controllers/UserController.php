<?php

namespace App\Http\Controllers;

use App\Models\BondingsModel;
use App\Models\LogActivitiesModel;
use App\Models\TwoShotModel;
use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        $user = $request->user();
        $search = $request->search;
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;

        $users = UserModel::where('id', '!=', $user->id);

        if (!empty($search)) {
            $users->whereRaw("LOWER(username) LIKE ?", ['%' . strtolower($search) . '%']);
        } 
        
        $count = $users->count();
        $users = $users->offset($offset)->limit($limit)->get();

        $hasil = [
            'code' => 200,
            'data' => $users,
            'total' => $count,
            'message' => 'Berhasil Mendapatkan Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }

    public function getUserProfile(Request $request)
    {
        $user = $request->user();
        $user = UserModel::where('id', $user->id)->first()->append(['theater_visit', 'total_two_shot']);

        $hasil = [
            'code' => 200,
            'data' => $user,
            'message' => 'Berhasil Mendapatkan Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }

    public function getUserPicture(Request $request)
    {
        $user = $request->user();

        if (!empty($user->picture) && $user->picture != null) {
            $path = storage_path('app/public/pictures/' . $user->picture);
            if (!file_exists($path)) {
                return response()->json(['code' => 404, 'data' => null, 'message' => 'File gambar tidak ditemukan'], 404);
            }
            $file = file_get_contents($path);
            $type = mime_content_type($path);
    
            return response($file, 200)
                ->header('Content-Type', $type);
        }
        return response()->json(['code' => 200, 'data' => null, 'message' => 'Belum upload gambar'], 200);
    }

    public function getUserPictureByName(Request $request)
    {
        $userPicture = $request->name;

        if (!empty($userPicture) && $userPicture != null && $userPicture != 'null') {
            $path = storage_path('app/public/pictures/' . $userPicture);
            if (!file_exists($path)) {
                return response()->json(['code' => 404, 'data' => null, 'message' => 'File gambar tidak ditemukan'], 404);
            }
            $file = file_get_contents($path);
            $type = mime_content_type($path);
    
            return response($file, 200)
                ->header('Content-Type', $type);
        }

        return response()->json(['code' => 200, 'data' => null, 'message' => 'Belum upload gambar'], 200);
    }

    public function getUserDetail(Request $request)
    {
        
        if (empty($request->id))
        {
            return response()->json(['code' => 400, 'data' => null, 'message' => 'Id tidak ditemukan'], 400);
        }
        $user = UserModel::where('id', $request->id)->first()->append(['theater_visit', 'total_two_shot']);

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
            'bio' => $request->bio,
            'oshimen' => $request->oshimen,
            'fav_songs' => $request->songs,
        ];

        if (strlen($params['bio']) > 150) {
            return response()->json( [
                'code' => 400,
                'data' => null,
                'message' => 'Gagal Mengupdate Data, Panjang Karakter Bio Melebihi Maksimal (150 karakter).'
            ], 400);
        }

        if ($request->hasFile('picture')) 
        {
            $picture = $request->file('picture');

            if (!$picture->isValid()) 
            {
                return response()->json( [
                    'code' => 400,
                    'data' => null,
                    'message' => 'Gagal Mengupdate Data, File Gambar Tidak Valid'
                ], 400);
            }

            $extension = $picture->getClientOriginalExtension();
            $picture_name = $picture->getClientOriginalName();

            if (!in_array(strtolower((string)$extension), ['jpg', 'jpeg', 'png'])) 
            {
                return response()->json( [
                    'code' => 400,
                    'data' => null,
                    'message' => 'Gagal Mengupdate Data, File Gambar Harus Berformat JPG, JPEG, PNG'
                ], 400);
            }

            if ($picture->getSize() > 100000) 
            {
                return response()->json( [
                    'code' => 400,
                    'data' => null,
                    'message' => 'Gagal Mengupdate Data, File Gambar Maksimal 100KB'
                ], 400);
            }

            if (!empty($user->picture) && $user->picture != null) {
                $oldPicturePath = storage_path('app/public/pictures/' . $user->picture);
                if (file_exists($oldPicturePath)) {
                    unlink($oldPicturePath);
                }
            }

            $picture->storeAs('public/pictures', $picture_name);

            $params['picture'] = $picture_name;
        }

        $update = UserModel::where('id', $user->id)->update($params);

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

        $performance = '';

        if ($randomPoints <= 3)
        {
            $performance = ' namun performamu kurang baik.';
        }
        else if ($randomPoints <= 6)
        {
            $performance = ' kamu bekerja dengan cukup baik.';
        }
        else
        {
            $performance = ' hari ini kamu bekerja dengan sangat baik!';
        }

         // insert log
        $params = [
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'activity' => 'Kamu Bekerja di Tempat Kerja,' . $performance .' Kamu mendapatkan (' . $randomPoints . ') 48points!',
            'time' => Carbon::now('Asia/Jakarta'),
            'sim_day' => $request->day
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

    public function deductPoints(Request $request)
    {
        $user = $request->user();

        $update = UserModel::where('id', $user->id)->update(['money' => DB::raw('money -'.$request->amount)]);

        if (!$update)
        {
            return response()->json( [
                'code' => 422,
                'data' => null,
                'message' => 'Gagal Mengupdate Data'
            ], 422);
        }

        $hasil = [
            'code' => 200,
            'data' => $update,
            'message' => 'Berhasil Mendapatkan Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }

    public function save2ShotResult(Request $request)
    {
        $user = $request->user();

        $params = [
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'member' => $request->member_name,
            'type' => $request->type,
            'day' => $request->day,
            'show_name' => $request->show_name,
            'roulette_number' => $request->roulette_number
        ];

        $insert = TwoShotModel::create($params);

        if (!$insert)
        {
            return response()->json( [
                'code' => 422,
                'data' => null,
                'message' => 'Gagal Menyimpan Data'
            ], 422);
        }

        // insert log
        $paramsLog = [
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'activity' => 'Kamu melakukan 2 shot pada show ' . $request->show_name . ' dengan nomor roulette ' . $request->roulette_number . '. dan kamu 2 shot bersama ' . $request->member_name . '!',
            'time' => Carbon::now('Asia/Jakarta'),
            'sim_day' => $request->day
        ];

        $bondings = 10;

        // UPDATE or INSERT BONDINGS
        // $paramsBondings = [
        //     'id' => Str::uuid(),
        //     'user_id' => $user->id,
        //     'member' => $request->member_name,
        //     'total' => $bondings,
        // ];

        $dataBondings = BondingsModel::firstOrCreate(
            [
                'user_id' => $user->id,
                'member' => $request->member_name
            ],
            [
                'id' => Str::uuid(),
                'total' => 0
            ]
        );

        $update = $dataBondings->increment('total', $bondings);

        if (!$update)
        {
            return response()->json( [
                'code' => 422,
                'data' => null,
                'message' => 'Gagal Menyimpan Data Bondings'
            ], 422);
        }

        $create =LogActivitiesModel::create($paramsLog);

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
            'data' => $params,
            'message' => 'Berhasil Menyimpan Data'
        ];
        return response()->json($hasil, $hasil['code']);
    }

    public function historyTwoShot(Request $request)
    {
        $userId = $request->user_id;
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;
        
        $data = TwoShotModel::where('user_id', $userId);

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
