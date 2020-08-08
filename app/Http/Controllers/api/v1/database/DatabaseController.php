<?php

namespace App\Http\Controllers\api\v1\database;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Project;
use App\Database;

class DatabaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->projectId === null)
            return Database::all();

        return $this->indexByProject(intval($request->projectId));
    }

    /**
     * Display a listing of the resource based on customerId.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexByProject($projectId)
    {
        $projectValidation = $this->projectExist($projectId);
        if ($projectValidation !== true) return $projectValidation;

        return Database::where('projectId', $projectId)->get()->toArray();
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

    private function createOne(Request $DatabaseData) {
        // check if sent data is correct
        $validation = $this->validateDatabase($DatabaseData->all());
        if ($validation !== true) return $validation;

        $DatabaseCreated = Database::create($DatabaseData->toArray());
        $Database = Database::find($DatabaseCreated->id);

        return response(['database' => $Database], config('httpcodes.CREATED'));
    }

    private function createBulk(Request $DatabaseData) {
        // check if sent data is correct
        foreach ($DatabaseData->toArray() as $DatabaseData) {
            $validation = $this->validateDatabase($DatabaseData);
            if ($validation !== true) return $validation;
        }

        $Database = [];
        foreach ($DatabaseData->toArray() as $DatabaseData) {
            array_push($Database, Database::create($DatabaseData));
        }
        return response(['database' => $Database], config('httpcodes.CREATED'));
    }

    private function validateDatabase($data) {
        $validation = Validator::make($data, [
            'projectId' => 'required|numeric|exists:projects,id',
            'name' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|boolean',
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
        if (Database::where('id', $id)->count() !== 1)
            return response(['message' => 'No database match this id.'], config('httpcodes.NOT_FOUND'));
        $Database = Database::find($id)->toArray();
        return response(['database' => $Database], config('httpcodes.OK'));
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
        $validation = $this->validateDatabase($request->toArray());
        if ($validation !== true) return $validation;

        if (Database::where('id', $id)->count() !== 1)
            return response(['message' => 'No database match this id.'], config('httpcodes.NOT_FOUND'));

        $Database = Database::find($id);
        $Database->update($request->toArray());

        return response(['database' => $Database->toArray()], config('httpcodes.OK'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (Database::where('id', $id)->count() !== 1)
            return response(['message' => 'No database match this id.'], config('httpcodes.NOT_FOUND'));

        $Database = Database::find($id);
        $Database->delete();

        return response(['database' => $Database->toArray()], config('httpcodes.OK'));
    }


    /**
     * Verifies if project exist with given customerId.
     *
     * @param  int  $projectId
     * @return \Illuminate\Http\Response
     */
    private function projectExist($projectId) {
        if (Project::where('id', $projectId)->count() !== 1)
            return response(['message' => 'No project match this id.'], config('httpcodes.NOT_FOUND'));
        return true;
    }
}
