<h1>{{$product->title}}</h1>
<table cellpadding="5" cellspacing="0" border="2">
<tr>
	<th>Color</th>
	<th>Size</th>
	<th>Bought</th>
	<th>Sold</th>
	<th>Ruined</th>
	<th>Total</th>
	<th>Dashboard Total</th>
</tr>

@foreach ($inventory as $iis)
<tr>
	<td>@if($iis->color == 0) NONE @else {{$iis->color_info->title}} @endif</td>
	<td>@if($iis->size == 0) NONE @else {{$iis->size_info->title}} @endif</td>
	<th>{{$iis->bought}}</th>
	<th>{{$iis->sold}}</th>
	<th>{{$iis->ruined_qty()}}</th>
	<th>{{$iis->bought - ($iis->sold + $iis->ruined_qty())}}</th>
	<td>{{xoxoxoxoproduct_available_units($product->id, $iis->color, $iis->size)}}</td>
</tr>
@endforeach
</table>