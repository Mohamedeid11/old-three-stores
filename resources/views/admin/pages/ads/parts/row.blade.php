@php
    $dateFilter=[$ad->date.' '.'00 00 00',$ad->date.' '.'23 59 59'];

  $invoiceDate= date('Y-m-d', strtotime($ad->date. ' + 10 days')).' 23::59::59';
  $platform_ides=\App\AdPlatform::where('ad_id',$ad->id)->pluck('platform_id');

@endphp
<td>{{$ad->date}}</td>
<td>

            @forelse($ad->platforms ??[] as $platform )
                {{$platform->title??''}} <br>
            @empty
            @endforelse

</td>
<td>

    <div class="form-check form-switch">
        <input class="form-check-input activeBtn" @if($ad->status==1)  checked
               @endif data-id="{{$ad->id}}" type="checkbox" role="switch">
    </div>

</td>

<td>{{$ad->ad_number	}}</td>
<td>

        @forelse($ad->products ??[] as $product)
            <span style="display:none">
                                                @php
                                                    $tags_ids= \App\productTag::where('product_id', $product->id)->pluck('tag_id')->toArray();
                                             $tags=\App\TagGroup::whereIn('id',$tags_ids)->get();
                                                @endphp
                                  </span>
            @forelse($tags ??[] as $tag)
                {{$tag->title??''}}
                <br>
            @empty

            @endforelse
            @empty

        @endforelse
</td>
<td>
        @forelse($ad->products ??[] as $product)
            {{$product->title??''}}
            <br>
        @empty
       @endforelse
</td>
<td>

    {{round($ad->result,2)}}

    <span style="display:none">
        {{$result=$ad->result	}}
    </span>

</td>
<td>
    {{round($ad->cost_per_result,2)}}
    <span style="display:none">
          {{$cost_per_result=$ad->cost_per_result}}
    </span>

</td>
<td>

    @if($ad->result==0)
        {{round($ad->cost_per_result,2)}}
    @else
        {{round($ad->result * $ad->cost_per_result,2)}}
    @endif

    <span style="display: none">
                                              @if($ad->result==0)
            {{$total1= $ad->cost_per_result}}
        @else
            {{$total1=$ad->result * $ad->cost_per_result}}
        @endif
      </span>

</td>
<td>
    @php($ordersNummbers=0)

        @forelse($ad->products ??[] as $product)
            <span style="display: none;">

                    @forelse($product->sell_orders ??[] as $order)




                        @if(optional($order)->created_at<= $ad->date.' '.'23 59 59'  && optional($order)->created_at>=$ad->date.' '.'00 00 00' && optional($order)->hide==0 )
                        {{$hasMatchingTag = false}}


                        @forelse($order->tags as $tag)
                            @if (in_array($tag->tag_id, $platform_ides))
                                {{$hasMatchingTag = true}}
                                @break

                            @endif
                        @empty
                        @endforelse
                           @if($hasMatchingTag)
                            {{$ordersNummbers=$ordersNummbers+1}}
                        @endif
                        @endif
                        @empty
                    @endforelse

			@empty							 </span>
        @endforelse

    {{$ordersNummbers}}


</td>

<td>
    @php($orderValue=0)
    @php($products_buy=0)


        @forelse($ad->products ??[] as $product)
            <span style="display: none;">

                    @forelse($product->sell_orders ??[]  as $order)

                        @if(optional($order)->created_at<= $ad->date.' '.'23 59 59'  && optional($order)->created_at>=$ad->date.' '.'00 00 00' && optional($order)->hide==0 )
                        {{$hasMatchingTag = false}}


                        @forelse($order->tags as $tag)
                            @if (in_array($tag->tag_id, $platform_ides))
                                {{$hasMatchingTag = true}}
                                @break

                            @endif
                        @empty
                        @endforelse

                    @if($hasMatchingTag)

                            {{$orderValue=$orderValue+$order->total_price??'0'}}
                            @php($product_buy=0)

                                @forelse(\App\SellOrderItem::where('order',$order->id)->where('hide',0)->get() ??[] as $details)

                                    @if($invoice=\App\BuyOrderItem::orderBy('id','DESC')->where('product',$details->product)->where('qty','>',0)->where('hide',0)->where('price','>',5)->where('created_at','<=',$invoiceDate)->first()??0)
                                        {{$product_buy=$product_buy+($invoice->price*$details->qty)}}
                                    @endif
                                 @empty
                                @endforelse
                            {{$products_buy=$products_buy+$product_buy}}
                        @endif
                    @endif
                     @empty
                    @endforelse

			@empty							 </span>
        @endforelse

    @if($ordersNummbers>0)
        {{$final_order_value=round($orderValue/$ordersNummbers,2)}}
    @else
        {{$final_order_value=round($orderValue,2)}}
    @endif

</td>
<td>
    {{$secondTotal=round($orderValue,2)}}

</td>
<td>


    @if($total1==0)
        {{round($secondTotal-$products_buy,2)}}

    @else
        {{round(($secondTotal-$products_buy)/$total1,2)}}

    @endif

    <span style="display:none">
             @if($total1==0)
            {{$roi=($secondTotal-$products_buy)}}

        @else
            {{$roi=($secondTotal-$products_buy)/$total1}}

        @endif


          </span>
</td>
<td>

    {{round($secondTotal-$products_buy-$total1,2)}}

    <span style="display:none">
                                            {{$revenue=$secondTotal-$products_buy-$total1}}

                                        </span>
</td>

<td>

    @if($ordersNummbers>0)
        {{round($total1/$ordersNummbers,2)}}
    @else
        {{round($total1,2)}}
    @endif


    <span style="display: none">
             @if($ordersNummbers>0)
            {{$cpo=$total1/$ordersNummbers}}
        @else
            {{$cpo=$total1}}
        @endif
        </span>

</td>

<th>
    @if($ordersNummbers>0)
        {{round($ad->result/$ordersNummbers,2)}}
    @else
        {{round($ad->result,2)}}
    @endif

    <span style="display:none">
           @if($ordersNummbers>0)
            {{$result_per_order=$ad->result/$ordersNummbers}}
        @else
            {{$result_per_order=$ad->result}}
        @endif
          </span>

</th>


<td>
    <div class="dropdown">
        <button class="btn btn-link" type="button" id="dropdownMenuButton"
                data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <i class="fas fa-ellipsis-h"></i>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <button class="dropdown-item editBtn" data-id="{{$ad->id}}">Edit
            </button>
            <button class="dropdown-item" data-id="{{$ad->id}}">Re-Ad</button>


            <button class="dropdown-item delete" data-id="{{$ad->id}}">Delete
            </button>
        </div>
    </div>


</td>