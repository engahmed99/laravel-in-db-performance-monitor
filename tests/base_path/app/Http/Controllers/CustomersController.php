<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Customer;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $customers = Customer::with(['orders', 'products'])->where('name', 'LIKE', "%$keyword%")
                ->orWhere('email', 'LIKE', "%$keyword%")
                ->orWhere('mobile', 'LIKE', "%$keyword%")
                ->orWhere('address', 'LIKE', "%$keyword%")
                ->orWhere('birth_date', 'LIKE', "%$keyword%")
                ->orWhere('kids_no', 'LIKE', "%$keyword%")
                ->paginate($perPage);
        } else {
            $customers = Customer::with(['orders', 'products'])->paginate($perPage);
        }

        return view('customers.index', compact('customers'));
    }

    /**
     * Generates random errors.
     *
     * @return \Illuminate\View\View
     */
    public function generateErrors(Request $request, $error_code)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if($error_code == 1)
            $this->getMyDataQuickly();
        if($error_code == 2)
            Clients::all();
        if($error_code == 3)
            $keyword .= $append;
        if($error_code == 4)
            Customer::create([
                'name' => 'SSS EEE FFF',
                'email' => 'vvvvvvv@ddd.com',
                'address' => 'none',
                'kids_no' => 'Fifteen'
            ]);
        if($error_code == 5)
            abort(403);
        if($error_code == 6)
            Customer::findOrfail(-1);
        if($error_code == 7) {
            \DB::select(\DB::raw('select * from customers where id = 3'));
            throw new \Exception("Raw SQL - Not elequent query");
        }
        if (!empty($keyword)) {
            $customers = Customer::where('name', 'LIKE', "%$keyword%")
                ->orWhere('email', 'LIKE', "%$keyword%")
                ->orWhere('mobile', 'LIKE', "%$keyword%")
                ->orWhere('address', 'LIKE', "%$keyword%")
                ->orWhere('birth_date', 'LIKE', "%$keyword%")
                ->orWhere('kids_no', 'LIKE', "%$keyword%")
                ->paginate($perPage);
        } else {
            $customers = Customer::paginate($perPage);
        }

        foreach ($customers as $c) 
            $x = count($c->products) + count($c->orders);
        return view('customers.generate-errors', compact('customers', 'error_code'));
    }

    /**
     * Generates JSON Response
     *
     * @return JSON
     */
    public function generateJson(Request $request) {
        return response()->json([
            'status' => 'success'
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        
        $requestData = $request->all();
        
        Customer::create($requestData);

        return redirect('customers')->with('flash_message', 'Customer added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $customer = Customer::with(['orders', 'orders.product'])->findOrFail($id);

        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        
        $requestData = $request->all();
        
        $customer = Customer::findOrFail($id);
        $customer->update($requestData);

        return redirect('customers')->with('flash_message', 'Customer updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Customer::destroy($id);

        return redirect('customers')->with('flash_message', 'Customer deleted!');
    }
}
