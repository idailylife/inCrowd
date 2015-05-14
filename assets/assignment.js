/**
 * Created by bowei on 2015/4/25.
 */
function post_callback(data, ret_status){
    preload_counter = 0;
    console.log('post_callback:' + ret_status);
    console.log(data);
    if(ret_status != 'success'){
        console.log('post_callback: 网络连接失败，请重试.');
    }
    var jsonval = jQuery.parseJSON(data);
    switch (jsonval.status){
        case 2:
            alert('err type 2');
            break;
        case 1:
            //window.location.href='finish';
            get_expand_status(jsonval.can_expand);
            break;
        case 0:
            refreshZoomImage($('#img_a'), jsonval.img_src1);
            refreshZoomImage($('#img_b'), jsonval.img_src2);

            $('#img_a').one('load', function(){
                console.log('img1 loaded.');
                switchLoadImg('a', false);
                set_image_margin();
                setZoomImage($(this), 1);

                preload_counter++;
                if(preload_counter < 2)
                    return;

                on_img_load(jsonval.next_img_src1, jsonval.next_img_src2);

            }).each(function() {
                if(this.complete) $(this).load();
            });

            $('#img_b').one('load', function(){
                console.log('img2 loaded.');
                switchLoadImg('b', false);
                set_image_margin();
                setZoomImage($(this), 11);
                preload_counter++;
                if(preload_counter < 2)
                    return;
                on_img_load(jsonval.next_img_src1, jsonval.next_img_src2);

            }).each(function() {
                if(this.complete) $(this).load();
            });

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
            //$(".radio").each(function(){
            //    $(this).prop('checked', false);
            //});
            $('input').iCheck('uncheck');
            
            start_time = new Date().getTime();

            //Set button status
            //var max_cmp_size = jsonval.max_size;
            //if((jsonval.prog_current == jsonval.prog_total)
            //    && jsonval.can_expand){
            //    switch_double_button(true);
            //} else {
            //  switch_double_button(false);
            //}

            break;
        default :
            alert('wtf');
    }

}

function on_img_load(img1, img2){
    if(!img1 ||
        !img2){
        return;
    }
    start_time = new Date().getTime();
    console.log('perload started.');
    pre_load_image([
        img1,
        img2
    ]);
}

function pre_load_image(arrayOfImages){
    $(arrayOfImages).each(function () {
        (new Image()).src = this;
    });
}

function show_hint(q_type){
    if(q_type == 0){
        $('#q_type_span').text("创新性");
    } else {
        $('#q_type_span').text("实用性");
    }
    $('#hint_container').show();
    $('#finish_hint_container').hide();
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

function refreshZoomImage(img_obj, img_src){
    $('.zoomContainer').remove();
    img_obj.removeData('elevateZoom');
    img_obj.attr('src', img_src);
    img_obj.data('zoom-image', img_src);
    //
}

function setZoomImage(img_obj, zoompos){
    //var zoomConfig = {
    //    scrollZoom : true,
    //    zoomWindowPosition: zoompos,
    //    zoomWindowWidth: 512,
    //    zoomWindowHeight: 512
    //};
    var zoomConfig = {
        zoomType	    : "lens",
        lensSize        : 300,
        scrollZoom      : true,
        //containLensZoom : true,
        borderColour    : '#fff',
        lensBorder      : 1 ,
        lensShape   : 'round'
    }
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

function post_to_server(id){
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

    window.console.log(postData);
    switchLoadImg('a', true);
    switchLoadImg('b', true);
    $.post("assignment", postData,
        post_callback
    );
}

function get_expand_status(can_expand){
    //到最后一题时，点击“继续”按钮将先检查答题状态
    $('#hint_container').hide();
    $('#finish_hint_container').show();
    $('#hint_mask').show();
    if(can_expand){
        $('#finish_hint').text('选择[再来一组]继续任务，或点击[完成]以填写支付信息.');
        $('#div_next_1').show();
        $('#div_next_2').show();
    } else {
        $('#finish_hint').text('请点击[完成]以填写支付信息.');
        $('#div_next_1').show();
        $('#div_next_2').hide();
    }

    //if(!check_validity()){
    //    $('#div_next').text('请填写完整后重试');
    //    return;
    //} else {
    //    $('#div_next').text('继续');
    //}
    //$('#hint_container').hide();
    //$('#finish_hint_container').show();
    //$('#hint_mask').show();
    //$('#finish_hint').text('请稍候...');
    ////Get expand status from server
    //$('#div_next_1').hide();
    //$('#div_next_2').hide();
    //$.get("assignment/can_expand", function(data, status){
    //    console.log(data);
    //    if(status != 'success'){
    //        alert('连接失败，请刷新页面重试. -' + status);
    //        return;
    //    }
    //    var jsonval = jQuery.parseJSON(data);
    //    switch (jsonval.status){
    //        case -2:
    //        case -1:
    //            alert(jsonval.msg);
    //            break;
    //        case 0:
    //            //Cannot expand
    //            $('#div_next_1').show();
    //            $('#div_next_2').hide();
    //            $('#finish_hint').text('请点击[完成]以填写支付信息.');
    //            break;
    //        case 1:
    //            //Can expand
    //            $('#div_next_1').show();
    //            $('#div_next_2').show();
    //            $('#finish_hint').text('选择[再来一组]继续任务，或点击[完成]以填写支付信息.');
    //            break;
    //        default : alert('wtf');
    //    }
    //});
}