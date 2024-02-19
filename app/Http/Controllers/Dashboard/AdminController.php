<?php
namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

use App\Admin;
use App\AdminPosition;
use App\AdminPermission;

class AdminController extends Controller
{
    public function index()
    {
        if(!permission_group_checker(Auth::guard('admin')->user()->id, 'Admins')) {abort(404);}
        $admins = Admin::where('hide', '=', 0)->get();
        return view('admin.pages.admins.index')->with(['admins'=>$admins]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_admin')) {abort(404);}
        $positions = AdminPosition::where('hide', '=', 0)->get();
        return view('admin.pages.admins.create')->with(['positions'=>$positions]);    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_admin')) {abort(404);}
        $validatedData = $request->validate([
            'name' => 'required',
            'user_name' => ['required', Rule::unique('admins')->where(function($query) {$query->where('hide', '=', 0);})],
            'email' => ['required', 'email', Rule::unique('admins')->where(function($query) {$query->where('hide', '=', 0);})],
            'password' => 'required|min:6|confirmed',
            'phone' => 'required',
            'position' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg,gif,webp'
        ],
        [
            'name.required'=>'Please Enter Name',
            'user_name.required'=>'Please Enter User Name',
            'user_name.unique'=>'This User Name Is Registered To Another User',
            'email.required'=>'Please Enter Email Address',
            'email.unique'=>'This Email Address Is Registered To Another User',
            'email.email'=>'Please Enter Correct Email Address',
            'password.required'=>'Please Enter Password',
            'password.min'=>'Password Must Be At Least 6 Charachters',
            'password.confirmed'=>'Password & Its Confirmation Not Matching',
            'phone.required'=>'Please Enter Phone Number',
            'image.mimes' => 'Please Choose Image File',
            'position.required' => 'Please Choose Admin Position'
        ]);
        $admin = new Admin;
        $admin->name  = $request->name;
        $admin->user_name = $request->user_name;
        $admin->phone = $request->phone;
        $admin->email = $request->email;
        $admin->position = $request->position;
        $admin->password = bcrypt($request->password); 
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
            $destinationPath = public_path('/uploads/admins');
            $image->move($destinationPath, $imageName);
            $admin->image ='/uploads/admins/'.$imageName;
        }
        $admin->save();   
        
        for ($i = 0; $i < count($request->permission); $i++)
        {
            $ap = new AdminPermission;
            $ap->admin = $admin->id;
            $ap->permission = $request->permission[$i];
            $ap->save();
        }
        return redirect()->route('admins.index');    
    }

    public function edit($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_admin')) {abort(404);}
        $admin = Admin::findorfail($id);
        if($admin->hide == 0)
        {
            $positions = AdminPosition::where('hide', '=', 0)->get();
            return view('admin.pages.admins.edit')->with(['admin'=>$admin, 'positions'=>$positions]);    
        }
        else
        {
            abort(404);
        }
    }

    public function change_password($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'change_admin_password')) {abort(404);}
        $admin = Admin::findorfail($id);
        if($admin->hide == 0)
        {
            return view('admin.pages.admins.edit_password')->with(['admin'=>$admin]);    
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
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_admin')) {abort(404);}
        $admin = Admin::findorfail($id);
        if($admin->hide == 0)
        {
            $validatedData = $request->validate([
                'name' => 'required',
                'phone' => 'required',
                'position' => 'required'
            ],
            [
                'name.required'=>'Please Enter Name',
                'phone.required'=>'Please Enter Phone Number',
                'position.required' => 'Please Choose Admin Position'
            ]);

            $admin = Admin::findorfail($id);
            if($admin->user_name != $request->user_name)
            {
                $validatedData = $request->validate([
                    'user_name' => ['required', Rule::unique('admins')->where(function($query) {$query->where('hide', '=', 0);})],
                ],
                [
                    'user_name.required'=>'Please Enter User Name',
                    'user_name.unique'=>'This User Name Is Registered To Another User'
                ]);
            }
            if($admin->email != $request->email)
            {
                $validatedData = $request->validate([
                    'email' => ['required', 'email', Rule::unique('admins')->where(function($query) {$query->where('hide', '=', 0);})],
                ],
                [
                    'email.required'=>'Please Enter Email Address',
                    'email.unique'=>'This Email Address Is Registered To Another User',
                    'email.email'=>'Please Enter Correct Email Address',
                ]);
            }            
            
            $admin->name  = $request->name;
            $admin->user_name = $request->user_name;
            $admin->phone = $request->phone;
            $admin->email = $request->email;
            $admin->position = $request->position;
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
                $destinationPath = public_path('/uploads/admins');
                $image->move($destinationPath, $imageName);
                $admin->image = '/uploads/admins/'.$imageName;
            }
            
            $admin->save();
            $old_prs = AdminPermission::where('admin', $admin->id)->get();
            foreach ($old_prs as $old_pr)
            {
                $old_pr->delete();
            }
            for ($i = 0; $i < count($request->permission); $i++)
            {
                $ap = new AdminPermission;
                $ap->admin = $admin->id;
                $ap->permission = $request->permission[$i];
                $ap->save();
            }
            return redirect()->route('admins.index');
        }
        else
        {
            abort(404);
        }
    }

    public function password_save (Request $request, $id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'change_admin_password')) {abort(404);}
        $admin = Admin::findorfail($id);
        if($admin->hide == 0)
        {
            $validatedData = $request->validate([
                'password' => 'required|min:6|confirmed',
            ],
            [
                'password.required'=>'Please Enter Password',
                'password.min'=>'Password Must Be At Least 6 Charachters',
                'password.confirmed'=>'Password & Its Confirmation Not Matching',
            ]);
            $admin->password = bcrypt($request->password);
            $admin->save();
            return redirect()->route('admins.index');
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
        if(!permission_checker(Auth::guard('admin')->user()->id, 'delete_admin')) {abort(404);}
        $admin = Admin::findorfail($id);
        $admin->hide = 1;
        $admin->save();
        return redirect()->route('admins.index');  
    }

    public function getAdmins(Request  $request){
        if ($request->ajax()) {

            $term = trim($request->term);
            $posts = DB::table('admins')->select('id', 'name as text')
               -> where('position', '=', 1)->where('hide', '=', 0)
                ->where('name', 'LIKE', '%' . $term . '%')
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
