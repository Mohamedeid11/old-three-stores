<tr>
    <td>{{ @$name->title }}  <input id="product_qty_id" type ="hidden" name="product[]" value="{{ $product->product_id }}"/></td>
	@if($product->color_id !=0)
    <td>{{ @$color->title }} <input  type ="hidden" name="color[]" value="{{ @$product->color_id }}"/></td>
	@else
	<td><input type ="hidden" name="color[]" value="0"/></td>
	@endif
	@if($product->size_id !=0)
    <td>{{ @$size->title }} <input  type ="hidden" name="size[]" value="{{ @$product->size_id }}"/></td>
	@else
	<td><input type ="hidden" name="size[]" value="0"/></td>
	@endif	
    <td><input type ="text" data-id="{{ $product->id }}" class="qty form-control buyorder_items_price" name="qty[]" value="{{ $product->qty }}"/></td>
    <td><input type ="text" data-id="{{ $product->id }}" name="price[]" class="form-control buyorder_items_price item_price" value="{{ $product->price }}"/></td>
    <td class="price">{{ $product->qty * $product->price }}</td>
    <td style="width:100px;"><button  type="button" data-id="{{ $product->id }}" class="btn btn-danger btn-sm btn-block delete_order_item delete" ><i class="fas fa-trash-alt"></i></button></td>
</tr>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>

/** Get Total Price **/
var sum = 0;
$('.price').each(function(){
    sum += parseFloat($(this).text());  // Or this.innerHTML, this.innerText
});
$('.total_price').html(sum);
/** Get Total QTY **/
var qty= 0;
$('.qty').each(function(){
    qty += parseFloat($(this).val());  // Or this.innerHTML, this.innerText
});
$('.total_qty').html(qty);


$('.delete').click(function(){
                       $(this).closest('tr').remove();
						$id_product = $(this).attr('data-id');
						$.ajax({
								url: "{{route('buys.destroy')}}",
								type: "Delete",
								data: {
									id_product: $id_product,
									_token: '{{csrf_token()}}'
								},

									success:function(response){
                                        $('.total_price').text(parseFloat($('.total_price').text()) - parseFloat(response.price));
                                        $('.total_qty').text(parseFloat($('.total_qty').text()) - parseFloat(response.qty));
									}
								});
								
								/** End Ajax Delete Roq **/
				});
      //////////////////////  script fro updte qty  ///////////
	  	  $(document).ready(function () {
          $('.qty').on('keyup', function () {
              var product_id = $(this).attr('data-id');
			  var qty = $(this).val();
			var total_qty = $('.total_qty').text();
			var price = $(this).closest('tr').find('.item_price').val();
			//var price = $(this).find('.item-item_price').val();

				if(qty != ''){
					$.ajax({
                  url: "{{route('update_qty_ajax')}}",
                  type: "POST",
                  data: {
                      product_id: product_id,
					  qty: qty,
					  total_qty:total_qty,
					  price:price,
                      _token: '{{csrf_token()}}'
                  },
				   context: this,
                 success:function(response){
					$(this).closest('tr').find('.price').text(response.total_price_item);
					//==============================================================================================
					var qty= 0;
					$('.qty').each(function(){
						qty += parseFloat($(this).val());  // Or this.innerHTML, this.innerText
					});
					$('.total_qty').html(qty);

					var sum = 0;
						$('.price').each(function(){
							sum += parseFloat($(this).text());  // Or this.innerHTML, this.innerText
						});
						$('.total_price').html(sum);
                  },
              });
				}
              

          });

		    $('.item_price').on('keyup', function () {
              var product_id = $(this).attr('data-id');
			  var price = $(this).val();

			 var qty =  $(this).closest('tr').find('.qty').val();

				if(price != ''){
					$.ajax({
                  url: "{{route('update_price_ajax')}}",
                  type: "POST",
                  data: {
                      product_id: product_id,
					  qty: qty,
					  price:price,
                      _token: '{{csrf_token()}}'
                  },
				   context: this,
                 success:function(response){
					$(this).closest('tr').find('.price').text(response.total_price_item);
					//==============================================================================================
					var qty= 0;
					$('.qty').each(function(){
						qty += parseFloat($(this).val());  // Or this.innerHTML, this.innerText
					});
					$('.total_qty').html(qty);

					var sum = 0;
						$('.price').each(function(){
							sum += parseFloat($(this).text());  // Or this.innerHTML, this.innerText
						});
						$('.total_price').html(sum);
                  },
              });
				}
              

          });

      });          

                
</script>