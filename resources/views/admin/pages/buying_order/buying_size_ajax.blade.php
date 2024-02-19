@if($products_fetch->count() > 0 )
<select  class="form-control size" name="size_id">
<option value="00" >Select Size</option>
@foreach ($products_fetch  as $size)
<option value="{{ $size->size }}">{{ $size->size_info->title }}</option>
@endforeach
</select>	
@endif