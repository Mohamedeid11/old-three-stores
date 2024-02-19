<table class="table table-striped">
    <tbody>
    @if($order->note != '')
        <tr>
            <td>{{date('Y-m-d h:i A', strtotime($order->created_at))}}</td>
            <td>{{$order->note}}</td>
        </tr>
    @endif
    @foreach ($order->notes as $note)
        <tr>
            <td>{{date('Y-m-d h:i A', strtotime($note->created_at))}}</td>
            <td>
                {{$note->note}}
                @foreach ($note->tags as $tag)
                    <span class="badge badge-{{$tag->tag_info->color}}">{{$tag->tag_info->title}}</span>
                @endforeach
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<form id="addFormNotes" class="addForm" method="POST" enctype="multipart/form-data"
      action="{{ url('selling_order_notes/'.$order->id) }}">
    {{csrf_field()}}
    <div class="row g-4">


        {{ csrf_field() }}
        <div id="ajsuform_yu"></div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-12">
            <label>Note</label>
            <textarea name="note" class="form-control"></textarea>
        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-6">
            <label>Rep </label>
            <select name="rep[]" multiple class="d-block form-control select2_input">
                @foreach ($repps as $sa)
                    <option value="{{$sa->id}}">{{$sa->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex flex-column mb-7 fv-row col-sm-6">
            <label>Tags </label>
            <select name="tag[]" multiple class="d-block form-control select2_input">
                @foreach ($tags as $sa)
                    <option value="{{$sa->id}}">{{$sa->title}}</option>
                @endforeach
            </select>
        </div>


    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="addButton">Update</button>
        <button type="button" class="btn btn-secondary"
                data-dismiss="modal">Close
        </button>
    </div>

</form>
<script>
    $('.select2_input').select2();

</script>