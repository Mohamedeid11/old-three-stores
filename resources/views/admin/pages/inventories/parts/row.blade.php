
    <td>{{$row->id}}</td>
    <td>{{$row->product_info->title??''}}
        {{$row->color_info->title??''}}
        {{$row->size_info->title??''}}

    </td>
    <td>{{$row->sold}}</td>
    <td>{{$row->bought}}</td>
    <td>{{$row->bought-$row->sold}}</td>
    <td>{{$row->last_cost??0}}</td>
    <td>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                Action
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a  class="ruinedItem" data-id="{{$row->id}}"  >Ruined Items</a>
            </div>
        </div>

    </td>


