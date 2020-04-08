<link href="/plugins/datepicker/datepicker3.css?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/datepicker3.css"); ?>" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
<link href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" />

<style>
    .tag-result-area button:first-child{margin-left: 0px;}
    .tag-result-area button{margin-left: 10px;margin-top: 10px;}
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

</style>


<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">상품관리 &gt; 등록</h4>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">

                    <form name="main_form" enctype="multipart/form-data" id="main_form" method="post" class="form-horizontal" role="form" action="<?=$aInput['action_path']?>" onsubmit="return formChk();">

                        <input type="hidden" name="mode" value="<?=$aInput['mode']?>">
                        <input type="hidden" name="seq" value="<?=$aInput['seq']?>">

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">활성상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_activate_flag">
                                    <input type="radio" id="activate_flagY" name="activate_flag" value="Y" <?if($special_offer_row['activate_flag'] == 'Y'){?>checked<?}?>><label for="activate_flagY">활성</label>&nbsp;&nbsp;
                                    <input type="radio" id="activate_flagN" name="activate_flag" value="N" <?if($special_offer_row['activate_flag'] == 'N'){?>checked<?}?>><label for="activate_flagN">비활성</label>&nbsp;&nbsp;
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출기간 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_view_type">
                                    <input type="radio" id="view_typeA" name="view_type" value="A" <?if($special_offer_row['view_type'] == 'A'){?>checked<?}?>><label for="view_typeA">기간사용</label>&nbsp;&nbsp;
                                    <input type="radio" id="view_typeB" name="view_type" value="B" <?if($special_offer_row['view_type'] == 'B'){?>checked<?}?>><label for="view_typeB">상시</label>&nbsp;&nbsp;
                                </div>
                                <div id="field_view_date" style="display: <?if($special_offer_row['view_type'] == 'B'){?>none<?}?>;">
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" name="start_date" value="<?=$special_offer_row['start_date']?>" />
                                            <span class="input-group-btn text-left">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pull-left text-center" style="width:20px;">~</div>
                                    <div class="pull-left">
                                        <div class="input-group date" style="width:133px;">
                                            <input type="text" class="form-control" style="width:100px;" name="end_date" value="<?=$special_offer_row['end_date']?>" />
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">테마명</label>
                            <div class="col-sm-8">
                                <input type="text" id="thema_name" name="thema_name" class="form-control" value="<?=$special_offer_row['thema_name']?>" placeholder="테마명" >
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">이미지</label>
                            <div class="col-sm-8">
                                <div class="input-group" id="field_p_rep_image" style="width:100%;">
                                    <span class="input-group-addon" style="width:110px">배너이미지 <span class="txt-danger">*</span></span>
                                    <input type="file" name="banner_img" class="form-control" accept="image/*">
                                </div>
                                <?php if( $this->config->item('special_offer_banner_image_size') ) { ?>
                                    <p class="help-block">*
                                        <?php foreach ($this->config->item('special_offer_banner_image_size') as $item) { ?>
                                            <?php echo $item[0] . " x " . $item[1]; ?>
                                        <?php } ?>
                                        로 리사이징(썸네일 생성) 됩니다. (사이즈가 다른경우 이미지 짤림현상이 발생합니다.)
                                    </p>
                                <?php } ?>

                            </div>
                            <?if($special_offer_row['banner_img']){?>
                                <div class="mgt5 mgb10 col-sm-offset-2 col-sm-2">
                                    <a href="#none" onclick="new_win_open('<?=$special_offer_row['banner_img']?>', 'img_pop', 800, 600);" class="thumbnail" style="margin-bottom: 0px!important;">
                                        <img src="<?=$special_offer_row['banner_img']?>" data-type="rep_img" alt="">
                                    </a>
                                </div>
                            <?}?>
                        </div>


