<?php

namespace App\Http\Controllers\api\v1\customer;

use App\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Customer::all();
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

    private function createOne(Request $customerData) {
        // check if sent data is correct
        $validation = $this->validateCustomer($customerData->all());
        if ($validation !== true) return $validation;

        $customer = Customer::create($customerData->toArray());

        return response(['customer' => $customer], config('httpcodes.CREATED'));
    }

    private function createBulk(Request $customersData) {
        // check if sent data is correct
        foreach ($customersData->toArray() as $customerData) {
            $validation = $this->validateCustomer($customerData);
            if ($validation !== true) return $validation;
        }

        $customers = [];
        foreach ($customersData->toArray() as $customerData) {
            array_push($customers, Customer::create($customerData));
        }
        return response(['customers' => $customers], config('httpcodes.CREATED'));
    }

    private function validateCustomer($data) {
        $validation = Validator::make($data, [
            'name' => 'required|string',
            'siren' => 'optional|string|digits:9',
            'logo' => 'optional|file|image|mimes:jpeg,png',
            'address_street_number' => 'optional|numeric|min:0',
            'address_street_name' => 'optional|string',
            'address_zip_code' => 'optional|numeric|min:5',
            'address_city' => 'optional|string',
            'address_country' => 'optional|string',
            'address_billing' => 'optional|string',
            'tva_number' => 'optional|string|min:13|max:13',
            'website' => 'optional|string|url',
            'source' => 'optional|string',
            'referent_name' => 'optional|string',
            'referent_email' => 'optional|string|email',
            'referent_number' => 'optional|string|min:12|max:12',
        ]);
        if ($validation->fails())
            return response(['errors' => $validation->messages()], config('httpcodes.UNPROCESSABLE_ENTITY'));
        // check if user already exists
        if (isset($data['siren']) && Customer::where('siren', '=', $data['siren'])->exists())
            return response(['errors' => ['conflict' => 'A customer already exist with given siren.']], config('httpcodes.CONFLICT'));
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
        if (Customer::where('id', $id)->count() !== 1)
            return response(['message' => 'No customer match this id'], config('httpcodes.NOT_FOUND'));
        $customer = Customer::find($id)->toArray();
        return response(['customer' => $customer], config('httpcodes.OK'));
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
        $validation = $this->validateCustomer($request->toArray());
        if ($validation !== true) return $validation;

        if (Customer::where('id', $id)->count() !== 1)
            return response(['message' => 'No customer match this id'], config('httpcodes.NOT_FOUND'));

        $customer = Customer::find($id);
        $customer->update($request->toArray());

        return response(['customer' => $customer->toArray()], config('httpcodes.OK'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (Customer::where('id', $id)->count() !== 1)
            return response(['message' => 'No customer match this id'], config('httpcodes.NOT_FOUND'));

        $customer = Customer::find($id);
        $customer->delete();

        return response(['customer' => $customer->toArray()], config('httpcodes.OK'));
    }
}
