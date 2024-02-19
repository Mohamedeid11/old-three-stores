$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});



$('body').on('click', '.add_cancel_time', function(e) {
    e.preventDefault(); 
    var action = $(this).attr('url');
    var num = $(this).attr('num');
    $.ajax({
        type: 'POST',
        data: {},
        url: action,
        success: function(data) 
        {
            $('#teacher_times_'+num).append(data);
        }
    });    
    return false;
});

$('body').on('click', '#add_available_time', function(e) {
    e.preventDefault(); 
    var action = $(this).attr('url');
    $.ajax({
        type: 'POST',
        data: {},
        url: action,
        success: function(data) 
        {
            $('#teacher_times').append(data);
        }
    });    
    return false;
});

$('body').on('click', '.remove_available_time', function(e) {
    $(this).parent().parent().parent().remove();
    return false;
});

$('body').on('submit', '#available_times_form', function(e) {
    e.preventDefault(); 
    $('#available_times_results').empty();
    var action = $(this).attr('action');
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
                $('#available_times_results').html('<div class="alert alert-danger">'+value+'</div>');	
            });
        },
        success: function(data) 
        {
            if(data.success)
            {
                location.reload();
            }
            else
            {
                $('#available_times_results').html('<div class="alert alert-danger">'+data.errors+'</div>');	
            }
        }
    });
    return false;
});


$('body').on('click', '.notifcation_item', function(e) {
    e.preventDefault;
    var action = $(this).attr('action');
    var num = $(this).attr('num');
    var href = $(this).attr('href');
    $.ajax({
        type: 'POST',
        data: {'num': num},
        url: action,
        success: function(data) 
        {
            window.location = href;
        }
    });   
    return false;
});

$('body').on('click', '.move_to_question', function(e) {
    e.preventDefault;
    var num = $(this).attr('num');
    $('.qustion-box').removeClass('active');
    $('#question_box_'+num).addClass('active');
    var num_int = parseInt(num);
    $.each($('.move_to_question_box'), function(){            
       var box_num = parseInt($(this).attr('num'));
        if(box_num < num) {$(this).parent().removeClass('active').addClass('fill');}
        else if(box_num == num) {$(this).parent().addClass('active').removeClass('fill');}
        else {$(this).parent().removeClass('active').removeClass('fill');}
    });

    return false;
});

$('body').on('click', '.move_to_next_question', function(e) {
    e.preventDefault;
    var num = parseInt($(this).attr('num')) + 1;
    $('.qustion-box').removeClass('active');
    $('#question_box_'+num).addClass('active');
    var num_int = parseInt(num);
    $.each($('.move_to_question_box'), function(){            
       var box_num = parseInt($(this).attr('num'));
        if(box_num < num) {$(this).parent().removeClass('active').addClass('fill');}
        else if(box_num == num) {$(this).parent().addClass('active').removeClass('fill');}
        else {$(this).parent().removeClass('active').removeClass('fill');}
    });
    return false;
});


$('body').on('submit', '.cancel_session_form', function(e) {
    e.preventDefault(); 
    var num = $(this).attr('num');
    $('#cancel_session_res_'+num).empty();
    var action = $(this).attr('action');
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
                $('#cancel_session_res_'+num).html('<div class="alert alert-danger">'+value+'</div>');	
            });
        },
        success: function(data) 
        {
            if(data.success)
            {
                location.reload();	
            }
            else
            {
                $('#cancel_session_res_'+num).html('<div class="alert alert-danger">'+data.errors+'</div>');	
            }
        }
    });
    return false;
});


$('body').on('submit', '#exam_form', function(e) {
    e.preventDefault(); 
    $('#exam_form_res').empty();
    var action = $(this).attr('action');
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
                $('#exam_form_res').html('<div class="alert alert-danger">'+value+'</div>');	
            });
        },
        success: function(data) 
        {
            if(data.success)
            {
                location.reload();
            }
            else
            {
                $('#exam_form_res').html('<div class="alert alert-danger">'+data.errors+'</div>');	
            }
        }
    });
    return false;
});



$('body').on('click', '.delete_answer_choice', function(e) {
    $(this).parent().parent().parent().parent().remove();
    var question = $('#add_answer_choices').attr('question-id');
    var choices_number = 0;
    $.each($('.answer_'+question), function(){            
        $(this).val(choices_number);
        choices_number = choices_number + 1;
    });
});

$('body').on('click', '#add_answer_choices', function(e) {
    e.preventDefault(); 
    var question = $(this).attr('question-id');
    var action = $('#add_exam_question').attr('url')+"/answers/choices";
    
    var choices_number = $('.choice_input_'+question).length + 1;
    $.ajax({
        type: 'POST',
        data: {'question': question, 'choice_number': choices_number},
        url: action,
        success: function(data) 
        {
            $('#question_choices_'+question).append(data);
            var choices_number = 0;
            $.each($('.answer_'+question), function(){            
                $(this).val(choices_number);
                choices_number = choices_number + 1;
            });        
        }
    });    
    return false;
});


$('body').on('submit', '#add_exam_form', function(e) {
    e.preventDefault(); 
    $('#exam_form_res').empty();
    var action = $(this).attr('action');
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
                $('#exam_form_res').html('<div class="alert alert-danger">'+value+'</div>');	
            });
        },
        success: function(data) 
        {
            if(data.success)
            {
                $('#exam_form_res').html('<div class="alert alert-success">'+data.message+'</div>');	
            }
            else
            {
                $('#exam_form_res').html('<div class="alert alert-danger">'+data.errors+'</div>');	
            }
        }
    });
    return false;
});

$('body').on('change', '.question_type_selector', function(e) {
    var item = $(this);
    var action = $('#add_exam_question').attr('url')+"/answers";
    $.ajax({
        type: 'POST',
        data: {type: $(this).val(), 'question': item.parent().parent().parent().parent().attr('id')},
        url: action,
        success: function(data) 
        {
            item.parent().parent().parent().parent().children('.question_answer').html(data);
        }
    });    
    return false;
});

$('body').on('click', '.delete_question', function(e) {
    $(this).parent().parent().parent().parent().remove();
});
 

function uniqId() 
{
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < 10; i++ ) {
       result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    result = Math.round(new Date().getTime() + (Math.random() * 100))+'_'+result;
    return result;
}

$('body').on('click', '#add_exam_question', function(e) {
    var action = $(this).attr('url');
    $.ajax({
        type: 'POST',
        data: {'div_id': uniqId()},
        url: action,
        success: function(data) 
        {
            $('#exam_questions').append(data);
        }
    });    
    return false;
});

$('body').on('submit', '#session_report_form', function(e) {
    e.preventDefault(); 
    $('#session_report_form_results').empty();
    var action = $(this).attr('action');
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
                $('#session_report_form_results').html('<div class="alert alert-danger">'+value+'</div>');	
            });
        },
        success: function(data) 
        {
            if(data.success)
            {
                window.location.href = 'https://matrixacademy.net/alust/al-ustadth/public/';	
            }
            else
            {
                $('#session_report_form_results').html('<div class="alert alert-danger">'+data.errors+'</div>');	
            }
        }
    });
    return false;
});

