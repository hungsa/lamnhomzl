$(document).ready(function() {
    $('select').select();
    $('.modal').modal();
    $('#trigger-sidebar').on('click', function() {
        $('#sidebar-left').toggleClass('sidebar-show');
        $('#menu-icon').toggleClass('rot');
        $('#logout').toggleClass('sidebar-show');
        $('#box-content').toggleClass('box-content-mini');
        $('#footer').toggleClass('footer-mini');
    });
    $('#btn-logout').on('click', function() {
        logout();
    });
    $("form").on('submit', function(event) {
        event.preventDefault();
    });
    $("form.form_test").on('submit', function(event) {
        event.preventDefault();
        submit_test(this.id);
        this.reset();
    });
});

function show_status(json_data) {
    if (json_data.status) {
        $('#status').addClass('success');
        $('#status').removeClass('failed');
    } else {
        $('#status').addClass('failed');
        $('#status').removeClass('success');
    }
    $('#status').html(json_data.status_value);
    $('#status').animate({
        'height': '65',
        'line-height': '65px',
        'opacity': '1'
    }, 500);
    $('#status').delay(1000).animate({
        'opacity': '0',
        'height': '0',
        'line-height': '0px'
    }, 500);
}

function logout() {
    var url = "index.php?action=logout";
    var data = {
        confirm: true
    };
    var success = function(result) {
        var json_data = $.parseJSON(result);
        show_status(json_data);
        if (json_data.status) {
            setTimeout(function() {
                window.location.replace("index.php");
            }, 1500);
        }
    };
    $.post(url, data, success);
}

function valid_email_on_profiles(data) {
    var new_email = $('#profiles-new-email').val();
    var current_email = $('#profiles-current-email').val();
    var url = "index.php?action=valid_email_on_profiles";
    var data1 = {
        new_email: new_email,
        current_email: current_email
    };
    var success = function(result) {
        var json_data = $.parseJSON(result);
        if (json_data.status) {
            $('#valid-email-true').removeClass('hidden');
            $('#valid-email-false').addClass('hidden');
        } else {
            $('#valid-email-false').removeClass('hidden');
            $('#valid-email-true').addClass('hidden');
        }
    };
    $.post(url, data1, success);
}

function submit_test(id) {
    $('#preload').removeClass('hidden');
    var data = $('#'+id).serialize();
    var url = "index.php?action=check_password";
    var success = function(result) {
        var json_data = $.parseJSON(result);
        show_status(json_data);
        if (json_data.status) {
            setTimeout(function() {
                location.reload();
            }, 1500);
        }
        $('#preload').addClass('hidden');
    };
    $.post(url, data, success);
}

function submit_practice(code) {
    $('#preload').removeClass('hidden');
   var data = {
    practice_code: code
    };
    var url = "index.php?action=get_practice";
    var success = function(result) {
        var json_data = $.parseJSON(result);
        show_status(json_data);
        if (json_data.status) {
            setTimeout(function() {
                location.reload();
            }, 1500);
        }
        $('#preload').addClass('hidden');
    };
    $.post(url, data, success);
}

function select_grade(data_value=null) {
    var url = "index.php?action=get_list_grades";
    var success = function(result) {
        var json_data = $.parseJSON(result);
        var sl = $('select[name=grade_id]');
        sl.empty();
        $.each(json_data, function(key, value) {
            var selected = '';
            if(data_value && value.grade_id == data_value) {
                selected = 'selected';
            }
            sl.append('<option value="' + value.grade_id + '" '+ selected +'>' + value.detail + '</option>');
        });
        $('select').select();
    };
    $.get(url, success);
}

function select_subject(data_value=null) {
    var url = "index.php?action=get_list_subjects";
    var success = function(result) {
        var json_data = $.parseJSON(result);
        var sl = $('select[name=subject_id]');
        sl.empty();
        if(sl.parents('.action.select_action')){
            sl.append('<option value="">Hãy Chọn Môn Học</option>');
        }
        $.each(json_data, function(key, value) {
            var selected = '';
            if(data_value && value.subject_id == data_value) {
                selected = 'selected';
            }
            sl.append('<option value="' + value.subject_id + '" '+ selected +'>' + value.subject_detail + '</option>');
        });
        $('select').select();
    };
    $.get(url, success);
}

function select_levels(data_value=null) {
    var url = "index.php?action=get_list_levels";
    var success = function(result) {
        var json_data = $.parseJSON(result);
        var sl = $('select[name=level_id]');
        
        sl.empty();
        $.each(json_data, function(key, value) {
            var selected = '';
            if(data_value && value.level_id == data_value) {
                selected = 'selected';
            }
            sl.append('<option value="' + value.level_id + '" '+ selected +'>' + value.level_detail + '</option>');
        });
        $('select').select();
    };
    $.get(url, success);
}