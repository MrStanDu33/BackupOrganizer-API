<?php

namespace App\Http\Controllers\api\v1\website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Project;
use App\Website;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->projectId === null)
            return Website::all();

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

        return Website::where('projectId', $projectId)->get()->toArray();
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

    private function createOne(Request $WebsiteData) {
        // check if sent data is correct
        $validation = $this->validateWebsite($WebsiteData->all());
        if ($validation !== true) return $validation;

        $WebsiteCreated = Website::create($WebsiteData->toArray());
        $Website = Website::find($WebsiteCreated->id);

        return response(['website' => $Website], config('httpcodes.CREATED'));
    }

    private function createBulk(Request $WebsiteData) {
        // check if sent data is correct
        foreach ($WebsiteData->toArray() as $WebsiteData) {
            $validation = $this->validateWebsite($WebsiteData);
            if ($validation !== true) return $validation;
        }

        $Website = [];
        foreach ($WebsiteData->toArray() as $WebsiteData) {
            array_push($Website, Website::create($WebsiteData));
        }
        return response(['website' => $Website], config('httpcodes.CREATED'));
    }

    private function validateWebsite($data) {
        $validation = Validator::make($data, [
            'projectId' => 'required|numeric|exists:projects,id',
            'url' => 'required|string|url',
            'description' => 'nullable|string',
            'type' => 'required|boolean',
            'status' => 'nullable|boolean',
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
        if (Website::where('id', $id)->count() !== 1)
            return response(['message' => 'No website match this id.'], config('httpcodes.NOT_FOUND'));
        $Website = Website::find($id)->toArray();
        return response(['website' => $Website], config('httpcodes.OK'));
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
        $validation = $this->validateWebsite($request->toArray());
        if ($validation !== true) return $validation;

        if (Website::where('id', $id)->count() !== 1)
            return response(['message' => 'No website match this id.'], config('httpcodes.NOT_FOUND'));

        $Website = Website::find($id);
        $Website->update($request->toArray());

        return response(['website' => $Website->toArray()], config('httpcodes.OK'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (Website::where('id', $id)->count() !== 1)
            return response(['message' => 'No website match this id.'], config('httpcodes.NOT_FOUND'));

        $Website = Website::find($id);
        $Website->delete();

        return response(['website' => $Website->toArray()], config('httpcodes.OK'));
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
