<form id="addForm" class="addForm" method="POST" enctype="multipart/form-data" action="{{route('import-ads-store')}}">
    {{csrf_field()}}
    <div class="row g-4">

        <div class="d-flex flex-column mb-7 fv-row col-sm-4">
            <!--begin::Label-->
            <label for="file" class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                <span class="required mr-1">File</span> <span class="red-star">*</span>
            </label>
            <!--end::Label-->
            <input id="file" required type="file" accept=".xlsx, .xls, .csv" class="form-control form-control-solid" placeholder=" " name="file"
                   value=""/>
        </div>






    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="addButton">Add</button>
        <button type="button" class="btn btn-secondary"
                data-dismiss="modal">Close</button>
    </div>

</form>