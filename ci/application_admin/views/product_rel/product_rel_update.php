<link href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" />
<style type="text/css">
    .gray_bg {background: #E8E8E8;width: 100%;padding: 15px 0;height: 100%;}
    ._result {display: none;}
    /*검색*/
    .n_search_wrap{width: 50%; height: auto; }
    .n_search_wrap.focus .search_outcome{display: inline-block;padding: 0 15px; width: 100%; }
    .n_search_wrap.focus .search_outcome img{width: 100px; }
    .n_search_wrap.focus .search_outcome.recently_pdt .sub_tit{padding: 10px 0 15px; float: left }
    .n_search_wrap.focus .search_outcome ul{font-size: 18px;background: #fff; width: 100%; border-radius: 3px; -webkit-box-shadow: 0 2px 3px rgba(0, 0, 0, 0.2);  box-shadow:0 2px 3px rgba(0, 0, 0, 0.2); display: inline-block;margin-bottom: 0!important;padding: 0;max-height: 350px;overflow-y: auto; }
    .n_search_wrap.focus .search_outcome ul > li{width: 100%;  border-bottom: 10px solid #f3f3f3; display: inline-block; padding: 10px;}
    .n_search_wrap.focus .search_outcome ul > li:last-child{border-bottom: none;}
    .n_search_wrap.focus .search_outcome ul > li .thumb{width: 100px;}
    .n_search_wrap.focus .search_outcome ul > li .thumb img{border-right: 3px;}
    .n_search_wrap.focus .search_outcome ul > li .info_txt{margin-left: 5px; width: 60%; min-width: calc(100% - 110px);}
    .n_search_wrap.focus .search_outcome ul > li .info_txt .pdt_name{font-size: 16px;}
    .n_search_wrap.focus .search_outcome ul > li .info_txt .pdt_name strong{color: #FF3C63;}
    .n_search_wrap.focus .search_outcome ul > li .info_txt .pdt_counting{font-size: 14px; margin-top: 10px;color: #333; }
    .n_search_wrap.focus .search_outcome ul > li .info_txt .pdt_counting em{color: #FF3C63 }

    .list-item .soldOut { background:rgba(0,0,0,0.5); position: absolute;width: 85%;height: calc( 100% - 63px )  ;z-index: 99;top: 25px; }
    .list-item .soldOut img {width: 85%}

</style>

<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">상품관리 > 연관상품 관리</h4>
    </div>

    <div class="row">

        <form name="pop_update_form" id="pop_update_form" method="post" action="/product_rel/update_proc">


            <input type="hidden" name="sort_type" value="" />
            <input type="hidden" name="sort" value="desc" />
            <input type="hidden" name="p_pnum" value="<?=$oProductInfo['p_num']?>" />

            <table class="table table-bordered">
                <colgroup>
                    <col width="20%">
                    <col width="">
                </colgroup>
                <tr>
                    <th class="active">상위상품명</th>
                    <td style="text-align: left!important;"><?=$oProductInfo['p_name']?></td>
                </tr>

                <tr>
                    <th class="active">연관상품 검색</th>
                    <td><input name="kwd" type="search" autocomplete="off"  class="form-control" placeholder="연관상품 검색" onkeydown="javascript:if(event.keyCode == 13) return false;"></td>
                </tr>

                <tr class="_result">
                    <th class="active">검색결과</th>
                    <td style="text-align: left!important;">

                        <div class="n_search_wrap focus">
                            <div class="n_search_pdtList gray_bg">
                                <div class="search_outcome">
                                    <ul></ul>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </td>
                </tr>

                <tr>
                    <th class="active">
                        연관상품
                        <hr/>
                        <a role="button" class="btn btn-info btn-xs sort-sales-price">매출순정렬</a>
                    </th>
                    <td>
                        <div class="mgt10 mgb10" id="detail_image_list" style="height: 480px;position: relative;">
                        <?
                        if(count($rel_list) > 0){
                            foreach ($rel_list as $key => $row) {
                                $p_rep_image_array = json_decode($row['p_rep_image'], true);
                                if($row['view_yn'] == 'N') continue

                                ?>
                                <div class="col-md-2 col-sm-12 col-xs-12 mgt10 text-center list-item" data-code="<?=$row['p_order_code']?>">
                                    <input type="hidden" name="p_num[]" value="<?=$row['p_num']?>" />
                                    <input type="hidden" name="p_order_code[]" value="<?=$row['p_order_code']?>" />
                                    <a href="#none" style="font-size:20px;"><i class="fa fa-sort"></i></a>
                                    <a style="">
                                        <img src="<?=IMG_HTTP.$p_rep_image_array[0]?>" style="width:100%;position: relative" alt="" />

                                        <?if($row['isAble'] == 'N'){?>
                                            <div class="soldOut">
                                                <img class="imgSoldOut" src="<?=IMG_HTTP?>/images/img_sold_out.png" alt="">
                                            </div>
                                        <?}?>
                                    </a>
                                    <p class="alert alert-warning" style="padding: 5px!important;width: 100%"><?=$row['p_name']?></p>
                                    <a href="#none" onclick="item_drop(this,'update','<?=$row['seq']?>');" class="btn btn-danger btn-xs">삭제</a>

                                </div>
                            <?  }
                        }?>

                        </div>

                    </td>
                </tr>

            </table>

            <div style="margin-bottom: 100px">
                <a role="button" href="<?echo $list_url; ?>" class="btn btn-default pull-left">뒤로가기</a>
                <button class="btn btn-primary goSubmit pull-right">수정하기</button>
            </div>
        </form>

    </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>var projects = eval("<?=addslashes(json_encode($aProductList))?>");</script>

<script type="text/javascript">

    var no_result_html  = '<li class="no_result" style="text-align: center">';
        no_result_html += ' <p style="margin-top: 10px;">검색결과가 없습니다.</p>';
        no_result_html += '</li>';

    var list_url = '<?=$list_url;?>';
    /**
     * 토스트 팝업 (웹용)
     * @param message
     * @returns {boolean}
     */
    function toast(message) {
        if( $('.taost').length > 0 )  return false;
        var $toast = $('<div class="toast ui-loader ui-overlay-shadow ui-body-e ui-corner-all">' + message + '</div>');
        $toast.stop();
        $toast.css({'display':'block','background':'rgba(90,90,90,0.9)','color':'#fff','border-radius':'20px','position':'fixed','padding':'7px','marginLeft':'0','height':'inherit','text-align':'center','width':'270px','left':($(window).width() - 284) / 2,'bottom':'60px','font-size':'17px','z-index':'9999'});
        var removeToast = function(){ $(this).remove(); };
        $toast.click(removeToast);
        $toast.appendTo('body').delay(1000);
        $toast.fadeOut(400, removeToast);
    }//end of toast()

    $(function(){

        $('input[name="kwd"]').on('keyup',function(e){ goSearch($(this).val()); }) // 검색텍스트 입력시

        $('form[name="pop_update_form"]').ajaxForm({
            type: 'post',
            dataType: 'json',
            beforeSubmit: function(formData, jqForm, options) {
            },
            success: function(result) {
                if(result.msg) alert(result.msg);
                if(result.success) location.replace(list_url);
            },
            complete: function() {
            }
        });//end of ajax_form()

        set_dragable();
        position_reset();


    });

    function item_drop(obj,type,seq=''){

        var cf = confirm('해당 연관상품을 삭제하시겠습니까?');

        if(cf ==true){

            if(type == 'update'){ //실제 데이터 변경

                $.ajax({
                    url: '/product_rel/item_drop',
                    data: {seq:seq},
                    type: 'post',
                    dataType: 'json',
                    success: function (result) {
                        if(result.msg) alert(result.msg);
                        $(obj).parent().remove();
                        position_reset();
                    }

                });

            }else{

                $(obj).parent().remove();
                position_reset();

            }

        }

    }

    function goSearch(v){

        if(v == ''){
            $('._result').hide();
            return false;
        }

        var html    = '';

        for(var i = 0 ; i < projects.length ; i++){ var row = projects[i];

            var label_toLowerCase   = row['label'].toLowerCase();
            var v_toLowerCase       = v.toLowerCase();

            var hash_toLowerCase = '';
            if(empty(row['hash']) == false) hash_toLowerCase = row['hash'].toLowerCase();

            var hash_arr            = hash_toLowerCase.split(',');
            var in_ok               = false;
            var hash_str            = '';

            $.each(hash_arr, function(index,val){
                if(val.indexOf(v_toLowerCase) > -1) {
                    hash_str   += '#'+hash_arr[index]+' ';
                    in_ok       = true;
                }
            });


            if(label_toLowerCase.indexOf(v_toLowerCase) > -1 || in_ok == true){
                var replace_label = row['label'].replace(v , '<strong>'+v+'</strong>');



                var display_str = '<span class="badge badge-info">판매중</span>';
                var stock_str   = '<span class="badge badge-info">재고있음</span>';
                var sale_str    = '<span class="badge badge-info">진열중</span>';

                if(row.p_stock_state == 'N') display_str = '<span class="badge badge-danger">진열안함</span>';
                if(row.p_display_state == 'N') sale_str = '<span class="badge badge-danger">판매안함</span>';
                if(row.p_stock_state == 'N') stock_str = '<span class="badge badge-danger">재고없음</span>';


                html += '<li class="select-pdt" onclick="select_pdt('+i+');" role="button" >';
                html += '       <div class="thumb pull-left"><img src="'+row['p_today_image'][0]+'"></div>';
                html += '       <div class="info_txt pull-left">';
                html += '           <span class="pdt_name">'+replace_label+'</span>';
                html += '           <div class="pdt_counting">';
                if(row['p_tot_order_count'] > 0) html += '<span class="cnt">구매 <em>'+row['p_tot_order_count_str']+'</em></span>&nbsp;';
                if(row['p_review_count'] > 0)    html += '<span class="cnt">리뷰 <em>'+row['p_review_count_str']+'</em></span>';
                html += '           </div>';
                html += '           <div class="p_state">'+display_str +'&nbsp;'+ sale_str +'&nbsp;'+ stock_str +'</div>';
                html += '       </div>';
                html += '</li>';
            }

        }

        if(html != ''){
            $('.search_outcome ul').html(html);
        }else{
            if( $('.search_outcome ul').html() != no_result_html ) $('.search_outcome ul').html(no_result_html);
        }

        $('._result').show();

    }

    var max_pdt = 12;
    function select_pdt(key){

        if($('.list-item').length >= max_pdt){
            alert('한 상품의 연관상품은 최대 '+max_pdt+'개가 등록이 가능합니다.');
            return false;
        }

        var p_row = projects[key];
        var p_rep_image_array = p_row.p_today_image;
        var isOk = true;

        $('.list-item input[name="p_num[]"]').each(function(k,r){
            if( $(this).val() == p_row.value ) isOk = false;
        });

        if(isOk == false){
            alert('이미 같은 상품이 등록되어 있습니다.');
            return false;
        }

        var html  = '<div class="col-md-2 col-sm-12 col-xs-12 mgt10 text-center list-item" data-code="'+p_row.p_order_code+'">';
            html += '   <input type="hidden" name="p_num[]" value="'+p_row.value+'" />';
            html += '   <input type="hidden" name="p_order_code[]" value="'+p_row.p_order_code+'" />';
            html += '   <a href="#none" style="font-size:20px;"><i class="fa fa-sort"></i></a>';
            html += '   <a style="">';
            html += '       <img src="'+p_rep_image_array[0]+'" style="width:100%;" alt="" />';
            html += '   </a>';
            html += '   <p class="alert alert-warning" style="padding: 5px!important;width: 100%">'+p_row.label+'</p>';
            html += '   <a href="#none" onclick="item_drop(this,\'insert\');" class="btn btn-danger btn-xs">삭제</a>';
            html += '</div>';

        $('#detail_image_list').append(html);

        set_dragable();
        position_reset();

        toast('추가완료');

    }

    function set_dragable(){

        $('#detail_image_list').sortable({
            revert: true,
            helper: "clone",
            stop : function(e, ui){
            }
        });
        $('#detail_image_list').disableSelection();

    }

    function position_reset(){

        <?/**
     * @date 170809
     * @writer 황기석
     * @desc 반드시 sortable 함수 아래 있을것 / sortable 최장이미지의 세로사이즈를 모든 item에 적용
     */ ?>
        var max_img_height = 0;
        $('.list-item').each(function(){
            var tmp_height = $(this).height();
            if(max_img_height < tmp_height){
                max_img_height = tmp_height;
            }
        }).promise().done(function () {
            $(this).css('min-height',parseInt(max_img_height,10)+'px');
        });
        <?/* END */?>

    }

</script>