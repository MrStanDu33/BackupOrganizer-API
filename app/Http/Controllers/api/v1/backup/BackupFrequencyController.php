<?php

namespace App\Http\Controllers\api\v1\backup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\BackupFrequency;

class BackupFrequencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return BackupFrequency::all();
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

    private function createOne(Request $BackupFrequencyData) {
        // check if sent data is correct
        $validation = $this->validateBackupFrequency($BackupFrequencyData->all());
        if ($validation !== true) return $validation;

        $BackupFrequencyCreated = BackupFrequency::create($BackupFrequencyData->toArray());
        $BackupFrequency = BackupFrequency::find($BackupFrequencyCreated->id);

        return response(['frequency' => $BackupFrequency], config('httpcodes.CREATED'));
    }

    private function createBulk(Request $BackupFrequencyData) {
        // check if sent data is correct
        foreach ($BackupFrequencyData->toArray() as $BackupFrequencyData) {
            $validation = $this->validateBackupFrequency($BackupFrequencyData);
            if ($validation !== true) return $validation;
        }

        $BackupFrequency = [];
        foreach ($BackupFrequencyData->toArray() as $BackupFrequencyData) {
            array_push($BackupFrequency, BackupFrequency::create($BackupFrequencyData));
        }
        return response(['frequency' => $BackupFrequency], config('httpcodes.CREATED'));
    }

    private function validateBackupFrequency($data) {
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
        if (BackupFrequency::where('id', $id)->count() !== 1)
            return response(['message' => 'No frequency match this id.'], config('httpcodes.NOT_FOUND'));
        $BackupFrequency = BackupFrequency::find($id)->toArray();
        return response(['frequency' => $BackupFrequency], config('httpcodes.OK'));
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
        $validation = $this->validateBackupFrequency($request->toArray());
        if ($validation !== true) return $validation;

        if (BackupFrequency::where('id', $id)->count() !== 1)
            return response(['message' => 'No frequency match this id.'], config('httpcodes.NOT_FOUND'));

        $BackupFrequency = BackupFrequency::find($id);
        $BackupFrequency->update($request->toArray());

        return response(['frequency' => $BackupFrequency->toArray()], config('httpcodes.OK'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (BackupFrequency::where('id', $id)->count() !== 1)
            return response(['message' => 'No frequency match this id.'], config('httpcodes.NOT_FOUND'));

        $BackupFrequency = BackupFrequency::find($id);
        $BackupFrequency->delete();

        return response(['frequency' => $BackupFrequency->toArray()], config('httpcodes.OK'));
    }
}
