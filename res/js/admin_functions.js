$(document).ready(function() {
    $('.modal').modal();
    $('.collapsible').collapsible();
    $('select').select();
    $('#trigger-sidebar').on('click', function() {
        $('#sidebar-left').toggleClass('sidebar-show');
        $('#menu-icon').toggleClass('rot');
        $('#logout').toggleClass('sidebar-show');
        $('#box-content').toggleClass('box-content-mini');
        $('#footer').toggleClass('footer-mini');
    });
    $('#menu').on('click', function() {
        $('#menu-arrow-up').toggleClass('hide');
        $('#menu-arrow-down').toggleClass('hide');
    });
    $('#btn-logout').on('click', function() {
        logout();
    });
    $("form").on('submit', function(event) {
        event.preventDefault();
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

function show_grade(json_data) {
    if (json_data.status) {
        $('#grade').addClass('success');
        $('#grade').removeClass('failed');
    } else {
        $('#grade').addClass('failed');
        $('#grade').removeClass('success');
    }
    $('#grade').html(json_data.detail);
    $('#grade').animate({
        'height': '65',
        'line-height': '65px',
        'opacity': '1'
    }, 500);
    $('#grade').delay(1000).animate({
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

function valid_username_or_email(value, elem) {
    var url = "index.php?action=valid_username_or_email";
    var data = {
        usr_or_email: value
    };
    var success = function(result) {
        var json_data = $.parseJSON(result);
        if (json_data.status) {
            $('#valid-' + elem + '-true').removeClass('hidden');
            $('#valid-' + elem + '-false').addClass('hidden');
        } else {
            $('#valid-' + elem + '-false').removeClass('hidden');
            $('#valid-' + elem + '-true').addClass('hidden');
        }
    };
    $.get(url, data, success);
}


function select_teacher() {
    var url = "index.php?action=get_list_teachers";
    var success = function(result) {
        var json_data = $.parseJSON(result);
        var sl = $('select[name=teacher_id]');
        sl.empty();
        $.each(json_data, function(key, value) {
            sl.append('<option value="' + value.teacher_id + '">' + value.name + '</option>');
        });
        $('select').select();
    };
    $.get(url, success);
}

function select_subject_head() {
    var url = "index.php?action=get_list_subject_head";
    var success = function(result) {
        var json_data = $.parseJSON(result);
        var sl = $('select[name=subject_head_id]');
        sl.empty();
        $.each(json_data, function(key, value) {
            sl.append('<option value="' + value.subject_head_id + '">' + value.name + '</option>');
        });
        $('select').select();
    };
    $.get(url, success);
}

function select_grade(data_value=null) {
    var url = "index.php?action=get_list_grades";
    var success = function(result) {
        var json_data = $.parseJSON(result);
        var sl = $('select[name=grade_id]');
        sl.empty();
        if(sl.parents('.action.select_action')){
            sl.append('<option value="">Hãy Chọn Khối</option>');
        }
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

function select_status(data_value=null) {
    var url = "index.php?action=get_list_statuses";
    var success = function(result) {
        var json_data = $.parseJSON(result);
        var sl = $('select[name=status_id]');
        sl.empty();
        if(json_data.length > 0){
            $.each(json_data, function(key, value) {
                var selected = "";
                if(data_value && value.status_id == data_value) {
                    selected= 'selected';
                }
                sl.append('<option value="' + value.status_id + '" '+ selected +'>' + value.detail + '</option>');
            });
        }
        else {
            $('select[name=status_id]').closest('.input-field').remove();
        }
        $('select').select();
    };
    $.get(url, success);
}

function select_class(data) {
    var url = "index.php?action=get_list_classes";
    var success = function(result) {
        var json_data = $.parseJSON(result);
        var sl = $('select[name=class_id]');
        sl.empty();
        $.each(json_data, function(key, value) {
            sl.append('<option value="' + value.class_id + '">' + value.class_name + '</option>');
        });
        $('select').select();
    };
    $.get(url, success);
}

function valid_class_name(value) {
    var url = "index.php?action=valid_class_name";
    var data = {
        class_name: value
    };
    var success = function(result) {
        var json_data = $.parseJSON(result);
        if (json_data.status) {
            $('#valid-class-true').removeClass('hidden');
            $('#valid-class-false').addClass('hidden');
        } else {
            $('#valid-class-false').removeClass('hidden');
            $('#valid-class-true').addClass('hidden');
        }
    };
    $.get(url, data, success);
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
