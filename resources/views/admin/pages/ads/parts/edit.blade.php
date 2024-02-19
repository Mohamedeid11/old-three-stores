<form id="updateForm" class="updateForm" method="POST" enctype="multipart/form-data" action="{{route('ads.updated',$row->id)}}">
    {{csrf_field()}}
    <div class="row g-4">

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="date" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">Date</span> <span class="red-star">*</span>
            </label>
            <!--end::Label-->
            <input id="date" required type="date" class="form-control form-control-solid" placeholder=" " name="date"
                   value="{{$row->date}}"/>
        </div>



        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="ad_number" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">Ad Number</span> <span class="red-star">*</span>
            </label>
            <!--end::Label-->
            <input id="ad_number" required type="text" class="form-control form-control-solid" placeholder=" " name="ad_number"
                   value="{{$row->ad_number}}"/>
        </div>


        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="status_select" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">Status</span> <span class="red-star">*</span>
            </label>
            <!--end::Label-->
            <select class="form-control" name="status" id="status_select">
                <option @if($row->status==1) selected @endif value="1">Active</option>
                <option @if($row->status==0) selected @endif value="0">Not Active</option>

            </select>
        </div>

        <div class="d-flex flex-column mb-7 fv-row col-sm-6">
            <!--begin::Label-->
            <label for="result" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">Result	</span> <span class="red-star">*</span>
            </label>
            <!--end::Label-->
            <input min="0" step="any" id="result" required type="number" class="form-control form-control-solid" placeholder=" " name="result"
                   value="{{$row->result}}"/>
        </div>


        <div class="d-flex flex-column mb-7 fv-row col-sm-6">
            <!--begin::Label-->
            <label for="cost_per_result" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">Cost Per Result	</span> <span class="red-star">*</span>
            </label>
            <!--end::Label-->
            <input min="0" step="any" id="cost_per_result" required type="number" class="form-control form-control-solid" placeholder=" " name="cost_per_result"
                   value="{{$row->cost_per_result}}"/>
        </div>










        <div class="d-flex flex-column mb-7 fv-row col-sm-8">
            <!--begin::Label-->
            <label for="product_id" class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
                <span class="required mr-1">   Product</span>
            </label>
            <select id='product_id' multiple name="product_id[]" style='width: 100%;'>
                @foreach($row->products as $product)
                  <option selected  value="{{$product->id}}">{{$product->title}}</option>
                @endforeach
            </select>
        </div>




        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="platform_id_pop" class="d-flex align-items-center fs-6 fw-bold form-label mb-2 ">
                <span class="required mr-1">   Platforms</span>
            </label>
            <select id='platform_id_pop' multiple name="platform_id[]" style='width: 100%;'>

                @foreach(\App\AdPlatform::where('ad_id',$row->id)->get() as $pivot)
                    <option selected value="{{$pivot->platform_id}}">{{\App\OrderTag::find($pivot->platform_id)->title}}</option>

                @endforeach

            </select>
        </div>







    </div>





    <div class="modal-footer">
        <button type="submit" class="btn btn-success" id="editBtn">Update</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
</form>



<script>

    (function () {

        $("#product_id").select2({
            closeOnSelect: false,
            placeholder: 'Channel...',
            // width: '350px',
            allowClear: true,
            ajax: {
                url: '{{route('admin.getProducts')}}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    }
                },
                cache: true
            }
        });
    })();

</script>
<script>

    (function () {

        $("#platform_id_pop").select2({
            closeOnSelect: false,
            placeholder: 'Channel...',
            // width: '350px',
            allowClear: true,
            ajax: {
                url: '{{route('admin.getPlatforms')}}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    }
                },
                cache: true
            }
        });
    })();

</script>
