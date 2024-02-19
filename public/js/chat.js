$( document ).ready(function() {

$('body').on('submit', '.chat_search_form', function(e) {
    e.preventDefault(); 
    var action = $(this).attr('action');
    var group = $(this).attr('group');
    $('#widget_body_'+group+' .chat_search_msgs').html('<i class="fas fa-spinner fa-pulse"></i>');
    $('#widget_body_'+group+' .chat_search_msgs').animate({ scrollTop: $('#widget_body_'+group+' .chat_search_msgs').prop("scrollHeight")}, 1000);	
    var formData = new FormData($(this)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        success: function(data) 
        {
            $('#widget_body_'+group+' .chat_search_msgs').html(data);
            $('#widget_body_'+group+' .chat_search_msgs').animate({ scrollTop: $('#widget_body_'+group+' .chat_search_msgs').prop("scrollHeight")}, 1000);
        }
    });
    return false;
});

$('body').on('submit', '.chat_msgs_form', function(e) {
    e.preventDefault(); 
    var action = $(this).attr('action');
    var group = $(this).attr('group');
    $('#chat_widget_actions_notifications_'+group).html('<i class="fas fa-spinner fa-pulse"></i>');
    $('#widget_body_'+group+' .chat_msgs').animate({ scrollTop: $('#widget_body_'+group+' .chat_msgs').prop("scrollHeight")}, 1000);	
    var formData = new FormData($(this)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value){
                $('#chat_widget_actions_notifications_'+group).html('<p>'+value+'</p>');	
            });
            $('#widget_body_'+group+' .chat_msgs').animate({ scrollTop: $('#widget_body_'+group+' .chat_msgs').prop("scrollHeight")}, 1000);
        },
        success: function(data) 
        {
            if(data.success)
            {
                $('#chat_widget_actions_notifications_'+group).html('');
                $('#chat_msgs_form_'+group+' textarea').val('');
                $('#chat_msgs_form_'+group+' input[type="file"]').val('');
            }
            else
            {
                $('#chat_widget_actions_notifications_'+group).html('<p>'+data.errors+'</p>');
                $('#widget_body_'+group+' .chat_msgs').animate({ scrollTop: $('#widget_body_'+group+' .chat_msgs').prop("scrollHeight")}, 1000);	
            }
        }
    });
    return false;
});

$('body').on('submit', '#chat_msgs_form', function(e) {
    e.preventDefault(); 
    var action = $(this).attr('action');
    var group = $(this).attr('group');
    var formData = new FormData($(this)[0]);
    $.ajax({
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        url: action,
        error: function(data) {
            jQuery.each(data.errors, function(key, value){
                $('#chat_widget_actions_notifications_'+group).html('<div class="alert alert-danger">'+value+'</div>');	
            });
        },
        success: function(data) 
        {
            
        }
    });
    $('.write_msg').val('');
    return false;
});


$('body').on('click', '.chat_widget_title a.hide_chat_widget', function(){
    var group = $(this).attr('group');
    $('#chat_groups_widget_'+group).addClass('hide');
    $('#chat_groups_widget_'+group).removeClass('active');
});

$('body').on('click', '.chat_widget_title a.search_chat_widget', function(){
    $(this).toggleClass('active');
    if($(this).hasClass('active')) {$(this).html('<i class="fas fa-comments"></i>');}
    else {$(this).html('<i class="fas fa-search"></i>');}
    var group = $(this).attr('group');
    $('#chat_groups_widget_'+group).removeClass('hide');
    $('#chat_groups_widget_'+group).addClass('active');
    $('#widget_body_'+group+' .chat_msgs').toggleClass('hide');
    $('#widget_body_'+group+' .chat_widget_form').toggleClass('hide');
    $('#widget_body_'+group+' .chat_search_form').toggleClass('hide');
    $('#widget_body_'+group+' .chat_search_msgs').toggleClass('hide');
    return false;
});

$('body').on('click', '.chat_widget_title', function(){
    if($(this).parent().parent().hasClass('hide') || $(this).hasClass('search_chat_widget'))
    {

    }
    else
    {
        $(this).parent().parent().toggleClass('active');
        var group = $(this).attr('group');
        if(group != '-')
        {
                $('#widget_body_'+group+' .chat_msgs').animate({ scrollTop: $('#widget_body_'+group+' .chat_msgs').prop("scrollHeight")}, 1000);
        }
    }
});

$('body').on('click', '.chat_widget_group_viewer', function(){
    var group = $(this).attr('group');
    $('#chat_groups_widget_'+group).removeClass('hide');
    $('#widget_body_'+group+' .chat_msgs').animate({ scrollTop: $('#widget_body_'+group+' .chat_msgs').prop("scrollHeight")}, 1000);
});

$('.chat_user_widget_title').each(function () {
    var group = $(this).attr('group');
    if(group != '-')
    {
        $.ajax({
		type: "POST",
		url: "https://matrixacademy.net/alust/al-ustadth/public/unseen_msgs",
		data: {group: group},
		success:function(data) {
			$('#widget_body_'+group+' .chat_msgs').append(data);
  			if(data != '')
  			{
    				$('#chat_groups_widget_'+group).removeClass('hide');
    				$('#chat_groups_widget_'+group).addClass('active');
  				$("#widget_body_"+group+" .chat_msgs").animate({ scrollTop: $('#widget_body_'+group+' .chat_msgs').prop("scrollHeight")}, 1000);
  			}
		}
	});
    }
});


$('.chat_admin_widget_title').each(function () {
    var group = $(this).attr('group');
    if(group != '-')
    {
        $.ajax({
		type: "POST",
		url: "https://matrixacademy.net/alust/al-ustadth/public/admin/unseen_msgs",
		data: {group: group},
		success:function(data) {
			$('#widget_body_'+group+' .chat_msgs').append(data);
  			if(data != '')
  			{
    				$('#chat_groups_widget_'+group).removeClass('hide');
    				$('#chat_groups_widget_'+group).addClass('active');
  				$("#widget_body_"+group+" .chat_msgs").animate({ scrollTop: $('#widget_body_'+group+' .chat_msgs').prop("scrollHeight")}, 1000);
  			}
		}
	});
    }
});


});


function playSound ()
{
      var mp3Source = '<source src="../public/sounds/msg_notification.mp3" type="audio/mpeg">';
      var embedSource = '<embed hidden="true" autostart="true" loop="false" src="../public/sounds/msg_notification.mp3">';
      $("#chat_notification_sound").html('<audio autoplay="autoplay">' + mp3Source + embedSource + '</audio>');
}

