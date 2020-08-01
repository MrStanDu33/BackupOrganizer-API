<?php

namespace App\Http\Controllers\api\v1\project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Customer;
use App\Project;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->customerId === null)
            return Project::all();

        return $this->indexByCustomer(intval($request->customerId));
    }

    /**
     * Display a listing of the resource based on customerId.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexByCustomer($customerId)
    {
        $customerValidation = $this->customerExist($customerId);
        if ($customerValidation !== true) return $customerValidation;

        return Project::where('customerId', $customerId)->get()->toArray();
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

    private function createOne(Request $ProjectData) {
        // check if sent data is correct
        $validation = $this->validateProject($ProjectData->all());
        if ($validation !== true) return $validation;

        $ProjectCreated = Project::create($ProjectData->toArray());
        $Project = Project::find($ProjectCreated->id);

        return response(['project' => $Project], config('httpcodes.CREATED'));
    }

    private function createBulk(Request $ProjectsData) {
        // check if sent data is correct
        foreach ($ProjectsData->toArray() as $ProjectData) {
            $validation = $this->validateProject($ProjectData);
            if ($validation !== true) return $validation;
        }

        $Projects = [];
        foreach ($ProjectsData->toArray() as $ProjectData) {
            array_push($Projects, Project::create($ProjectData));
        }
        return response(['projects' => $Projects], config('httpcodes.CREATED'));
    }

    private function validateProject($data) {
        $validation = Validator::make($data, [
            'customerId' => 'required|numeric|exists:customers,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
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
        if (Project::where('id', $id)->count() !== 1)
            return response(['message' => 'No project match this id'], config('httpcodes.NOT_FOUND'));
        $Project = Project::find($id)->toArray();
        return response(['project' => $Project], config('httpcodes.OK'));
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
        $validation = $this->validateProject($request->toArray());
        if ($validation !== true) return $validation;

        if (Project::where('id', $id)->count() !== 1)
            return response(['message' => 'No project match this id'], config('httpcodes.NOT_FOUND'));

        $Project = Project::find($id);
        $Project->update($request->toArray());

        return response(['project' => $Project->toArray()], config('httpcodes.OK'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (Project::where('id', $id)->count() !== 1)
            return response(['message' => 'No project match this id'], config('httpcodes.NOT_FOUND'));

        $Project = Project::find($id);
        $Project->delete();

        return response(['project' => $Project->toArray()], config('httpcodes.OK'));
    }


    /**
     * Verifies if customer exist with given customerId.
     *
     * @param  int  $customerId
     * @return \Illuminate\Http\Response
     */
    private function customerExist($customerId) {
        if (Customer::where('id', $customerId)->count() !== 1)
            return response(['message' => 'No customer match this id'], config('httpcodes.NOT_FOUND'));
        return true;
    }
}
