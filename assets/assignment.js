/**
 * Created by bowei on 2015/4/25.
 */


function resize_image(load_count) {
    if(load_count < 2) {
        return false;
    }
    $('.comp_image').each(function () {
        var maxWidth = 480;
        var maxHeight = 480;
        var ratio = 0; //缩放比例
        var width = $(this).width();
        var height = $(this).height();

        //检查图片是否超宽
        if (width > maxWidth) {
            ratio = maxWidth / width;
            $(this).css('width', maxWidth);
            height = height * ratio;
            $(this).css('height', height);
            width = maxWidth;
        }

        if (height > maxHeight) {
            ratio = maxHeight / height;
            $(this).css('height', maxHeight);
            width = width * ratio;
            $(this).css('width', width);
            height = maxHeight;
        }
        console.log('w,h=['+ width + ',' + height + ']');
    });
    return true;
}

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
            //$('#img_a_a').attr('data-zoom-image', jsonval.img_src1);
            //$('#img_a').attr('src', jsonval.img_src1);
            //$('#img_b').attr('data-zoom-image', jsonval.img_src2);
            //$('#img_b').attr('src', jsonval.img_src2);
            refreshZoomImage($('#img_a'), 1, jsonval.img_src1);
            refreshZoomImage($('#img_b'), 11, jsonval.img_src2);

            $('#curr_index').text(jsonval.prog_current);
            $('#total_index').text(jsonval.prog_total);
            var progress = jsonval.prog_current/jsonval.prog_total*100 + "%";
            $('#meter_span').css('width', progress);
            //Clear radio button
            $(".radio").each(function(){
                $(this).prop('checked', false);
            });
            start_time = new Date().getTime();
            break;
        default :
            alert('wtf');
    }

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

//function elevateZoomPic(refresh){
//    if(refresh){
//        // Remove old instance od EZ
//        $('.zoomContainer').remove();
//        $('#img_a').removeData('elevateZoom');
//        $('#img_b').removeData('elevateZoom');
//    }
//    var zoomConfig = {
//        scrollZoom : true,
//        zoomWindowPosition: 1,
//        zoomWindowWidth: 512,
//        zoomWindowHeight: 512
//    };
//    $('#img_a').elevateZoom(zoomConfig);
//    zoomConfig['zoomWindowPosition']=11;
//    $('#img_b').elevateZoom(zoomConfig);
//}

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