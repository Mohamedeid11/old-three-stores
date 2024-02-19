<div id="chat_widgets_bar">

<div id="chat_groups_widget" class="chat_widget">
<div class="chat_widget_container">
<div id="widget_title" class="chat_widget_title" group="-">
<a href="Javascript:void(0)">Chat Groups</a>
</div>
<div id="widget_body" class="chat_widget_body">
@foreach (all_chat_groups() as $group)
<p><a href="Javascript:void(0);" class="chat_widget_group_viewer" group="{{$group->id}}">{{$group->title}}</a></p>
@endforeach
<div id="chat_notification_sound"></div>
</div>
</div>
</div>

@foreach (all_chat_groups() as $group)
<div id="chat_groups_widget_{{$group->id}}" class="chat_widget hide">
<div class="chat_widget_container">
<div id="widget_title_{{$group->id}}" class="chat_widget_title chat_admin_widget_title" group="{{$group->id}}">
<a href="Javascript:void(0);">{{$group->title}}</a>

<a href="Javascript:void(0);" class="hide_chat_widget" group="{{$group->id}}"><i class="fas fa-times"></i></a>
<a href="Javascript:void(0);" class="search_chat_widget" group="{{$group->id}}"><i class="fas fa-search"></i></a>
</div>
<div id="widget_body_{{$group->id}}" class="chat_widget_body">
<form class="kt-form kt-form--label-right chat_search_form hide" method="post" action="{{url('admin/chat_search')}}" group="{{$group->id}}">
{{csrf_field()}}
<input type="hidden" name="group" value="{{$group->id}}" />
<div class="form-group">
<div class="input-group">
<input type="text" name="search" class="form-control" placeholder="Search for...">
<div class="input-group-append">
<button class="btn btn-dark" type="submit"><i class="fas fa-search"></i></button>
</div>
</div>
</div>
</form>

<div class="chat_search_msgs hide">

</div>
<div class="chat_msgs">
@foreach (admin_chat_seen_msgs($group->id) as $msg)
    @if($msg->sender == Auth::guard('admin')->user()->id && $msg->sender_type == 1)
    <div class="outgoing_msg"><div class="sent_msg">
    @if($msg->content != '') <p>{{$msg->content}}</p> @endif
    @if($msg->file_name != '') <p><a href="{{asset($msg->file)}}" download><i class="far fa-arrow-alt-circle-down"></i> {{$msg->file_name}}</a></p> @endif
    <span class="time_date"> {{date('l, Y-m-d h:i A', admin_zone_time(strtotime($msg->created_at)))}} </span> </div>
    </div>
    @else
    <div class="incoming_msg"><p class="incoming_msg_user">@if($msg->sender_type == 0) {{$msg->sender_info->name}} @else {{$msg->admin_info->name}} @endif</p><div class="received_withd_msg">
    @if($msg->content != '') <p>{{$msg->content}}</p> @endif
    @if($msg->file_name != '') <p><a href="{{asset($msg->file)}}" download><i class="far fa-arrow-alt-circle-down"></i> {{$msg->file_name}}</a></p> @endif
    <span class="time_date"> {{date('l, Y-m-d h:i A', admin_zone_time(strtotime($msg->created_at)))}}</span> </div>
    </div>
    @endif
@endforeach
</div>
<div class="chat_widget_form">
<form class="type_msg form-horizontal chat_msgs_form" method="post" action="{{url('admin/chat_send')}}" id="chat_msgs_form_{{$group->id}}" enctype="multipart/form-data" group="{{$group->id}}">
<div class="input_msg_write">
{{csrf_field()}}
<input type="hidden" name="reciver" value="{{$group->id}}"> 
<textarea class="form-control" placeholder="Type a message" name="message"></textarea>
<div class="chat_widget_actions">
<label><i class="fas fa-paperclip"></i><input type="file" name="file"></label>
<button class="msg_send_btn" type="submit"><i class="fas fa-paper-plane"></i></button>
</div>
<div id="chat_widget_actions_notifications_{{$group->id}}" class="chat_widget_actions_notifications"></div>
</div>
</form>
</div>
</div>

</div>
</div>

<script>
var pusher = new Pusher('665c9c0c50eef30bd237', {
  cluster: 'eu',
  encrypted: true
});
var channel = pusher.subscribe('chat_admin_{{$group->id}}_{{Auth::guard('admin')->user()->id}}');
// bind the server event to get the response data and append it to the message div
channel.bind('client_event', function(data) {
  $('#widget_body_{{$group->id}} .chat_msgs').append(data);
  $('#chat_groups_widget_{{$group->id}}').removeClass('hide');
  $('#chat_groups_widget_{{$group->id}}').addClass('active');
  $("#widget_body_{{$group->id}} .chat_msgs").animate({ scrollTop: $('#widget_body_{{$group->id}} .chat_msgs').prop("scrollHeight")}, 1000);

  $('.unseen_msgs').each(function () {
        var msg= $(this).attr('msg');
        if(msg != '-')
        {
            $.ajax({
		type: "POST",
		url: "{{url('admin/seen_msg')}}",
		data: {msg: msg},
		success:function(data) {
			$(this).removeClass('unseen_msgs');
		}
	    });
        }
    });
});
channel.bind('client_event_sound', function(data) {playSound();});
</script>


@endforeach

</div>
