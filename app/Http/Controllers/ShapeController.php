<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreShapeCoordinates as StoreRequest;

use App\UserCoordinates;

class ShapeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response([
            'shapes' => auth()->user()->shapes ?? []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $requestParams = $request->only('body');
        $coordinates = $requestParams['body']['coordinates'];

        $user = auth()->user();
        $userCoordinates = new UserCoordinates;
        $userCoordinates->coordinates = $coordinates;
        $saved = $user->shapes()->save($userCoordinates);

        return response(['shape' => $userCoordinates]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = UserCoordinates::destroy($id);
        return response(['delete' => (int)$delete]);
    }
}
