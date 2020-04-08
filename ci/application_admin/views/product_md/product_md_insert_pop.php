<style>
    .list-group { max-height:280px; overflow-y:auto; }
    .list-group-item { padding:5px; }
    .list-group-item .row { margin:0; padding:5px; }
    .list-group-item .row p { margin:0; }
    #product_list_group .left { padding-right:5px; }
    #product_list_group .right { padding-left:5px; }
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

                        <div class="form-group form-group-sm" id="product_list_group">
                            <div class="col-md-7 col-md-xs-12 left">
                                <label>검색된 상품 목록</label>
                                <!--<div class="form-inline">-->
                                <!--    <div class="input-group">-->
                                <!--        <input type="text" name="kwd" class="form-control" placeholder="상품명을 입력하세요" />-->
                                <!--        <span class="input-group-btn"><button type="button" class="btn btn-primary btn-sm" onclick="product_search();">검색</button></span>-->
                                <!--    </div>-->
                                <!--</div>-->
                                <div id="pop_product_list"></div>
                            </div>
                            <div class="col-md-5 col-md-xs-12 right">
                                <div id="field_pmd_product_num">
                                    <label>선택된 상품 목록</label>
                                    <ul id="pop_product_select_list" class="list-group"></ul>
                                </div>
                            </div>
                        </div>
                        <!--<div class="form-group form-group-sm form-inline">-->
                        <!--    <label class="col-sm-2 control-label">출력순서</label>-->
                        <!--    <div class="col-sm-10">-->
                        <!--        <input type="text" id="field_p_name" name="p_name" class="form-control" style="width:100px;" />-->
                        <!--        <span class="help-inline middle txt-default mgl10">* 미입력시 가장 하위에 노출됩니다.</span>-->
                        <!--    </div>-->
                        <!--</div>-->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /*
    //window.paceOptions = {
    Pace.options = {
        //target: '#pop_product_list'
        elements: {
            selectors: ['#pop_product_list']
        }
    };
    */

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
                //$('#pop_product_list').html(result);

                //var html = '';
                //html += '<ul>';
                //html += '</ul>';

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

            if( item.p_display_state == 'N' ) {
                disp_text = '<span class="badge badge-danger">진열안함</span>';
            }
            if( item.p_sale_state == 'N' ) {
                sale_text = '<span class="badge badge-danger">판매종료</span>';
            }
            if( item.p_stock_state == 'N' ) {
                stock_text = '<span class="badge badge-danger">품절</span>';
            }

            html += '<li class="list-group-item">';
            html += '   <div class="row">';
            html += '       <div class="pull-left"><img style="margin-bottom: 0!important;" class="thumbnail" src="' + item.p_rep_image_array[1] + '" alt="" width="60" /></div>';
            html += '       <div class="pull-left mgl5">';
            html += '           <p><b>' + item.p_name + '</b></p>';
            html += '           <p>' + disp_text + '&nbsp;' + sale_text + '&nbsp;'  + stock_text + '</p>';
            html += '       </div>';
            html += '   </div>';
            html += '   <div class="clear"></div>';
            html += '   <div><button type="button" class="btn btn-warning btn-xs" style="width: 100%;padding: 5px 0" onclick="product_select(\'' + item.p_num + '\', \'' + item.p_name + '\', \'' + item.p_rep_image_array[1] + '\');">선택</button></div>';
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
        html += '       <div class="pull-left"><img class="thumbnail" src="' + p_img + '" alt="" width="60" /></div>';
        html += '       <div class="pull-left mgl5">';
        html += '           <p><b>' + p_name + '</b></p>';
        html += '       </div>';
        html += '       <div class="pull-right"><button type="button" class="btn btn-danger btn-xs" onclick="product_select_delete(\'' + p_num + '\');">삭제</button></div>';
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

        console.log(num_arr);
        console.log(num_arr.join(':'));
        $('#pop_form [name="pmd_product_num"]').val(num_arr.join(':'));
    }//end of product_select()

    /**
     * 선택된 상품 삭제
     * @param p_num
     */
    function product_select_delete(p_num) {
        $('#pop_product_select_list li[data-num="' + p_num + '"]').remove();
    }

    //document.ready
    $(function(){
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