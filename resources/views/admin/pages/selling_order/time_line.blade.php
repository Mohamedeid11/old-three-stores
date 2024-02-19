<div class="container">
    <div class="row justify-content-center">
        <span class="h2">{{$order->order_number}}</span>
    </div>

    @foreach($order->time_lines as $line)
        <div class="row">
            <div class="col-md-3"><b>{{date('Y-m-d h:i A', strtotime($line->created_at))}}</b></div>
            <div class="col-md-9">{{$line->admin_info->name.$line->text}}</div>
        </div>
    @endforeach
</div>