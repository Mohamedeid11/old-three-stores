@if($find_client !== NULL)
    <input type="hidden" name="client" value="{{$find_client->id}}" />
@else
    <input type="hidden" name="client" value="0" />
@endif
<div class="form-group">
    <label>Name</label>
    <input class="form-control" type="text"  placeholder="Name" value="@if($find_client !== NULL){{$find_client->name}}@endif" id="name" name="name" />
</div>
<div class="form-group">
    <label>Phone No.</label>
    <input class="form-control" type="text"  placeholder="Phone No." value="@if($find_client !== NULL){{$find_client->phone}}@else{{$search}}@endif" id="phone" name="phone" />
</div>
<div class="form-group">
    <label>Phone 2 No.</label>
    <input class="form-control" type="text"  placeholder="Phone 2 No." value="@if($find_client !== NULL){{$find_client->phone_2}}@else{{$search}}@endif" id="phone_2" name="phone_2" />
</div>
<div class="form-group">
    <label>Email</label>
    <input class="form-control" type="text"  placeholder="Email" value="@if($find_client !== NULL){{$find_client->email}}@endif" id="email" name="email" />
</div>
