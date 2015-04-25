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
    alert('blabla');
}