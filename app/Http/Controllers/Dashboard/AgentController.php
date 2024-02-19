<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

use App\Agent;
use App\City;

class AgentController extends Controller
{
    public function index()
    {
        if(!permission_group_checker(Auth::guard('admin')->user()->id, 'Agents')) {abort(404);}
        $clients = Agent::where('hide', '=', 0)->with(['agent_balances'])->get();
        return view('admin.pages.agents.index')->with(['clients'=>$clients]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_agent')) {abort(404);}
        $cities = City::where('hide', '=', 0)->orderBy('title')->get();
        return view('admin.pages.agents.create')->with(['cities'=>$cities]);   
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_agent')) {abort(404);}
        $validatedData = $request->validate([
            'name' => 'required',
        ],
        [
            'name.required'=>'Please Enter Name',
        ]);
        if($request->email != '')
        {
            $validatedData = $request->validate([
                'email' => ['nullable', 'email', Rule::unique('agents')->where(function($query) {$query->where('hide', '=', 0);})],
            ],
            [
                'email.unique'=>'This Email Address Is Registered To Another User',
                'email.email'=>'Please Enter Correct Email Address'
            ]);
        }            
        if($request->phone != '')
        {
            $validatedData = $request->validate([
                'name' => 'required',
                'phone' => ['required', 'min:11', Rule::unique('agents')->where(function($query) {$query->where('hide', '=', 0);})],
            ],
            [
                'phone.required'=>'Please Enter Phone Number',
                'phone.min'=>'Phone Number Min. Length Is 11',
                'phone.unique'=>'This Phone Number Is Registered To Another Agent',
            ]);
        }
        $admin = new Agent;
        $admin->name  = $request->name;
        $admin->phone = $request->phone;
        $admin->email = $request->email;
        $admin->address = $request->address;
        if($request->city) {$admin->city = $request->city;}
        if($request->hasFile('image'))
        {
            $validatedData = $request->validate([
                'image' => 'mimes:jpeg,png,jpg,gif,svg,gif,webp'
            ],
            $messages = [
                'image.mimes' => 'Please Choose Image File'
            ]);
    
    
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/agents');
            $image->move($destinationPath, $imageName);
            $admin->image = '/uploads/agents/'.$imageName;
        }
        $admin->save();   
        return redirect()->route('agents.index');    
    }

    public function edit($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_agent')) {abort(404);}
        $cities = City::where('hide', '=', 0)->orderBy('title')->get();
        $admin = Agent::findorfail($id);
        if($admin->hide == 0)
        {
            return view('admin.pages.agents.edit')->with(['admin'=>$admin, 'cities'=>$cities]);    
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
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_agent')) {abort(404);}
        $admin = Agent::findorfail($id);
        if($admin->hide == 0)
        {
            $validatedData = $request->validate([
                'name' => 'required',
            ],
            [
                'name.required'=>'Please Enter Name',
            ]);

            $admin = Agent::findorfail($id);
            
            if($admin->email != $request->email)
            {
                $validatedData = $request->validate([
                    'email' => ['nullable', 'email', Rule::unique('agents')->where(function($query) {$query->where('hide', '=', 0);})],
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
                    'phone' => ['required',  'min:11', Rule::unique('agents')->where(function($query) {$query->where('hide', '=', 0);})],
                ],
                [
                    'name.required'=>'Please Enter Name',
                    'phone.required'=>'Please Enter Phone Number',
                    'phone.min'=>'Phone Number Min. Length Is 11',
                    'phone.unique'=>'This Phone Number Is Registered To Another Agent',
                ]);
            }
            $admin->name  = $request->name;
            $admin->phone = $request->phone;
            $admin->email = $request->email;
            $admin->address = $request->address;
            if($request->city) {$admin->city = $request->city;}
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
                $destinationPath = public_path('/uploads/agents');
                $image->move($destinationPath, $imageName);
                $admin->image = '/uploads/agents/'.$imageName;
            }

            $admin->save();
            return redirect()->route('agents.index');
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
        if(!permission_checker(Auth::guard('admin')->user()->id, 'delete_agent')) {abort(404);}
        $admin = Agent::findorfail($id);
        $admin->hide = 1;
        $admin->save();
        return redirect()->route('agents.index');  
    }


    public function find_agent(Request $request)
    {
        $search = $request->search;
        if($search != '')
        {
            echo "<div id='cient_finder_data'>";
            $find_client = Agent::where('hide', '=', 0)->where(function($query) use ($search)
            {
                $query->where('phone', '=', $search)->
                orwhere('name', '=', $search)->
                orwhere('email', '=', $search);
            })->first();
            if($find_client !== NULL)
            {
                echo '<input type="hidden" name="client" value="'.$find_client->id.'" />';
                echo "<h3>Agent Details</h3>";
                echo "<div class='row'>";
                echo "<div class='col-md-4'><p><b>Name : </b>".$find_client->name."</p></div>";
                echo "<div class='col-md-4'><p><b>Phone No. : </b>".$find_client->phone."</p></div>";
                echo "<div class='col-md-4'><p><b>Email : </b>".$find_client->email."</p></div>";
                echo "</div>";
            }
            else
            {
                echo "<h3 class='text-center'>No Results Found - Add New Agent</h3>";
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

				
                <?php
            }
            echo '</div>';
        }
        else
        {
            echo '';
        }
    }
}
