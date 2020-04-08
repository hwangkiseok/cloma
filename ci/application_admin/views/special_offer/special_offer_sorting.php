<link href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" />
<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">상품관리 &gt; 특가전 순서 변경</h4>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">

                    <form name="main_form" id="main_form" method="post" class="form-horizontal" role="form" action="/special_offer/set_sorting">

                        <div class="form-group form-group-sm">
                            <h5 class="col-sm-12">특가전 순서 변경</h5>
                            <div class="col-sm-12">
                                <div class="mgt10 mgb10" id="detail_image_list" style="border:1px solid #ccc;border-radius:4px;padding:10px;width:100%;overflow:auto;min-height: 300px;">

                                    <?
                                    if(count($special_offer_lists) > 0){
                                        foreach ($special_offer_lists as $key => $row) {
                                    ?>
                                        <div class="col-md-2 col-sm-12 col-xs-12 mgt10 text-center list-item">
                                            <input type="hidden" name="seq[]" value="<?=$row['seq']?>" />
                                            <a href="#none" style="font-size:20px;"><i class="fa fa-sort"></i></a>
                                            <img src="<?=$row['banner_img']?>" style="width:100%;" alt="" />
                                            <p class="alert alert-warning" style="padding: 5px!important;width: 100%"><?=$row['thema_name']?></p>
                                        </div>

                                    <?  }
                                    }?>

                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="form-group form-group-sm">
                            <div class="col-sm-offset-2 col-sm-10 col-xs-12 text-right">
                                <a href="/special_offer/lists" id="btn_list" class="btn btn-default btn-sm mgr5">목록보기</a>
                                <button type="submit" class="btn btn-primary btn-sm">변경</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

    $(document).ready(function() {

        $('#detail_image_list').sortable({
            revert: true,
            helper: "clone",
            stop: function (e, ui) {
            }
        });
        $('#detail_image_list').disableSelection();

        <?/**
     * @date 170809
     * @writer 황기석
     * @desc 반드시 sortable 함수 아래 있을것 / sortable 최장이미지의 세로사이즈를 모든 item에 적용
     */ ?>
        var max_img_height = 0;
        $('.list-item').each(function () {
            var tmp_height = $(this).height();
            if (max_img_height < tmp_height) {
                max_img_height = tmp_height;
            }
        }).promise().done(function () {
            $(this).css('min-height', parseInt(max_img_height, 10) + 'px');
        });
        <?/* END */?>


        $('#main_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            success: function (result) {

                if (result.status == '000') { // 성공시
                    alert(result.message);
                    location.replace('/special_offer/lists/');
                } else {
                    var msg = '';
                    for (var i in result.error_data) {
                        var row = result.error_data[i];
                        msg += row + '\n';
                    }
                    alert(msg);
                    return false;
                }

            }
        });
    });

</script>



<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    /*!
 * jQuery UI Touch Punch 0.2.3
 *
 * Copyright 2011–2014, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */
    <?
    /**
     * @date 190109
     * @modify 황기석
     * @desc 핸드폰으로 터치드래그가 안되는 문제로 아래 코드 추가
     */
    ?>
    !function(a){function f(a,b){if(!(a.originalEvent.touches.length>1)){a.preventDefault();var c=a.originalEvent.changedTouches[0],d=document.createEvent("MouseEvents");d.initMouseEvent(b,!0,!0,window,1,c.screenX,c.screenY,c.clientX,c.clientY,!1,!1,!1,!1,0,null),a.target.dispatchEvent(d)}}if(a.support.touch="ontouchend"in document,a.support.touch){var e,b=a.ui.mouse.prototype,c=b._mouseInit,d=b._mouseDestroy;b._touchStart=function(a){var b=this;!e&&b._mouseCapture(a.originalEvent.changedTouches[0])&&(e=!0,b._touchMoved=!1,f(a,"mouseover"),f(a,"mousemove"),f(a,"mousedown"))},b._touchMove=function(a){e&&(this._touchMoved=!0,f(a,"mousemove"))},b._touchEnd=function(a){e&&(f(a,"mouseup"),f(a,"mouseout"),this._touchMoved||f(a,"click"),e=!1)},b._mouseInit=function(){var b=this;b.element.bind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),c.call(b)},b._mouseDestroy=function(){var b=this;b.element.unbind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),d.call(b)}}}(jQuery);
</script>