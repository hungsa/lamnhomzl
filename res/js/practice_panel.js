//practice

$(function() {
    get_list_practice();
    select_grade();
    select_subject();
    select_levels();
    list_unit();
    $('#add_practice_form').on('submit', function() {
        submit_add_practice($('#add_prackhtice_form').serializeArray());
    });
});

function get_list_practice() {
    $('#preload').removeClass('hidden');
    var url = "index.php?action=get_list_practice";
    var success = function(result) {
        var json_data = $.parseJSON(result);
        show_list_practice(json_data);
        select_grade();
        select_subject();
        select_levels();
        $('select').select();
        $('#preload').addClass('hidden');
    };
    $.get(url, success);
}

function show_list_practice(data) {
    var list = $('#list_practice');
    console.log(data);
    list.empty();
    for (var i = 0; i < data.length; i++) {
        var tr = $('<tr class="" id="practice-' + data[i] + '"></tr>');
        tr.append('<td class="">' + data[i].practice_code + '</td>');
        tr.append('<td class="">' + data[i].subject_detail + '</td>');
        tr.append('<td class="">' + data[i].grade + '</td>');
        tr.append('<td class="">' + data[i].level_id + ' câu hỏi, thời gian ' + data[i].time_to_do + ' phút' + '</td>');
        tr.append('<td class="">' + practice_detail_button(data[i])+ '<br />' + '</td>');
        list.append(tr);
    }
    $('#table_practice').DataTable( {
        "language": {
            "lengthMenu": "Hiển thị _MENU_",
            "zeroRecords": "Không tìm thấy",
            "info": "Hiển thị trang _PAGE_/_PAGES_",
            "infoEmpty": "Không có dữ liệu",
            "emptyTable": "Không có dữ liệu",
            "infoFiltered": "(tìm kiếm trong tất cả _MAX_ mục)",
            "sSearch": "Tìm kiếm",
            "paginate": {
                "first":      "Đầu",
                "last":       "Cuối",
                "next":       "Sau",
                "previous":   "Trước"
            },
        },
        "aoColumnDefs": [
        { "bSortable": false, "aTargets": [ 3 ] }, //hide sort icon on header of column 0, 5
        ]
    } );
    $("form").on('submit', function(event) {
        event.preventDefault();
    });
}

function practice_detail_button(data) {
    return btn = '<a class="waves-effect waves-light btn" style="margin-bottom: 7px;" href="index.php?action=show_practice_result&practice_code=' + data.practice_code + '">Chi Tiết</a>';
}

function submit_add_practice(data) {
    $('#preload').removeClass('hidden');
    var url = "index.php?action=check_add_practice";
    var success = function(result) {
        var json_data = $.parseJSON(result);
        $('#table_practice').DataTable().destroy();
        $('#add_practice_form')[0].reset();
        get_list_practice();
        $('select').select();
        show_status(json_data);
        if (json_data.status) {
            console.log(json_data.status);
            submit_practice(json_data.practice_code);
        }
    };
    $.post(url, data, success);
}

function list_unit() {
    $('#preload').removeClass('hidden');
    var grade_id = $('#grade_id').val();
    if(grade_id == null)
        grade_id = 1;
    var subject_id = $('#subject_id').val();
    if(subject_id == null)
        subject_id = 1;
    var data = {
        grade_id: grade_id,
        subject_id: subject_id
    }
    var div = $('#list_unit');
    var url = "index.php?action=get_list_units";
    var success = function(result) {
        div.empty();
        var json_data = $.parseJSON(result);
        if(json_data == "")
            div.append('<span class="title">Chưa có câu hỏi cho khối và môn đã chọn!</span>');
        else {
            for (var i = 0; i < json_data.length; i++) {
                var ip = '<div class="input-field">' +
                        '<label for="'+ json_data[i].unit +'">Chương '+ json_data[i].unit +' (tổng số '+ json_data[i].total +' câu)</label>' +
                        '<input class="unit" type="number" id="'+ json_data[i].unit +'" name="'+ json_data[i].unit +'" onchange="update_total()" max="'+ json_data[i].total +'" min="0">' +
                        '</div>';
                div.append(ip);
            }
        }
        $('#preload').addClass('hidden');
    };
    $.post(url, data, success);
}

function update_total() {
    var sum = 0;
    $('.unit').each(function () {
        if (parseInt(this.value) > parseInt(this.getAttribute("max")))
            {
                alert("Nhập quá số câu hỏi đang có, vui lòng kiểm tra lại");
                this.value = this.getAttribute("max");
                sum += parseInt(this.value);
            }
        else if (this.value != "")
            sum += parseInt(this.value);
        });
    $('#total_questions').val(sum);
}