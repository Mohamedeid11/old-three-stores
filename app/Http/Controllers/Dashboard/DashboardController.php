<?php
namespace App\Http\Controllers\Dashboard;

use App\Inventory;
use App\Traits\Chat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

use App\Admin;
use App\Client;
use App\Agent;
use App\Product;
use App\SellOrder;
use App\BuyOrder;
use App\Fulfillment;
use App\ProductColor;
use App\ProductSize;
use App\SellOrderItem;
use App\BuyOrderItem;
use App\RuinedItem;
use App\City;
use App\OrderStatus;
use App\Category;
use App\Tag;

use App\Http\Controllers\Dashboard\SellingOrderController;

class DashboardController extends Controller
{

    public function index()
    {


        if(Auth::guard('admin')->user()->position == 2)
        {
            return app('App\Http\Controllers\Dashboard\SellingOrderController')->reps_delivery();
        }
        else
        {

            if(permission_group_checker(Auth::guard('admin')->user()->id, 'Selling Orders'))
            {
                if(permission_checker(Auth::guard('admin')->user()->id, 'view_all_selling_orders') || Auth::guard('admin')->user()->position == 1)
                {
                    $sell_orders = SellOrder::where('hide', '=', 0)->count();
                }
                else
                {
                   $sell_orders = SellOrder::where('hide', '=', 0)->whereIn('status', [5, 7])->where('delivered_by', '=', Auth::guard('admin')->user()->id)->count();
                }
            }
            elseif (Auth::guard('admin')->user()->position == 2 && !permission_group_checker(Auth::guard('admin')->user()->id, 'Selling Orders'))
            {
                $sell_orders = SellOrder::where('hide', '=', 0)->whereIn('status', [5, 7])->where('delivered_by', '=', Auth::guard('admin')->user()->id)->count();
            }
            $buy_orders = BuyOrder::where('hide', '=', 0)->count();
            return view('admin.home')->with([
            'buy_orders'=>$buy_orders]);
        }
    }

    
    public function fullfilment_checker()
    {
        $orders = SellOrder::where('fullfillment_checker', 0)->whereNotIn('status', array(0, 1, 11))->where('hide', 0)->orderBy('id')->paginate(100);
        foreach ($orders as $order)
        {
            $xch = Fulfillment::where('order', '=', $order->id)->get();
            if(true)
            {
                foreach ($xch as $x) {$x->delete();}
                foreach ($order->itemsq as $item)
                {
                    for ($i = 1; $i <= abs($item->qty); $i++)
                    {
                        $v = new Fulfillment;
                        $v->order = $order->id;
                        $v->item = $item->id;
                        $v->item_index = $i;
                        $v->save();
                    }
                }
            }
            $order->fullfillment_checker = 1;      
            $order->save();            
        }
        return view('fiif', compact('orders'));    
    }

    public function profile()
    {
        return view('admin.pages.admins.profile');
    }

    
    public function save_profile(Request $request)
    {
        $admin = Admin::findorfail(Auth::guard('admin')->user()->id);
        $validatedData = $request->validate([
            'name' => 'required',
            'phone' => 'required'
        ],
        [
            'name.required'=>'Please Enter Name',
            'phone.required'=>'Please Enter Phone Number'
        ]);
        if($admin->user_name != $request->user_name)
        {
            $validatedData = $request->validate([
                'user_name' => ['required', Rule::unique('admins')->where(function($query) {$query->where('hide', '=', 0);})]
            ],
            [
                'user_name.required'=>'Please Enter User Name',
                'user_name.unique'=>'This User Name Is Registered To Another User'
            ]);
        }

        if($admin->email != $request->email)
        {
            $validatedData = $request->validate([
                'email' => ['required', 'email', Rule::unique('admins')->where(function($query) {$query->where('hide', '=', 0);})]
            ],
            [
                'email.required'=>'Please Enter Email Address',
                'email.unique'=>'This Email Address Is Registered To Another User',
                'email.email'=>'Please Enter Correct Email Address'
            ]);
        }            
        
        $admin->name  = $request->name;
        $admin->user_name = $request->user_name;
        $admin->phone = $request->phone;
        $admin->email = $request->email;

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
        Session::flash('alert_message','Account Information Changed Successfully');
        return redirect('profile');
    }