<!--                        <div class="form-group form-group-sm">-->
<!--                            <label class="col-sm-2 control-label">헤드 타이들</label>-->
<!--                            <div class="col-sm-8">-->
<!--                                <div class="pull-left">-->
<!--                                    <input type="text" id="head_title" name="head_title" class="form-control" value="--><?//=$special_offer_row['head_title']?><!--" placeholder="헤드 타이들">-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">헤드 타이틀 이미지</label>
                            <div class="col-sm-10">
                                <div class="input-group" id="field_p_rep_image" style="width:100%;">
                                    <span class="input-group-addon" style="width:110px">배너이미지 <span class="txt-danger">*</span></span>
                                    <input type="file" name="header_title_img" class="form-control" accept="image/*">
                                </div>
                            </div>
                            <?if($special_offer_row['header_title_img']){?>
                                <div class="mgt5 mgb10 col-sm-offset-2 col-sm-2">
                                    <a href="#none" onclick="new_win_open('<?=$special_offer_row['header_title_img']?>', 'img_pop', 800, 600);" class="thumbnail" style="margin-bottom: 0px!important;">
                                        <img src="<?=$special_offer_row['header_title_img']?>" data-type="header_title_img" alt="">
                                    </a>
                                </div>
                            <?}?>
                        </div>



                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">태그</label>
                            <div class="col-sm-8">
                                <div class="pull-left">
                                    <input type="text" id="tag_str" name="tag_str" class="form-control" value="" placeholder="태그" autocomplete="off">
                                </div>
                                <div class="pull-left" style="margin-left: 10px;">
                                    <button class="btn btn-primary btn-sm pull-left add-tags-btn">추가</button>
                                </div>
                                <div class="pull-left" style="margin-left: 10px;">
                                    <p class="alert alert-warning" style="padding: 5px 10px;margin: 0">수정 또는 등록시 변경됩니다.</p>
                                </div>
                                <div class="clearfix"></div>

                                <div class="tag-result-area">
                                    <?if(empty($special_offer_row['tag']) == false){ $tags_arr = explode(',',$special_offer_row['tag']);  ?>
                                        <? foreach ($tags_arr as $v) {?>
                                            <button class="btn btn-default add-tag db"><?=$v?><input type="hidden" name="tag_arr[]" value="<?=$v?>" /></button>
                                        <? } ?>
                                    <?}?>
                                </div>
                            </div>
                        </div>
