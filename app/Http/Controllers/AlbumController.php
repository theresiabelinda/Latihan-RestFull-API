<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlbumRegisterRequest;
use App\Http\Resources\AlbumResource;
use App\Models\Album;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    protected function register(AlbumRegisterRequest $request)
    {
        $data = $request->validated();

        $file = base64_decode($data['image']);
        $safeName = "album-".Str::uuid().".jpg";
        file_put_contents(public_path().'/gambar/'.$safeName,$file);

        $album = new Album($data);
        $album->image = $safeName;
        $album->save();

        return (new AlbumResource($album))->response()->setStatusCode(201);
    }
}
