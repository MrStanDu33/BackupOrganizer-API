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
    public function index($customerId = null)
    {
        if ($customerId === null)
            return Project::all();

        return $this->indexByCustomer($customerId);
    }

    /**
     * Display a listing of the resource based on customerId.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexByCustomer(Customer $customerId)
    {
        return Project::where('customerId', $customerId);
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

        return response(['Project' => $Project], config('httpcodes.CREATED'));
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
        return response(['Projects' => $Projects], config('httpcodes.CREATED'));
    }

    private function validateProject($data) {
        $validation = Validator::make($data, [
            'name' => 'required|string',
            'siret' => 'nullable|string|digits:9',
            'logo' => 'nullable|file|image|mimes:jpeg,png',
            'address_street_number' => 'nullable|string',
            'address_street_name' => 'nullable|string',
            'address_zip_code' => 'nullable|string|digits:5',
            'address_city' => 'nullable|string',
            'address_country' => 'nullable|string',
            'address_billing' => 'nullable|string',
            'tva_number' => 'nullable|string|min:13|max:13',
            'website' => 'nullable|string|url',
            'source' => 'nullable|string',
            'referent_name' => 'nullable|string',
            'referent_email' => 'nullable|string|email',
            'referent_number' => 'nullable|string|min:12|max:12',
        ]);
        if ($validation->fails())
            return response(['errors' => $validation->messages()], config('httpcodes.UNPROCESSABLE_ENTITY'));
        // check if user already exists
        if (isset($data['siret']) && Project::where('siret', '=', $data['siret'])->exists())
            return response(['errors' => ['conflict' => 'A Project already exist with given siret.']], config('httpcodes.CONFLICT'));
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
            return response(['message' => 'No Project match this id'], config('httpcodes.NOT_FOUND'));
        $Project = Project::find($id)->toArray();
        return response(['Project' => $Project], config('httpcodes.OK'));
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
            return response(['message' => 'No Project match this id'], config('httpcodes.NOT_FOUND'));

        $Project = Project::find($id);
        $Project->update($request->toArray());

        return response(['Project' => $Project->toArray()], config('httpcodes.OK'));
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
            return response(['message' => 'No Project match this id'], config('httpcodes.NOT_FOUND'));

        $Project = Project::find($id);
        $Project->delete();

        return response(['Project' => $Project->toArray()], config('httpcodes.OK'));
    }
}