    public function change_password()
    {
        return view('admin.pages.admins.change_password');
    }

    public function password_save(Request $request)
    {
        $admin = Admin::findorfail(Auth::guard('admin')->user()->id);
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
        Session::flash('alert_message','Password Changed Successfully');
        return redirect('change_password');
    }

    public function notes_dashboard_tasks(Request $request)
    {
        ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th style="width:150px;">Note</th>
                    <th>Order No</th>
                    <th style="width:75px;">Date</th>
                    <th style="width:100px;">Admin</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $date = date('Y-m-d h:i:s', strtotime('- 14 days'))."";
            foreach(uncompleted_notes($date) as $note)
            {
                if($note->order_info->hide == 0)
                {
                    ?>
                    <tr>
                        <td><p class="mb-0" style="width:200px; word-break: break-all;"><?php echo $note->note; ?></p></td>
                        <td><?php echo $note->order_info->order_number; ?></td>
                        <td><?php echo date('d-M', strtotime($note->created_at)); ?></td>
                        <td><?php echo $note->admin_info->name; ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
        <?php
    }



    public function get_reps_data(Request $request)
    {
        ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>REP Name</th>
                    <th>Total</th>
                    <th>Shipped</th>
                    <th>Delivered</th>
                    <th>Rejected</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $data = reps_data();
            for($i = 0; $i < count($data[1]); $i++)
            {
                ?>
                <tr>
                    <td><?php echo $data[1][$data[0][$i][1]][0]; ?></td>
                    <td><?php echo $data[1][$data[0][$i][1]][1]; ?></td>
                    <td><?php echo $data[1][$data[0][$i][1]][2]; ?></td>
                    <td><?php echo $data[1][$data[0][$i][1]][3]; ?></td>
                    <td><?php echo $data[1][$data[0][$i][1]][4]; ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    }


    public function order_dashboard_search(Request $request)
    {

        $order = SellOrder::where(function ($query) use ($request) {
            $query->where('order_number', '=', $request->search)
                ->orWhere('shipping_number', '=', $request->search);
        })->where('hide', '=', 0)->first();
        if($order !== NULL)
        {
            ?>
            <table class="table table-striped table-bordered table-hover table-checkable" id="kt_table_2">
				<!--
				<thead>
					<tr>
						<th>Order ID</th>
						<th>Client Name <hr /> Client Phone</th>
						<th>Status <hr /> REP</th>
						<th>Order Date <hr /> City</th>
						<th>Items <hr /> Total</th>
						<th>Action</th>
					</tr>
				</thead>
				-->
                <tbody>
                <tr>
                    <td><b><?php echo $order->order_number; ?></b></td>
                    <td><b><?php echo $order->client_info->name; ?></b> <hr /> <?php echo $order->client_info->phone; ?></td>
                    <td>
                        <?php if($order->status > 0) { echo $order->status_info->title; } else {echo "Pending"; } ?>
                        <hr />
                        <?php if($order->delivered_by > 0) {echo $order->delivery_info->name; } ?>
                    </td>
                    <td>
                        <?php echo date('Y-m-d', strtotime($order->created_at)); ?>
                        <hr />
                        <?php if($order->city > 0) {echo $order->city_info->title; } ?>
                    </td>
                    <td>PCS : <?php echo $order->itemsq->sum('qty'); ?> <hr /> <?php echo $order->total_price + $order->shipping_fees; ?></td>
                    <td>
						<div class="dropdown">
							<button class="btn btn-link" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" 
							aria-expanded="false">
								<i class="fas fa-ellipsis-h"></i>
							</button>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							    <?php
							    if(permission_checker(Auth::guard('admin')->user()->id, 'edit_selling_orders'))
							    {
							        ?>
								    <a class="dropdown-item" href="<?php echo route('selling_order.edit', $order->id); ?>">Edit</a>							        
							        <?php
							    }
							    if(permission_checker(Auth::guard('admin')->user()->id, 'add_selling_order'))
							    {
							        if($order->order_id == 0)
								    {
								        ?>
								        <a class="dropdown-item" href="<?php echo url('selling_reorder/'.$order->id); ?>">Re-Order</a>
                                        <?php
								    }
								    else
								    {
								        ?>
								        <a class="dropdown-item" href="<?php echo url('selling_reorder/'.$order->order_id); ?>">Re-Order</a>										    
							            <?php
								    }
							    }
                                ?>
								<a class="dropdown-item" href="<?php echo route('selling_order.show', $order->id); ?>">Details</a>
								<a class="dropdown-item" href="<?php echo url('/selling_order/orders_operation/Print_Invoice?orders='.$order->id); ?>" target="_blank">Invoice</a>
							    <a class="dropdown-item sellorder_notes_viewer" order-num="<?php echo $order->id; ?>" data-toggle="modal" 
								url="<?php echo url('sellorder_notes_viewer'); ?>" href="#myNotes-<?php echo $order->id; ?>">Notes</a>
								<?php

								if($order->time_lines->count() > 0)

								{

								    ?>

								    <a class="dropdown-item" data-toggle="modal" href="#myTime-<?php echo $order->id; ?>">Time Line</a>								    

								    <?php

								}

								if(permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order'))

								{

								    ?>

								    <a class="dropdown-item" data-toggle="modal" href="#myModal-<?php echo $order->id; ?>">Delete</a>								    

								    <?php

								}

								?>

							</div>

						</div>

						<div class="modal fade" id="myNotes-<?php echo  $order->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

						<div class="modal-dialog modal-lg">

						<div class="modal-content">

						<div class="modal-header">

							<h5 class="modal-title">Order Notes</h5>

							<button type="button" class="close" data-dismiss="modal" aria-label="Close">

							<span aria-hidden="true">&times;</span>

							</button>

						</div>

						<div class="modal-body">

						<table class="table table-striped">

							<tbody>

								<?php

								if($order->note != '')

								{

								    ?>

    								<tr>

    									<td><?php echo date('Y-m-d h:i A', strtotime($order->created_at)); ?></td>

    									<td>
                                            <?php echo $order->note; ?> 
                                            
                                        </td>

    								</tr>

                                    <?php

								}
                                $tags = Tag::get();
								foreach ($order->notes as $note)

								{

								    ?>

    								<tr>

    									<td><?php echo date('Y-m-d h:i A', strtotime($note->created_at)); ?></td>

    									<td><?php echo $note->note;
                                            foreach ($note->tags as $tag)
                                            {
                                                ?>
												<span class="badge badge-<?php echo $tag->tag_info->color; ?>"><?php echo $tag->tag_info->title; ?></span>
                                                <?php
                                            }
                                            ?>
                                    </td>

    								</tr>

    								<?php

								}

								$repps = Admin::where('position', '=', 1)->where('hide', '=', 0)->get();

                                ?>

							</tbody>

						</table>

						<form role="form" action="<?php echo url('selling_order_notes/'.$order->id); ?>" class="" method="POST" id="ajsuformreload">

						<?php echo csrf_field(); ?>

						<div id="ajsuform_yu"></div>

						<div class="form-group">

						    <label>Note</label>

							<textarea name="note" class="form-control"></textarea>

						</div>

						<div class="form-group">
						    <label>Rep </label>
							<select name="rep[]" multiple class="d-block form-control orders_selector_mul_reps">
							    <?php
							    foreach ($repps as $sa)
                                {
                                    ?>
    							    <option value="<?php echo $sa->id; ?>"><?php echo $sa->name; ?></option>
	                                <?php
                                }
                                ?>
							</select>
						</div>
                        <div class="form-group">
                            <label>Tags </label>
                            <select name="tag[]" multiple class="d-block form-control orders_selector_mul_tags">
                                <?php
                                foreach ($tags as $sa)
                                {
                                    ?>
                                    <option value="<?php echo $sa->id; ?>"><?php echo $sa->title; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
						<button type="submit" class="btn btn-success" name='delete_modal'>Save</button>

						<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

						</form>

						</div>

						</div>

						</div>

						</div>





                        <div class="modal fade" id="myTime-<?php echo $order->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

							<div class="modal-dialog modal-lg">

							<div class="modal-content">

							<div class="modal-header">

								<h5 class="modal-title">Order Timeline</h5>

								<button type="button" class="close" data-dismiss="modal" aria-label="Close">

								<span aria-hidden="true">&times;</span>

								</button>

							</div>

							<div class="modal-body time_line_list">

							<ul>

							    <?php

							    foreach ($order->time_lines as $line)

							    {

							        ?>

    							    <li class="row">

    							        <div class="col-md-3"><b><?php echo date('Y-m-d h:i A', strtotime($line->created_at)); ?></b></div>

    							        <div class="col-md-9"><?php echo $line->admin_info->name.$line->text; ?></div>

    							    </li>

    							    <?php

							    }

							    ?>

							</ul>

							    

							

							</div>

							</div>

							</div>

						</div>

						<?php

						if(permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order'))

						{

						    ?>

    						<div class="modal fade" id="myModal-<?php echo $order->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    						<div class="modal-dialog">

    						<div class="modal-content">

    						<div class="modal-header">

    							<h5 class="modal-title">Delete Order</h5>

    							<button type="button" class="close" data-dismiss="modal" aria-label="Close">

    							<span aria-hidden="true">&times;</span>

    							</button>

    						</div>

    						<div class="modal-body">

    						<form role="form" action="<?php echo url('selling_order/'.$order->id); ?>" class="" method="POST">

    						<input name="_method" type="hidden" value="DELETE">

    						<?php echo csrf_field(); ?>

    						<p>Are You Sure?</p>

    						<button type="submit" class="btn btn-danger" name='delete_modal'>Delete</button>

    						<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>

    						</form>

    						</div>

    						</div>

    						</div>

    						</div>

    						<?php

						}

						?>

					</td>

                </tr>



                </tbody>

            </table>



            <ul class="container-fluid">

            <?php

                foreach ($order->time_lines as $line)

                {

                    ?>

                    <li class="row mb-1">

                        <div class="col-md-4"><?php echo date('Y-m-d h:i A', strtotime($line->created_at)); ?></div>

                        <div class="col-md-8"><?php echo $line->admin_info->name.$line->text; ?></div>

                    </li>

                    <hr />

                    <?php

                }

                ?>

            </ul>

            

            <?php

        }

        

    }



    public function purchases_dashboard_tasks(Request $request)
    {
        ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $rows = purchases_data();
            foreach ($rows as $row)
            {

                    ?>
                    <tr>

                        <td>
                            <?php
                            echo optional($row->product_info)->title . ' '; // Add space after product title
                            echo optional($row->color_info)->title . ' ';   // Add space after color title
                            echo optional($row->size_info)->title;
                            ?>
                        </td>

                        <td><?php echo $row->count; ?></td>
                    </tr>
                    <?php

            }
            ?>
            </tbody>
        </table>
        <?php
    }

    

    public function print_purchase_list()

    {

        $items =purchases_data();




        return view('admin.print_purchase')->with(['items'=>$items]);

    }

    

    public function product_dashboard_options(Request $request)
    {
        $product = $request->item;
        $colors = ProductColor::where('product', '=', $product)->get();
        $sizes = ProductSize::where('product', '=', $product)->get();
        if($colors->count() > 0 || $sizes->count() > 0)
        {
            ?>
            <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <?php if($colors->count() > 0){ ?><th>Color</th><?php } ?>
                    <?php if($sizes->count() > 0){ ?><th>Size</th><?php } ?>
                    <th>Qty</th>
                    <th>Fullfilment</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($colors->count() > 0)
                {
                    foreach ($colors as $color)
                    {
                        if($sizes->count() == 0)
                        {
                            ?>
                            <tr>
                                <td><?php echo $color->color_info->title; ?></td>
                                <td><?php echo qty_sold_inventory($product, $color->color_info->id, 0); ?></td>
                                <td><?php echo product_fullfilment_units($product, $color->color_info->id, 0); ?></td>
                            </tr>
                            <?php
                        }
                        else
                        {
                            foreach ($sizes as $size)
                            {
                                ?>
                                <tr>
                                    <td><?php echo $color->color_info->title; ?></td>
                                    <td><?php echo $size->size_info->title; ?></td>
                                    <td><?php echo qty_sold_inventory($product, $color->color_info->id, $size->size_info->id); ?></td>
                                    <td><?php echo product_fullfilment_units($product, $color->color_info->id, $size->size_info->id); ?></td>
                                </tr>
                                <?php
                            }
                        }
                    }
                }
                else if($colors->count() == 0 && $sizes->count() > 0)
                {
                    foreach ($sizes as $size)
                    {
                        ?>
                        <tr>
                            <td><?php echo $size->size_info->title; ?></td>
                            <td><?php echo qty_sold_inventory($product, 0, $size->size_info->id); ?></td>
                            <td><?php echo product_fullfilment_units($product, 0, $size->size_info->id); ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
            </table>
            <?php
        }
        else
        {
            ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Available Units</th>
                        <th>Fullfilment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo qty_sold_inventory($product, 0, 0); ?></td>
                        <td><?php echo product_fullfilment_units($product, 0, 0); ?></td>
                    </tr>
                </tbody>
            </table>
            <?php
        }
    }

    public function test()
    {
        $items = array();
        $products_num = array();
        $products = array();
        $sorders = SellOrder::where('hide', '=', 0)->whereNotIn('status', [0, 1, 8])->get()->pluck('id');
        $borders = BuyOrder::where('hide', '=', 0)->get()->pluck('id');
        $total_prices = 0;
        $items = Product::where('discontinue', '=', 0)->where('hide', 0)->get();
        foreach ($items as $product)
        {
            $colors = ProductColor::where('product', '=', $product->id)->get();
            $sizes = ProductSize::where('product', '=', $product->id)->get();
            if($colors->count() > 0 && $sizes->count() == 0)
            {
                foreach ($colors as $color)
                {
                    $sitems = SellOrderItem::
                    join('fulfillments', 'fulfillments.item', '=', 'sell_order_items.id')->whereIn('sell_order_items.order', $sorders)
                    ->where('sell_order_items.product', $product->id)
                    ->where('sell_order_items.color', $color->color)
                    ->where('sell_order_items.size', 0)
                    ->groupBy('sell_order_items.id')
                    ->select('sell_order_items.*')
                    ->get()
                    ->sum('qty');
                    $cr = RuinedItem::where('product', '=', $product->id)->where('color', '=', $color->color)->where('size', '=', 0)->first();
                    if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}

                    $bitems = BuyOrderItem::whereIn('order', $borders)->where('product', '=', $product->id)->where('color', '=', $color->color)->where('size', '=', 0)->where('qty', '!=', 0)->get()->sum('qty');

                    $price = BuyOrderItem::whereIn('order', $borders)->where('product', '=', $product->id)->where('color', '=', $color->color)->where('size', '=', 0)->where('qty', '!=', 0)->get()->sum(('qty * price'));

                    $px = "";

                    $px = $product->title;

                    if($color->color > 0) {$px .= " - ".$color->color_info->title;}

                    $products[] = array($px, $color->color, 0, $sitems, $bitems, $price, $product->id, $ruined);

                }

            }

            else if($colors->count() == 0 && $sizes->count() > 0)
            {
                foreach ($sizes as $size)
                {
                    $sitems = SellOrderItem::
                    join('fulfillments', 'fulfillments.item', '=', 'sell_order_items.id')
                    ->whereIn('sell_order_items.order', $sorders)
                    ->where('sell_order_items.product', $product->id)
                    ->where('sell_order_items.color', 0)
                    ->where('sell_order_items.size', $size->size)
                    ->groupBy('sell_order_items.id')
                    ->select('sell_order_items.*')
                    ->get()
                    ->sum('qty');

                    $bitems = BuyOrderItem::whereIn('order', $borders)->where('product', '=', $product->id)->where('color', '=', 0)->where('size', '=', $size->size)->where('qty', '!=', 0)->get()->sum('qty');
                    $price = BuyOrderItem::whereIn('order', $borders)->where('product', '=', $product->id)->where('color', '=', 0)->where('size', '=', $size->size)->where('qty', '!=', 0)->get()->sum(('qty * price'));
                    $cr = RuinedItem::where('product', '=', $product->id)->where('color', '=', 0)->where('size', '=', $size->size)->first();
                    if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}
                    $px = "";
                    $px = $product->title;
                    if($size->size > 0) {$px .= " - ".$size->size_info->title;}
                    $products[] = array($product->title, 0, $size->size, $sitems, $bitems, $price, $product->id, $ruined);
                }
            }
            else if($colors->count() > 0 && $sizes->count() > 0)
            {
                foreach ($colors as $color)
                {
                    foreach ($sizes as $size)
                    {
                        $sitems = SellOrderItem::
                        join('fulfillments', 'fulfillments.item', '=', 'sell_order_items.id')
                        ->whereIn('sell_order_items.order', $sorders)
                        ->where('sell_order_items.product', $product->id)
                        ->where('sell_order_items.color', $color->color)
                        ->where('sell_order_items.size', $size->size)
                        ->groupBy('sell_order_items.id')
                        ->select('sell_order_items.*')
                        ->get()
                        ->sum('qty');
                        $bitems = BuyOrderItem::whereIn('order', $borders)->where('product', '=', $product->id)->where('color', '=', $color->color)->where('size', '=', $size->size)->where('qty', '!=', 0)->get()->sum('qty');
                        $price = BuyOrderItem::whereIn('order', $borders)->where('product', '=', $product->id)->where('color', '=', $color->color)->where('size', '=', $size->size)->where('qty', '!=', 0)->get()->sum(('qty * price'));
                        $cr = RuinedItem::where('product', '=', $product->id)->where('color', '=', $color->color)->where('size', '=', $size->size)->first();
                        if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}
                        $px = "";
                        $px = $product->title;
                        if($color->color > 0) {$px .= " - ".$color->color_info->title;}
                        if($size->size > 0) {$px .= " - ".$size->size_info->title;}
                        $products[] = array($px, $color->color, $size->size, $sitems, $bitems, $price, $product->id, $ruined);
                    }
                }
            }
            elseif($colors->count() == 0 && $sizes->count() == 0)
            {
                $sitems = SellOrderItem::
                join('fulfillments', 'fulfillments.item', '=', 'sell_order_items.id')
                ->whereIn('sell_order_items.order', $sorders)
                ->where('sell_order_items.product', $product->id)
                ->where('sell_order_items.color', 0)
                ->where('sell_order_items.size', 0)
                ->groupBy('sell_order_items.id')
                ->select('sell_order_items.*')

                ->get()

                ->sum('qty');

                $bitems = BuyOrderItem::whereIn('order', $borders)->where('product', '=', $product->id)->where('color', '=', 0)->where('size', '=', 0)->where('qty', '!=', 0)->get()->sum('qty');

                $price = BuyOrderItem::whereIn('order', $borders)->where('product', '=', $product->id)->where('color', '=', 0)->where('size', '=', 0)->where('qty', '!=', 0)->get()->sum(('qty * price'));

                $cr = RuinedItem::where('product', '=', $product->id)->where('color', '=', 0)->where('size', '=', 0)->first();

                if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}

                $px = "";

                $px = $product->title;

                $products[] = array($px, 0, 0, $sitems, $bitems, $price, $product->id, $ruined);

            }

        }

        /*

        $orders = BuyOrder::where('hide', '=', 0)->get();

        foreach ($orders as $order)

        {

            foreach ($order->itemsq as $item)

            {

                $itx = $item->product."_".$item->color.$item->size;

                if(in_array($itx, $items))

                {

                    $in = array_search($itx, $items);

                    $products[$in][4] = $products[$in][4] + $item->qty;

                    $products[$in][5] = $products[$in][5] + ($item->qty * $item->price);

                    // $total_items = $total_items + $item->qty;

                }

                else

                {

                    $items[] = $itx;

                    $px = "";

                    $px = $item->product_info->title;

                    if($item->color > 0) {$px .= " - ".$item->color_info->title;}

                    if($item->size > 0) {$px .= " - ".$item->size_info->title;}

                    $cr = RuinedItem::where('product', '=', $item->product)->where('color', '=', $item->color)->where('size', '=', $item->size)->first();

                    if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}

                    $products[] = array($px, $item->color, $item->size, 0, $item->qty, $item->qty * $item->price, $item->product, $ruined);

                    // $total_items = $total_items + $item->qty - $ruined;

                }

            }

        }

        */

        

        $total_amount = 0;

        /*

        for ($i = 0; $i < count($products); $i++)

        {

            $product = $products[$i][6];

            $color = $products[$i][1];

            $size = $products[$i][2];

            $tot = $products[$i][4] - $products[$i][3] - $products[$i][7];

            if($tot > 0)

            {

                $total_items = $total_items + $tot;

            }

            $ruined_items = RuinedItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->first();

            if($ruined_items !== NULL) {$ruined = $ruined_items->qty;}else {$ruined = 0;}

            $sold = 0;

            $sold_items = SellOrderItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->where('qty', '!=', 0)->get();

            foreach ($sold_items as $item)

            {

                if($item->order_info->hide == 0 && ($item->order_info->status != 0 && $item->order_info->status != 1 && 

                $item->order_info->status != 8 && $item->order_info->status != 11)) {$sold = $sold + $item->qty;}

                else if($item->order_info->hide == 0 && $item->order_info->status == 11)

                {

                    $xch = Fulfillment::where('order', '=', $item->order_info->id)->where('item', '=', $item->id)->first();

                    if($xch !== NULL)

                    {

                        $xxch = Fulfillment::where('order', '=', $item->order_info->id)->where('item', '=', $item->id)->get()->count();

                        $sold = $sold + $xxch;

                    }

                }

            }

            $bought = 0;

            $amount = 0;

            $bought_items = BuyOrderItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->where('qty', '!=', 0)->get();

            foreach ($bought_items as $item)

            {

                if($item->order_info->hide == 0) {$bought = $bought + $item->qty; $amount = $amount + ($item->qty * $item->price);}

            }

            if($bought != 0)

            {

                $total_amount = $total_amount + ($bought-$sold-$ruined) * ($amount / $bought);

            }

        }

        */

        $total_items = 0;

        

        return view('admin.pages.inventory.index')->with(['products'=>$products, 'total_items'=>$total_items, 'total_prices'=>$total_amount]);

    }

    

    public function product_available_units ($product, $color, $size)
    {
        $items = 0;
        $sorders = SellOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->whereNotIn('status', [0, 1, 8])->pluck('id');
        $sitems = SellOrderItem::
            whereIn('sell_order_items.order', $sorders)
            ->where('sell_order_items.product', $product)
            ->where('sell_order_items.color', $color)
            ->where('sell_order_items.size', $size)
            ->select('sell_order_items.*')
            ->sum('qty');
        
        $borders = BuyOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->pluck('id');
        $bitems = BuyOrderItem::whereIn('order', $borders)->where('product', $product)->where('color', $color)->where('size', $size)->sum('qty');
        $cr = RuinedItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->first();
        if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}
        $items = $bitems - ($sitems + $ruined);
        return $items;
    }

    public function product_available_units_withoutpat ($product, $color, $size)
    {
        $inventory = Inventory::where('product', $product)->where('color', $color)->where('size', $size)->first();
        if($inventory === NULL)
        {
            $items = 0;
            $sorders = SellOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->whereNotIn('status', [7, 8, 9, 10, 12])->pluck('id');
            $sold =  SellOrderItem::
                whereIn('sell_order_items.order', $sorders)
                ->where('sell_order_items.product', $product)
                ->where('sell_order_items.color', $color)
                ->where('sell_order_items.size', $size)
                ->where('sell_order_items.qty', '>', 0)
                ->sum('qty');
            $sold = (int) $sold;
            
            $borders = BuyOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->pluck('id');
            $bitems = BuyOrderItem::whereIn('order', $borders)->where('product', $product)->where('color', $color)->where('size', $size)->sum('qty');
            $cr = RuinedItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->first();
            if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}
            $items = $bitems - ($sold + $ruined);

            $inventory = new Inventory;
            $inventory->product = $product;
            $inventory->color = $color;
            $inventory->size = $size;
            $inventory->sold = $sold;
            $inventory->bought = $bitems;
            $inventory->save();
        }
        else 
        {
            $cr = RuinedItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->first();
            if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}
            $items = $inventory->bought - ($inventory->sold + $ruined);
        }
        return $items;
    }

    public function getClientInfo(Request  $request){
        $won=$lost=$open=0;
        $orders=[];
        $client=Client::where('phone',$request->phone)->orWhere('phone_2',$request->phone)->first();
        if ($client){
            $won=SellOrder::whereIn('status',[5,6])->where('client',$client->id)->where('hide',0)->count();
            $lost=SellOrder::whereIn('status',[7,8])->where('client',$client->id)->where('hide',0)->count();
            $open=SellOrder::whereNotIn('status',[5,6,7,8])->where('client',$client->id)->where('hide',0)->count();
            $orders=SellOrder::where('client',$client->id)->where('hide',0)->latest()->get();

        }
        return view('admin.client_info',compact('won','lost','open','orders','client'));

    }
}



