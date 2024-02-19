<?php

namespace App\Http\Controllers\Dashboard;

use App\SellOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

use App\Client;
use App\City;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if(!permission_group_checker(Auth::guard('admin')->user()->id, 'Clients')) {abort(404);}
        $clients = Client::where('hide', '=', 0)->paginate(30);
        if ($request->client_id){
            $clients = Client::where('hide', '=', 0)->where('id',$request->client_id)->paginate(30);

        }
        $queryParameters = $request->query();

        return view('admin.pages.clients.index')->with(['clients'=>$clients,'request'=>$request,'queryParameters'=>$queryParameters]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_client')) {abort(404);}
        $cities = City::where('hide', '=', 0)->orderBy('title')->get();
        return view('admin.pages.clients.create')->with(['cities'=>$cities]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_client')) {abort(404);}
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => ['nullable', 'email', Rule::unique('clients')->where(function($query) {$query->where('hide', '=', 0);})],
            'phone' => ['required', 'min:11', Rule::unique('clients')->where(function($query) {$query->where('hide', '=', 0);})],
            'phone_2' => 'nullable|min:11',
            'address' => 'required',
            'city' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg,gif,webp'
        ],
            [
                'name.required'=>'Please Enter Name',
                'email.unique'=>'This Email Address Is Registered To Another Client',
                'email.email'=>'Please Enter Correct Email Address',
                'phone.required'=>'Please Enter Phone Number',
                'phone.min'=>'Phone Number Min. Length Is 11',
                'phone.unique'=>'This Phone Number Is Registered To Another Client',
                'phone_2.min'=>'Phone Number Min. Length Is 11',
                'address.required'=>'Please Enter Address',
                'city.required'=>'Please Choose City',
                'image.mimes' => 'Please Choose Image File'
            ]);
        $admin = new Client;
        $admin->name  = $request->name;
        $admin->phone = $request->phone;
        $admin->phone_2 = $request->phone_2;
        $admin->email = $request->email;
        $admin->address = $request->address;
        $admin->city = $request->city;
        if($request->hasFile('image'))
        {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/clients');
            $image->move($destinationPath, $imageName);
            $admin->image ='/uploads/clients/'.$imageName;
        }
        $admin->save();
        return redirect()->route('clients.index');
    }

    public function edit($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_client')) {abort(404);}
        $cities = City::where('hide', '=', 0)->orderBy('title')->get();
        $admin = Client::findorfail($id);
        if($admin->hide == 0)
        {
            return view('admin.pages.clients.edit')->with(['admin'=>$admin, 'cities'=>$cities]);
        }
        else
        {
            abort(404);
        }
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
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_client')) {abort(404);}
        $admin = Client::findorfail($id);
        if($admin->hide == 0)
        {
            $validatedData = $request->validate([
                'name' => 'required',
                'phone' => 'required|min:11',
                'address' => 'required',
                'city' => 'required'
            ],
                [
                    'name.required'=>'Please Enter Name',
                    'phone.required'=>'Please Enter Phone Number',
                    'phone.min'=>'Phone Number Min. Length Is 11',
                    'address.required'=>'Please Enter Address',
                    'city.required'=>'Please Choose City'
                ]);


            $admin = Client::findorfail($id);

            if($admin->email != $request->email)
            {
                $validatedData = $request->validate([
                    'email' => ['nullable', 'email', Rule::unique('clients')->where(function($query) {$query->where('hide', '=', 0);})],
                ],
                    [
                        'email.unique'=>'This Email Address Is Registered To Another User',
                        'email.email'=>'Please Enter Correct Email Address'
                    ]);
            }
            if($admin->phone != $request->phone)
            {
                $validatedData = $request->validate([
                    'name' => 'required',
                    'phone' => ['required', 'min:11', Rule::unique('clients')->where(function($query) {$query->where('hide', '=', 0);})],
                ],
                    [
                        'name.required'=>'Please Enter Name',
                        'phone.required'=>'Please Enter Phone Number',
                        'phone.min'=>'Phone Number Min. Length Is 11',
                        'phone.unique'=>'This Phone Number Is Registered To Another Client',
                    ]);
            }
            $admin->name  = $request->name;
            $admin->phone = $request->phone;
            $admin->phone_2 = $request->phone_2;
            $admin->email = $request->email;
            $admin->address = $request->address;
            $admin->city = $request->city;
            if($request->hasFile('image'))
            {
                $validatedData = $request->validate([
                    'image' => 'mimes:jpeg,png,jpg,gif,svg,gif,webp'
                ],
                    $messages = [
                        'image.mimes' => 'Please Choose Image File'
                    ]);

                if (File::exists(public_path().$admin->image))
                {
                    File::delete(public_path().$admin->image);
                }

                $image = $request->file('image');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/clients');
                $image->move($destinationPath, $imageName);
                $admin->image = '/uploads/clients/'.$imageName;
            }

            $admin->save();
            return redirect()->route('clients.index');
        }
        else
        {
            abort(404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'delete_client')) {abort(404);}
        $admin = Client::findorfail($id);
        $admin->hide = 1;
        $admin->save();
        return redirect()->route('clients.index');
    }

    public function order_client_search(Request $request)
    {
        $search = $request->search;
        if($search != '')
        {
            $find_client = Client::where('hide', '=', 0)->where(function($query) use ($search)
            {
                $query->where('phone', '=', $search)->
                orWhere('phone_2', '=', $search)->
                orWhere('name', '=', $search)->
                orWhere('email', '=', $search);
            })->first();
            $cities = City::where('hide', '=', 0)->orderBy('title')->get();

            $client_info = view('ajax.client_info', compact('find_client', 'search'))->render();
            $delivery_info = view('ajax.delivery_info', compact('find_client', 'search', 'cities'))->render();
            return response()->json(['success' => true, 'client_info'=>$client_info, 'delivery_info'=>$delivery_info]);
        }
        else
        {
            return response()->json(['success' => true, 'client_info'=>NULL, 'delivery_info'=>NULL]);
        }
    }
    public function find_client(Request $request)
    {
        $search = $request->search;
        if($search != '')
        {
            echo "<div id='cient_finder_data'>";
            $find_client = Client::where('hide', '=', 0)->where(function($query) use ($search)
            {
                $query->where('phone', '=', $search)->
                orWhere('phone_2', '=', $search)->
                orWhere('name', '=', $search)->
                orWhere('email', '=', $search);
            })->first();
            if($find_client !== NULL)
            {

                $won=SellOrder::whereIn('status',[5,6])->where('client',$find_client->id)->where('hide',0)->count();
                $lost=SellOrder::whereIn('status',[7,8])->where('client',$find_client->id)->where('hide',0)->count();
                $open=SellOrder::whereNotIn('status',[5,6,7,8])->where('client',$find_client->id)->where('hide',0)->count();

                echo '<input type="hidden" name="client" value="'.$find_client->id.'" />';
                echo "<span class='h3 mr-4'>Client Details</span>";
                ?>
                <span style="color: blue;" class="h3 mr-2">won:<span class="ml-2"><?php echo $won ; ?></span></span>
                -
                <span style="color: red;" class="h3 mr-2">lost:<span class="ml-2"><?php echo $lost ; ?></span></span>
                -
                <span style="color: #ffaa1d;" class="h3 mr-2">open:<span class="ml-2"><?php echo $open ; ?></span></span>

                <?php
                echo "<div class='row'>";
                ?>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control" type="text"  placeholder="Name" value="<?php echo $find_client->name; ?>" id="name" name="name" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Phone No.</label>
                        <input class="form-control" type="text"  placeholder="Phone No." value="<?php echo $find_client->phone; ?>" id="phone" name="phone" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Phone 2 No.</label>
                        <input class="form-control" type="text"  placeholder="Phone 2 No." value="<?php echo $find_client->phone_2; ?>" id="phone_2" name="phone_2" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Email</label>
                        <input class="form-control" type="text"  placeholder="Email" value="<?php echo $find_client->email; ?>" id="email" name="email" />
                    </div>
                </div>
                <?php
                echo "</div>";
                echo "<div class='row'>";
                $cities = City::where('hide', '=', 0)->orderBy('title')->get();
                $client_city = City::find($find_client->city);
                ?>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Address</label>
                        <input class="form-control" type="text"  placeholder="Address" value="<?php echo $find_client->address; ?>" id="address" name="address" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>City</label>
                        <select class="form-control" name="city" id="client_city_selector" shipping-url="<?php echo url('shipping_price_info'); ?>">
                            <option value="" disabled selected>Choose City</option>
                            <?php
                            foreach ($cities as $city)
                            {
                                ?>
                                <option value="<?php echo $city->id; ?>" <?php if($find_client->city == $city->id){ ?> selected <?php } ?>><?php echo $city->title; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Shipping Price (EGP)</label>
                        <input class="form-control" type="text"  placeholder="Shipping Price" id="order_ship_price" name="ship_price"
                               value="<?php echo $client_city->shipment??''; ?>" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Location</label>
                        <input class="form-control" type="text"  placeholder="Order Location" id="order_location" name="location"  value="<?php echo $find_client->location; ?>" />
                    </div>
                </div>
                <?php
                echo "</div>";
            }
            else
            {
                $cities = City::where('hide', '=', 0)->orderBy('title')->get();
                echo "<h3 class='text-center'>No Results Found - Add New Client</h3>";
                echo "<hr />";
                echo '<input type="hidden" name="client" value="0" />';
                ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Name</label>
                            <input class="form-control" type="text" placeholder="Name" name="name" id="name" />
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Address</label>
                            <input class="form-control" type="text"  placeholder="Address" value="" id="address" name="address" />
                        </div>
                        <div class="col-md-4">
                            <label>City</label>
                            <select class="form-control" name="city" id="client_city_selector" shipping-url="<?php echo url('shipping_price_info'); ?>">
                                <option value="" disabled selected>Choose City</option>
                                <?php
                                foreach ($cities as $city)
                                {
                                    ?>
                                    <option value="<?php echo $city->id; ?>"><?php echo $city->title; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Shipping Price (EGP)</label>
                            <input class="form-control" type="text"  placeholder="Shipping Price" value="" id="order_ship_price" name="ship_price" />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label>E-mail</label>
                            <input class="form-control" type="email"  placeholder="E-mail" id="email" name="email" />
                        </div>
                        <div class="col-md-6">
                            <label>Phone</label>
                            <input class="form-control" type="text"  placeholder="Phone" name="phone" id="phone" value="<?php echo $search; ?>" />
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Phone 2 No.</label>
                        <input class="form-control" type="text"  placeholder="Phone 2 No." id="phone_2" name="phone_2" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Location</label>
                        <input class="form-control" type="text"  placeholder="Order Location" id="order_location" name="location" value="" />
                    </div>
                </div>
                <?php
            }
            echo '</div>';
        }
        else
        {
            echo '';
        }
    }


    public function client_location(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'url|nullable'
        ],
            [
                'location.url'=>'Please Enter Valid URL'
            ]);

        if ($validator->fails())
        {
            return response()->json(['success' => false, 'errors'=>$validator->errors()->first()]);
        }
        else
        {
            $admin = Client::findorfail($id);
            $admin->location  = $request->location;
            $admin->save();
            return response()->json(['success' => true, 'message'=>"Client Location Updated Successfully"]);
        }
    }
    public function get_clients(Request  $request){
        if ($request->ajax()) {

            $term = trim($request->term);
            $posts = DB::table('clients')->select('id', 'phone as text')
                ->where('name', 'LIKE', '%' . $term . '%')
                ->orWhere('phone', 'LIKE', '%' . $term . '%')
                ->orWhere('phone_2', 'LIKE', '%' . $term . '%')
                    ->orderBy('name', 'asc')->simplePaginate(10);

            $morePages = true;
            $pagination_obj = json_encode($posts);
            if (empty($posts->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $posts->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return \Response::json($results);

        }

    }

}