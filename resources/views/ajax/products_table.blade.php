<table class="table table-striped- table-bordered" id="kt_table_1xxs">
    <thead>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>QTY</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($products as $product)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$product->title}}</td>
            <td>
                {{$product->qty}}
</td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        Action
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if(permission_checker(Auth::guard('admin')->user()->id, 'edit_product'))
                            <a class="dropdown-item" href="{{route('products.edit', $product->id)}}">Edit</a>
                            <a class="dropdown-item" data-toggle="modal" href="#SKU{{ $product->id }}">SKU</a>
                            <a class="dropdown-item" href="{{url('products/timeline/'.$product->id)}}">Timeline</a>
                            {{-- <a class="dropdown-item" href="{{route('products.show', $product->id)}}">Images</a> --}}
                        @endif
                        @if(permission_checker(Auth::guard('admin')->user()->id, 'copy_product'))
                            <a class="dropdown-item" href="{{url('product_copy/'.$product->id)}}">Copy</a>
                        @endif
                        @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_product'))
                            <a class="dropdown-item" data-toggle="modal" href="#myModal-{{ $product->id }}">Delete</a>
                        @endif
                    </div>
                </div>
                <div class="modal fade" id="SKU{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">SKU Product</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><b>{{$product->title}}</b> SKU : <span class="text-info">Three00{{$product->id}}</span></p>
                                @if (count($product->colors) > 0 && count($product->sizes) > 0)
                                    @foreach ($product->colors as $color)
                                        @foreach ($product->sizes as $size)
                                            <p><b>{{$product->title}} - {{optional($color->color_info)->title}} - {{optional($size->size_info)->title}}</b> SKU : <span class="text-info">Three00{{$product->id}}-00{{$color->color}}-00{{$size->size}}</span></p>
                                        @endforeach
                                    @endforeach
                                @elseif (count($product->colors) > 0 && count($product->sizes) == 0)
                                    @foreach ($product->colors as $color)
                                        <p><b>{{$product->title}} - {{optional($color->color_info)->title}}</b> SKU : <span class="text-info">Three00{{$product->id}}-00{{$color->color}}-000</span></p>
                                    @endforeach
                                @elseif (count($product->colors) == 0 && count($product->sizes) > 0)
                                    @foreach ($product->sizes as $size)
                                        <p><b>{{$product->title}} - {{optional($size->size_info)->title}}</b> SKU : <span class="text-info">Three00{{$product->id}}-000-00{{$size->size}}</span></p>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                @if(permission_checker(Auth::guard('admin')->user()->id, 'delete_product'))
                    <div class="modal fade" id="myModal-{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Delete Product</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form role="form" action="{{ url('products/'.$product->id) }}" class="" method="POST">
                                        <input name="_method" type="hidden" value="DELETE">
                                        {{ csrf_field() }}
                                        <p>Are You Sure?</p>
                                        <button type="submit" class="btn btn-danger" name='delete_modal'>Delete</button>
                                        <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
