
<style>
    .list-group { max-height:280px; overflow-y:auto; }
    .list-group-item { padding:5px; }
    .list-group-item .row { margin:0; padding:5px; }
    .list-group-item .row p { margin:0; }
    #product_list_group .left { padding-right:5px; }
    #product_list_group .right { padding-left:5px; }


    #pop_product_select_list .list-item {
        /* remove these
         overflow-x: hidden;
         overflow-y: auto; */
    }

</style>


<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <form name="pop_form" id="pop_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->insert_proc; ?>">
                        <input type="hidden" name="pmd_product_num" value="" />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">카테고리 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_pmd_division">
                                    <?php echo get_input_radio('pmd_division', $this->config->item('product_md_division'), '1'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">상품검색 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div>
                                    <div class="input-group">
                                        <input type="text" name="kwd" class="form-control" placeholder="상품명을 입력하세요" />
                                        <span class="input-group-btn"><button type="button" class="btn btn-primary btn-sm" onclick="product_search();">검색</button></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr />


                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">검색된 상품 목록</label>
                            <div class="col-sm-10">

                                <div id="pop_product_list"></div>

                            </div>
                        </div>

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">선택된 상품 목록</label>
                            <div class="col-sm-10">
                                <div id="pop_product_select_list" class="row" style="border: 1px solid #ddd;"></div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    /**
     * 상품 검색
     */
    function product_search(url) {
        console.log('product_search');

        var kwd = $('#pop_form input[name="kwd"]').val();
        if( empty(kwd) ) {
            //alert('상품명을 입력하세요.');
            $('#pop_form input[name="kwd"]').focus();
            return false;
        }
        if( empty(url) ) {
            url = '/product/search_ajax/?page=1';
        }

        //prograssBar.show();
        //Pace.restart();
        loadingBar.show($('#pop_product_list'));

        $.ajax({
            //url : '/product/search_ajax',
            url : url,
            data : {kfd:'p_name', kwd:kwd},
            type : 'post',
            //dataType : 'html',
            dataType : 'json',
            success : function (result) {

                var html = get_product_list_html(result.data);
                $('#pop_product_list').html(html);

                loadingBar.hide();
            },
            error : function() {
                loadingBar.hide();
            }
        });
    }//end of product_search()

    function get_product_list_html(data) {

        if( empty(data) ) {
            return false;
        }

        var html = '';
        html += '<ul class="list-group">';

        $.each(data, function(index, item){

            var disp_text = '<span class="badge badge-primary">진열함</span>';
            var sale_text = '<span class="badge badge-primary">판매중</span>';
            var stock_text = '<span class="badge badge-primary">재고있음</span>';
            var ctgr = '';
            var price = '';
            if( item.p_display_state == 'N' ) {
                disp_text = '<span class="badge badge-danger">진열안함</span>';
            }
            if( item.p_sale_state == 'N' ) {
                sale_text = '<span class="badge badge-danger">판매종료</span>';
            }
            if( item.p_stock_state == 'N' ) {
                stock_text = '<span class="badge badge-danger">품절</span>';
            }

            if(empty(item.p_cate1) == false ) ctgr = '<span class="badge badge-primary">'+item.p_cate1+'</span>';
            if(empty(item.p_cate1) == false ) price = '<span class="badge badge-primary">'+item.p_sale_price.comma()+' 원</span>';


            html += '<li class="list-group-item">';
            html += '   <div class="row">';
            html += '       <div class="col-sm-4"><img style="margin-bottom: 0!important;" class="thumbnail" src="' + item.p_rep_image_array[1] + '" alt="" width="100%" /></div>';
            html += '       <div class="col-sm-6">';
            html += '           <p><b style="font-size: 16px;">' + item.p_name + '</b></p>';
            html += '           <p>' + disp_text + '&nbsp;' + sale_text + '&nbsp;'  + stock_text + '&nbsp;'  + ctgr + '&nbsp;'  + price + '</p>';
            html += '           <p style="margin-top: 10px"><button type="button" class="btn btn-warning btn-block" onclick="product_select2(\'' + item.p_num + '\', \'' + item.p_name + '\', \'' + item.p_rep_image_array[1] + '\');">선택</button></p>';
            html += '       </div>';
            html += '   </div>';
            html += '</li>';
        });

        html += '</ul>';

        return html;
    }//end of product_list_print()

    /**
     * 상품 선택
     */
    function product_select(p_num, p_name, p_img) {
        console.log('product_select', p_num, p_name, p_img);
        info_message_all_clear();

        if( empty(p_num) ) {
            alert('잘못된 접근입니다.');
            return false;
        }

        //중복체크
        if( $('#pop_product_select_list li[data-num="' + p_num + '"]').length > 0 ) {
            return false;
        }

        var html = '';
        html += '<li class="list-group-item" data-num="' + p_num + '">';
        html += '   <div class="row">';
        html += '       <div class="col-sm-4"><img class="thumbnail" src="' + p_img + '" alt="" width="100%" /></div>';
        html += '       <div class="col-sm-4">';
        html += '           <p><b style="font-size: 16px;">' + p_name + '</b></p>';
        html += '           <button type="button" class="btn btn-danger btn-xs" onclick="product_select_delete(\'' + p_num + '\');">삭제</button>';
        html += '       </div>';
        html += '   </div>';
        html += '</li>';
        $('#pop_product_select_list').append(html);

        var num_arr = [];
        $.each($('#pop_product_select_list li'), function(){
            var num = $(this).attr('data-num');
            if( !empty(num) ) {
                num_arr.push(num);
            }
        });

        $('#pop_form [name="pmd_product_num"]').val(num_arr.join(':'));
    }//end of product_select()


    /**
     * 상품 선택
     */
    function product_select2(p_num, p_name, p_img) {
        console.log('product_select', p_num, p_name, p_img);
        info_message_all_clear();

        if( empty(p_num) ) {
            alert('잘못된 접근입니다.');
            return false;
        }

        //중복체크
        if( $('#pop_product_select_list div[data-num="' + p_num + '"]').length > 0 ) {
            return false;
        }

        var html = '';

        html += '   <div class="col-md-3 col-sm-12 col-xs-12 mgt10 mgb10 text-center list-item" data-num="' + p_num + '">';
        html += '       <a href="#none" style="font-size:20px;"><i class="fa fa-sort"></i></a>';
        html += '       <img src="'+p_img+'" style="width:100%;" alt="" />';
        html += '       <p class="alert alert-warning" style="padding: 5px!important;width: 100%">'+p_name+'</p>';
        //html += '       <a href="#none" onclick="product_select_delete(\'' + p_num + '\');" class="btn btn-danger btn-xs">삭제</a>';
        html += '   </div>';

        $('#pop_product_select_list').append(html);

        mod_data();

    }//end of product_select()

    function mod_data(){

        var num_arr = [];
        $.each($('#pop_product_select_list div'), function(){
            var num = $(this).attr('data-num');
            if( !empty(num) ) {
                num_arr.push(num);
            }
        });

        $('#pop_form [name="pmd_product_num"]').val(num_arr.join(':'));

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

    /**
     * 선택된 상품 삭제
     * @param p_num
     */
    function product_select_delete(p_num) {
        $('#pop_product_select_list div[data-num="' + p_num + '"]').remove();
    }


    function callProduct(){

        $.ajax({
            url : '/product_md/list_ajax',
            data : { md_div : $('input[name="pmd_division"]:checked').val() },
            type : 'post',
            dataType : 'json',
            success : function (result) {

                $('#pop_product_select_list').html('');//선택상품 초기화

                $.each(result,function(k,r){
                    var p_rep_image_array = JSON.parse(r.p_rep_image);
                    product_select2(r.p_num, r.p_name, p_rep_image_array[1])
                });

            }
        });

    }

    //document.ready
    $(function(){

        callProduct();
        $('input[name="pmd_division"]').on('change', callProduct )

        /*
        @date 200525
        @author 황기석
        @desc sortable
        */
        $('#pop_product_select_list').sortable({
            revert: true,
            helper: "clone",
            update: function( ) {
                // do stuff
                mod_data();
            }

        });
        $('#pop_product_select_list').disableSelection();

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

        $('#pop_form input[name="kwd"]').off('keypress').on('keypress', function(e){
            if( e.keyCode == 13 ) {
                product_search();
                e.preventDefault();
            }
        });

        //ajax form
        $('#pop_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            beforeSubmit: function(formData, jqForm, options) {
                if( $('#pop_form [name="pmd_division"]:checked').length < 1 ) {
                    //alert('카테고리를 선택하세요.');
                    error_message($('#field_pmd_division'), '카테고리를 선택하세요.');
                    return false;
                }
                if( !$('#pop_form [name="pmd_product_num"]').val() ) {
                    error_message($('#field_pmd_product_num'), '상품을 선택하세요.');
                    return false;
                }
            },
            success: function(res) {
                if( res.message ) {
                    if( res.message_type == 'alert' ) {
                        alert(res.message);
                    }
                }

                if( res.status == status_code['success'] ) {
                    //location.replace(list_url);
                    $('#search_form').submit();
                    modalPop.hide();
                }
                else {
                    if( res.error_data ) {
                        $.each(res.error_data, function(key, msg){
                            if( $('#field_' + key).length ) {
                                error_message($('#field_' + key), msg);
                            }
                        });
                    }
                }//end of if()
            },
            complete : function() {
                //$(this).attr('action', this_form_action);
            }
        });//end of ajax_form()

        //ajax page
        $(document).on('click', '#pop_product_list .pagination.ajax a', function(e){
            e.preventDefault();

            if( $(this).attr('href') == '#none' ) {
                return false;
            }

            product_search($(this).attr('href'));
        });
    });//end of document.ready()
</script>