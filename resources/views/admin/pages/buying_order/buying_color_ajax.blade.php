@if($products_fetch->count() > 0 )
<select  class="form-control color" name="color_id">
<option value="00" >Select Color</option>
@foreach ($products_fetch  as $color)
<option value="{{ $color->color }}">{{ $color->color_info->title }}</option>
@endforeach
</select>	
@endif