-->
                        <hr>

                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품리스트 접기 - 설정 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_fold_flag">
                                    <input type="radio" id="fold_flag1" name="fold_flag" value="Y" <?if($special_offer_row['fold_flag'] == 'Y'){?>checked<?}?>><label for="fold_flag1">사용</label>&nbsp;&nbsp;
                                    <input type="radio" id="fold_flag2" name="fold_flag" value="N" <?if($special_offer_row['fold_flag'] == 'N'){?>checked<?}?>><label for="fold_flag2">사용안함</label>&nbsp;&nbsp;
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm use_folded" style="display: <?=$special_offer_row['fold_flag']=='N'?'none':''?>;">
                            <label class="col-sm-2 control-label">상품리스트 접기 - 기본 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_folded">
                                    <input type="radio" id="folded1" name="folded" value="Y" <?if($special_offer_row['folded'] == 'Y'){?>checked<?}?>><label for="folded1">접기</label>&nbsp;&nbsp;
                                    <input type="radio" id="folded2" name="folded" value="N" <?if($special_offer_row['folded'] == 'N'){?>checked<?}?>><label for="folded2">펼치기</label>&nbsp;&nbsp;
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품정렬 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <div id="field_use_class">

                                    <input type="radio" id="use_class2" name="use_class" value="pdt_listA" <?if($special_offer_row['use_class'] == 'pdt_listA'){?>checked<?}?>><label for="use_class2">1개 상품 리스트</label>&nbsp;&nbsp;
                                    <input type="radio" id="use_class2" name="use_class" value="pdt_listB" <?if($special_offer_row['use_class'] == 'pdt_listB'){?>checked<?}?>><label for="use_class2">3개 상품 리스트</label>&nbsp;&nbsp;

                                </div>
                            </div>
                        </div>
                        -->



                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">
                                <input name="kwd" type="search" autocomplete="off"  class="form-control" placeholder="상품검색" onkeydown="javascript:if(event.keyCode == 13) return false;">
                            </div>
                        </div>


                        <div class="form-group form-group-sm _result">

                            <label class="col-sm-2 control-label">검색결과</label>
                            <div class="col-sm-8">

                                <div class="n_search_wrap focus">
                                    <div class="n_search_pdtList gray_bg">
                                        <div class="search_outcome">
                                            <ul></ul>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div class="clear"></div>


                            </div>

                        </div>
                        <!--
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품 <span class="txt-danger">*</span></label>
                            <div class="col-sm-8">

                                <select name="product_lists">
                                    <option value="">* 선택 *</option>
                                    <? foreach ($select_product_lists as $row) { $p_image = $row['p_rep_image_array'];?>
                                        <option value="<?=$row['p_num']?>"><?=$row['p_name']?></option>
                                    <? } ?>
                                </select>

                            </div>
                        </div>
                        -->


                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-10">
                                <div class="mgt10 mgb10" id="detail_image_list" style="border:1px solid #ccc;border-radius:4px;padding:10px;width:100%;overflow:auto;min-height: 300px;">

                                    <?
                                    if(count($special_offer_product_lists) > 0){
                                        foreach ($special_offer_product_lists as $key => $row) {
                                            $p_rep_image_array = json_decode($row['p_rep_image'], true);
                                    ?>
                                        <div class="col-md-2 col-sm-12 col-xs-12 mgt10 text-center list-item" data-code="<?=$row['p_order_code']?>">
                                            <input type="hidden" name="p_num[]" value="<?=$row['p_num']?>" />
                                            <input type="hidden" name="p_order_code[]" value="<?=$row['p_order_code']?>" />
                                            <a href="#none" style="font-size:20px;"><i class="fa fa-sort"></i></a>
                                                <img src="<?=$p_rep_image_array[0]?>" style="width:100%;" alt="" />
                                                <p class="alert alert-warning" style="padding: 5px!important;width: 100%"><?=$row['p_name']?></p>
                                            <a href="#none" onclick="item_drop(this);" class="btn btn-danger btn-xs">삭제</a>
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
                                <?if($aInput['mode'] == 'insert'){?>
                                    <button type="submit" class="btn btn-primary btn-sm">등록</button>
                                <?}else{?>
                                    <button type="submit" class="btn btn-primary btn-sm">수정</button>
                                <?}?>

                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" charset="utf-8"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="/plugins/datepicker/bootstrap-datepicker.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/bootstrap-datepicker.js"); ?>" charset="utf-8"></script>
<script src="/plugins/datepicker/locales/bootstrap-datepicker.kr.js?v=<?php echo filemtime($this->input->server("DOCUMENT_ROOT") . "/plugins/datepicker/locales/bootstrap-datepicker.kr.js"); ?>" charset="utf-8"></script>

<script>var projects = eval("<?=addslashes(json_encode($select_product_lists))?>");</script>

<script type="text/javascript">
    var no_result_html  = '<li class="no_result" style="text-align: center">';
        no_result_html += ' <p style="margin-top: 10px;">검색결과가 없습니다.</p>';
        no_result_html += '</li>';

    $(document).ready(function(){


        $('input[name="kwd"]').on('keyup',function(e){ goSearch($(this).val()); }) // 검색텍스트 입력시

        $('.add-tags-btn').on('click',function(e){
            e.preventDefault();
            var v = $('#tag_str').val();
            insert_tag_data(v);
        });
        $('#tag_str').on('keyup',function(e){
            e.preventDefault();

            if(e.keyCode == 13 && $(this).val() != ''){
                var v = $(this).val();
                insert_tag_data(v);
            }
        });

        $('input[name="bg_color"]').on('blur change',function(){
            $('.bg_color_test_area div').css('background-color',$(this).val());
        });

        $('#detail_image_list').sortable({
            revert: true,
            helper: "clone",
            stop : function(e, ui){
            }
        });
        $('#detail_image_list').disableSelection();

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


        $('#main_form').ajaxForm({
            type : 'post',
            dataType : 'json',
            success: function(result){

                if( result.status == '000'){ // 성공시
                    alert(result.message);
                    location.replace('/special_offer/lists/');
                }else{
                    var msg = '';
                    for(var i in result.error_data) {
                        var row = result.error_data[i];
                        msg += row+'\n';
                    }
                    alert(msg);
                    return false;
                }

            }
        });

        $('.input-group.date').datepicker({format: "yyyymmdd", language: "kr", autoclose: true});
        $('[name="product_lists"]').select2();

        $('input[name="view_type"]').on('change',function(){
            if( $(this).val() == 'B'){
                $('#field_view_date').hide();
            }else{
                $('#field_view_date').show();
            }
        });

        $('input[name="fold_flag"]').on('change',function(){
            if( $(this).val() == 'N'){
                $('.use_folded').hide();
            }else{
                $('.use_folded').show();
            }
        });


        //대표상품명 변경시
        $('[name="product_lists"]').on('select2:select', function(){

            var curr_val = $(this).val();
            var distinct_num = false;

            $('input[name="p_num[]"]').each(function(){
                if(curr_val == $(this).val()){
                    distinct_num = true ;
                }
            });

            if(distinct_num == true){
                alert('이미 같은 상품이 있습니다');
                return false;
            }

            $.ajax({
                url : '/special_offer/get_product_row',
                data : {p_num:$(this).val() },
                type : 'post',
                dataType : 'json',
                async : false,
                success : function(result){

                    var p_row = result.data;
                    var p_rep_image_array = p_row.p_rep_image_array;

                    var html  = '';
                        html += '<div class="col-md-2 col-sm-12 col-xs-12 mgt10 text-center list-item">';
                        html += '   <input type="hidden" name="p_num[]" value="'+p_row.p_num+'" />';
                        html += '   <input type="hidden" name="sort" value="" />';
                        html += '   <a href="#none" style="font-size:20px;"><i class="fa fa-sort"></i></a>';
                        html += '   <img src="'+p_rep_image_array[0]+'" style="width:100%;" alt="" />';
                        html += '   <p class="alert alert-warning" style="padding: 5px!important;width: 100%">'+p_row.p_name+'</p>';
                        html += '   <a href="#none" onclick="item_drop(this);" class="btn btn-danger btn-xs">삭제</a>';
                        html += '</div>';

                    $('#detail_image_list').append(html);
                    $('#detail_image_list').sortable({
                        revert: true,
                        helper: "clone",
                        stop : function(e, ui){
                        }
                    });
                    $('#detail_image_list').disableSelection();

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
            });


        });

    });
    function item_drop(obj){
        $(obj).parent().remove();

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
    function formChk(){

    }

    function insert_tag_data(v){
        var add_tags = '<button class="btn btn-default add-tag js">'+v+'<input type="hidden" name="tag_arr[]" value="'+v+'" /></button>';
        $('.tag-result-area').append(add_tags);
        $('#tag_str').val('');
    }

    $(document).on('click','.add-tag',function(e){
        e.preventDefault();
        var cf = confirm('선택태그를 삭제하시겠습니까?');
        if(cf) $(this).remove();

    })

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

                html += '<li class="select-pdt" onclick="select_pdt('+i+');" role="button" >';
                html += '       <div class="thumb pull-left"><img src="'+row['p_today_image'][0]+'"></div>';
                html += '           <div class="info_txt pull-left">';
                html += '               <span class="pdt_name">'+replace_label+'</span>';
                html += '               <div class="pdt_counting">';
                if(row['p_tot_order_count'] > 0) html += '<span class="cnt">구매 <em>'+row['p_tot_order_count_str']+'</em></span>&nbsp;';
                if(row['p_review_count'] > 0)    html += '<span class="cnt">리뷰 <em>'+row['p_review_count_str']+'</em></span>';
                html += '           </div>';
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

    function select_pdt(key){

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

