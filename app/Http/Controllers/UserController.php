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

    public function getUserPicture(Request $request)
    {
        $user = $request->user();

        $path = storage_path('app/public/pictures/' . $user->picture);
        if (!file_exists($path)) {
            return response()->json(['code' => 404, 'data' => null, 'message' => 'File gambar tidak ditemukan'], 404);
        }

        $file = file_get_contents($path);
        $type = mime_content_type($path);

        return response($file, 200)
            ->header('Content-Type', $type);
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
            'bio' => $request->bio,
            'oshimen' => $request->oshimen,
            'fav_songs' => $request->songs,
        ];

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

            $oldPicturePath = storage_path('app/public/pictures/' . $user->picture);
            if (file_exists($oldPicturePath)) {
                unlink($oldPicturePath);
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
