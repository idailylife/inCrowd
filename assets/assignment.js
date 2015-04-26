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

function foo(){
    console.log('blabla');
}

function post_callback(data, ret_status){
    console.log('post_callback:' + ret_status);
    console.log(data);
    var jsonval = jQuery.parseJSON(data);
    switch (jsonval.status){
        case 2:
            alert('err type 2');
            break;
        case 1:
            alert('status 1');
            break;
        case 0:
            $('#img_a_a').attr('href', jsonval.img_src1);
            $('#img_a').attr('src', jsonval.img_thumb1);
            $('#img_b_a').attr('href', jsonval.img_src2);
            $('#img_b').attr('src', jsonval.img_thumb2);
            $('#curr_index').text(jsonval.prog_current);
            $('#total_index').text(jsonval.prog_total);
            //Clear radio button
            $(".radio").each(function(){
                $(this).prop('checked', false);
            });
            break;
        default :
            alert('wtf');
    }

}

