<form id="addForm" class="addForm" method="POST" enctype="multipart/form-data" action="{{route('inventories.store')}}">
    {{csrf_field()}}
    <div class="row g-4">

        <input id="inventory_row_id" value="{{$inventory->id}}" type="hidden">

        <div class="d-flex flex-column mb-7 fv-row col-sm-6">
            <!--begin::Label-->
            <label for="qty" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">qty of Need To  Ruined</span> <span class="red-star">*</span>
            </label>
            <!--end::Label-->
            <input min="0" id="qty" required type="number" class="form-control form-control-solid" placeholder=" " name="qty"
                   value="0"/>
        </div>


        <div class="d-flex flex-column mb-7 fv-row col-sm-6">
            <!--begin::Label-->
            <label for="ruined" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">All Qty ruined</span>
            </label>
            <!--end::Label-->
            <input readonly  id="ruined" required type="number" class="form-control form-control-solid" placeholder=" "
                   value="{{$ruined_item->qty}}"/>
        </div>



        <input type="hidden" name="inventory_id" value="{{$inventory->id}}">


        <div class="m-4">
        @foreach($ruined_item->destroyer as $destroy)

            <ul>
                <li>
                    <span class="h5">{{$destroy->created_at}}</span> -{{$destroy->admin->name??''}}- ruined {{$destroy->qty}} from <span class="h5">{{$inventory->product_info->title??''}}{{$inventory->color_info->title??''}}{{$inventory->size_info->title??''}}</span>
                </li>
            </ul>

        @endforeach


        </div>



    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="addButton">Add</button>
        <button type="button" class="btn btn-secondary"
                data-dismiss="modal">Close</button>
    </div>

</form>
