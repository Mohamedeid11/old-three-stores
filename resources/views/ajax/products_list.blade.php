@foreach ($main_categories as $cat)
    <optgroup label="{{$cat->title}}">
        @foreach ($cat->sub_cats() as $scat)
            @foreach($scat->products() as $product)
                @if(in_array($product->id, $selected_products))
                    <option value="{{$product->id}}" @if(in_array($product->id, $selected_products_in)) selected @endif>{{$product->title}}</option>
                @endif
            @endforeach
        @endforeach
    </optgroup>
@endforeach