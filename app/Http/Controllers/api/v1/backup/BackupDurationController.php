<?php

namespace App\Http\Controllers\api\v1\backup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\BackupDuration;

class BackupDurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return BackupDuration::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // check if required action is not a bulk action
        if (gettype(json_decode($request->getContent())) === 'object')
            return $this->createOne($request);
        return $this->createBulk($request);
    }

    private function createOne(Request $BackupDurationData) {
        // check if sent data is correct
        $validation = $this->validateBackupDuration($BackupDurationData->all());
        if ($validation !== true) return $validation;

        $BackupDurationCreated = BackupDuration::create($BackupDurationData->toArray());
        $BackupDuration = BackupDuration::find($BackupDurationCreated->id);

        return response(['duration' => $BackupDuration], config('httpcodes.CREATED'));
    }

    private function createBulk(Request $BackupDurationData) {
        // check if sent data is correct
        foreach ($BackupDurationData->toArray() as $BackupDurationData) {
            $validation = $this->validateBackupDuration($BackupDurationData);
            if ($validation !== true) return $validation;
        }

        $BackupDuration = [];
        foreach ($BackupDurationData->toArray() as $BackupDurationData) {
            array_push($BackupDuration, BackupDuration::create($BackupDurationData));
        }
        return response(['duration' => $BackupDuration], config('httpcodes.CREATED'));
    }

    private function validateBackupDuration($data) {
        $validation = Validator::make($data, [
            'name' => 'required|string',
            'value' => 'required|numeric',
        ]);
        if ($validation->fails())
            return response(['errors' => $validation->messages()], config('httpcodes.UNPROCESSABLE_ENTITY'));
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (BackupDuration::where('id', $id)->count() !== 1)
            return response(['message' => 'No duration match this id.'], config('httpcodes.NOT_FOUND'));
        $BackupDuration = BackupDuration::find($id)->toArray();
        return response(['duration' => $BackupDuration], config('httpcodes.OK'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // check if sent data is correct
        $validation = $this->validateBackupDuration($request->toArray());
        if ($validation !== true) return $validation;

        if (BackupDuration::where('id', $id)->count() !== 1)
            return response(['message' => 'No duration match this id.'], config('httpcodes.NOT_FOUND'));

        $BackupDuration = BackupDuration::find($id);
        $BackupDuration->update($request->toArray());

        return response(['duration' => $BackupDuration->toArray()], config('httpcodes.OK'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (BackupDuration::where('id', $id)->count() !== 1)
            return response(['message' => 'No duration match this id.'], config('httpcodes.NOT_FOUND'));

        $BackupDuration = BackupDuration::find($id);
        $BackupDuration->delete();

        return response(['duration' => $BackupDuration->toArray()], config('httpcodes.OK'));
    }
}
