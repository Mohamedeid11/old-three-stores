<div class="form-group">
    <label>Address</label>
    <input class="form-control" type="text"  placeholder="Address" value="@if($find_client !== NULL){{$find_client->address}}@endif" id="address" name="address" />
</div>

<div class="form-group">
    <label>City</label>
    <select class="form-control" name="city" id="client_city_selector" shipping-url="{{ url('shipping_price_info') }}">
        <option value="" disabled selected>Choose City</option>
        @foreach ($cities as $city)
            <option value="{{$city->id}}" @if($find_client !== NULL)  @if($find_client->city == $city->id) selected @endif @endif>{{ $city->title }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Shipping Price (EGP)</label>
    <input class="form-control" type="text"  placeholder="Shipping Price" id="order_ship_price" name="ship_price" value="@if($find_client !== NULL){{$find_client->city_info->shipment }}@endif" />
</div>

<div class="form-group">
    <label>Location</label>
    <input class="form-control" type="text"  placeholder="Order Location" id="order_location" name="location"  value="@if($find_client !== NULL){{$find_client->location}}@endif" />
</div>
