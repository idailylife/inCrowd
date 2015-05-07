/**
 * Created by bowei on 2015/4/25.
 */


function post_callback(data, ret_status){
    console.log('post_callback:' + ret_status);
    console.log(data);
    if(ret_status != 'success'){
        console.log('post_callback: Connection failed.');
    }
    var jsonval = jQuery.parseJSON(data);
    switch (jsonval.status){
        case 2:
            alert('err type 2');
            break;
        case 1:
            window.location.href='finish';
            break;
        case 0:
            refreshZoomImage($('#img_a'), 1, jsonval.img_src1);
            //$('#img_a').on('load',function () {
            switchLoadImg('a', false);
            //}); //Reset onload function
            refreshZoomImage($('#img_b'), 11, jsonval.img_src2);
            //$('#img_b').on('load',function () {
            switchLoadImg('b', false);
            //
            var q_type = jsonval.q_type;
            if(q_type == 0){
                $('#cmp_usability').css('display', 'none');
                $('#cmp_creativity').css('display', 'inline');
            }else if(q_type == 1){
                $('#cmp_usability').css('display', 'inline');
                $('#cmp_creativity').css('display', 'none');
            }else{
                alert('qtype error');
            }
            if(q_type != last_q_type){
                //Show hint when q_type changes
                console.log('q_type changed.');
                show_hint(q_type);
            }
            last_q_type = q_type;

            $('#curr_index').text(jsonval.prog_current);
            $('#total_index').text(jsonval.prog_total);
            var progress = jsonval.prog_current/jsonval.prog_total*100 + "%";
            $('#meter_span').css('width', progress);
            //Clear radio button
            $(".radio").each(function(){
                $(this).prop('checked', false);
            });
            start_time = new Date().getTime();

            //Set button status
            var max_cmp_size = jsonval.max_size;
            if((jsonval.prog_current == jsonval.prog_total)
                && (jsonval.prog_total < max_cmp_size)){
                switch_double_button(true);
            } else {
                switch_double_button(false);
            }

            set_image_margin();
            break;
        default :
            alert('wtf');
    }

}

function show_hint(q_type){
    if(q_type == 0){
        $('#q_type_span').text("创新性");
    } else {
        $('#q_type_span').text("实用性");
    }
    $('#hint_mask').show();
}

//检查表单填写完整性
function check_validity(){
    var val_c = $("input[name='creativity']:checked").val();
    var val_u = $("input[name='usability']:checked").val();
    if(val_c == undefined && val_u == undefined){
        return false;
    }
    return true;
}

function refreshZoomImage(img_obj, zoompos, img_src){
    var zoomConfig = {
        scrollZoom : true,
        zoomWindowPosition: zoompos,
        zoomWindowWidth: 512,
        zoomWindowHeight: 512
    };
    $('.zoomContainer').remove();
    img_obj.removeData('elevateZoom');
    img_obj.attr('src', img_src);
    img_obj.data('zoom-image', img_src);
    img_obj.elevateZoom(zoomConfig);
}

function init_zoom(){
    var zoomConfig = {
        scrollZoom : true,
        zoomWindowPosition: 1,
        zoomWindowWidth: 512,
        zoomWindowHeight: 512
    };
    $('#img_a').elevateZoom(zoomConfig);
    zoomConfig['zoomWindowPosition']=11;
    $('#img_b').elevateZoom(zoomConfig);
}

function switchLoadImg(index, value){
    //更改“加载中”图片的显示true/隐藏false
    //index='a'/'b'
    if(value == true){
        $('#load_img_'+index).show();
        $('#img_'+index).hide();
    } else {
        $('#load_img_'+index).hide();
        $('#img_'+index).show();
    }
}

function resetTimer(){
    $('#time').text('0');
}

function tick_and_show(){
    var time_passed = new Date().getTime() - start_time;
    time_passed = new Number(time_passed / 1000).toFixed(0);
    $('#time').text(time_passed);
}

function set_image_margin(){
    //such stupid solution...
    var container_h = $('.comp_image_container').height();
    $('.comp_image').each(function(){
        $(this).css('margin-top', (container_h - $(this).height())/2 + "px");
    });
}

function switch_double_button(on){
    if(on){
        $('#button_set').show();
        $('#div_next').hide();
    } else {
        $('#button_set').hide();
        $('#div_next').show();
    }
}

function post_to_server(id, expand){
    if(!check_validity()){
        $(id).text('请填写完整后重试');
        return;
    } else {
        $(id).text('继续');
    }
    var val_c = $("input[name='creativity']:checked").val();
    var val_u = $("input[name='usability']:checked").val();
    var duration = new Date().getTime() - start_time;
    var postData = {
        'creativity': val_c,
        'usability': val_u,
        'duration' : duration
    };
    if(expand){
        postData['expand'] = true;
    }
    window.console.log(postData);
    switchLoadImg('a', true);
    switchLoadImg('b', true);
    $.post("assignment", postData,
        post_callback
    );
}