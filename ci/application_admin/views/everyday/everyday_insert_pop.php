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
                    <form name="pop_insert_form" id="pop_insert_form" method="post" class="form-horizontal" role="form" action="<?php echo $this->page_link->insert_proc; ?>">
                        <input type="hidden" name="ed_product_num" value="" />

                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">진행상태 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ed_usestate">
                                    <?php echo get_input_radio('ed_usestate', $this->config->item('everyday_usestate'), 'Y'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">노출여부 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ed_displaystate">
                                    <?php echo get_input_radio('ed_displaystate', $this->config->item('everyday_displaystate'), 'Y'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">당첨인원 <span class="txt-danger">*</span></label>
                            <div class="col-sm-10">
                                <div id="field_ed_winner_count">
                                    <input type="text" class="form-control" name="ed_winner_count" style="width:100px;" maxlength="5" numberOnly="true" value="" />
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
                            <div class="col-md-6 col-md-xs-12 left">
                                <label>검색된 상품 목록</label>
                                <div id="pop_product_list"></div>
                            </div>
                            <div class="col-md-6 col-md-xs-12 right">
                                <div id="field_ed_product_num">
                                    <label>선택된 상품 목록</label>
                                    <ul id="pop_product_select_list" class="list-group"></ul>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var form = '#pop_insert_form';

    /**
     * 상품 검색 (기간한정인 상품만)
     */
    function product_search(url) {
        var kwd = $(form + ' input[name="kwd"]').val();
        if( empty(kwd) ) {
            $(form + ' input[name="kwd"]').focus();
            return false;
        }
        if( empty(url) ) {
            url = '/product/search_ajax/?page=1&term_yn=Y&noPage=Y';
        }

        loadingBar.show($('#pop_product_list'));

        $.ajax({
            url : url,
            data : {kfd:'p_name', kwd:kwd},
            type : 'post',
            dataType : 'json',
            success : function (result) {
                var html = get_product_list_html(result.data);
                $('#pop_product_list').html(html);
            },
            complete : function() {
                loadingBar.hide();
            }
        });
    }//end of product_search()

    /**
     * 상품 출력
     */
    function get_product_list_html(data) {
        if( empty(data) ) {
            return false;
        }

        var html = '';
        html += '<ul class="list-group">';

        $.each(data, function(index, item){
            html += '<li class="list-group-item">';
            html += '   <div class="row">';
            html += '       <div class="pull-left"><img class="thumbnail" src="' + item['p_rep_image_array'][1] + '" alt="" width="60" /></div>';
            html += '       <div class="pull-left mgl5">';
            html += '           <p><b>' + item.p_name + '</b></p>';
            html += '           <p>' + item.p_display_state_text + ' / ' + item.p_sale_state_text + '</p>';
            html += '           <p>' + item.p_termlimit_datetime1.substr(0, 8) + ' ~ ' + item.p_termlimit_datetime2.substr(0, 8) + '</p>';
            html += '       </div>';
            html += '       <div class="pull-right"><button type="button" class="btn btn-primary btn-xs" onclick="product_select(\'' + item.p_num + '\', \'' + item.p_name + '\', \'' + item.p_rep_image_array[1] + '\', \'' + item.p_display_state_text + '\', \'' + item.p_sale_state_text + '\', \'' + item.p_termlimit_datetime1.substr(0, 8) + '\', \'' + item.p_termlimit_datetime2.substr(0, 8) + '\');">선택</button></div>';
            html += '   </div>';
            html += '</li>';
        });

        html += '</ul>';

        return html;
    }//end of get_product_list_html()

    /**
     * 상품 선택
     */
    function product_select(p_num, p_name, p_img, t_dis, t_sale, d1, d2) {
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
        html += '           <p>' + t_dis + ' / ' + t_sale + '</p>';
        html += '           <p>' + d1 + ' ~ ' + d2 + '</p>';
        html += '       </div>';
        html += '       <div class="pull-right"><button type="button" class="btn btn-danger btn-xs" onclick="product_select_delete(\'' + p_num + '\');">삭제</button></div>';
        html += '   </div>';
        html += '</li>';
        $('#pop_product_select_list').html(html);   //html=단일, append=다중(:로 구분)

        var num_arr = [];
        $.each($('#pop_product_select_list li'), function(){
            var num = $(this).attr('data-num');
            if( !empty(num) ) {
                num_arr.push(num);
            }
        });

        $(form + ' [name="ed_product_num"]').val(num_arr.join(':'));
    }//end of product_select()

    /**
     * 선택된 상품 삭제
     * @param p_num
     */
    function product_select_delete(p_num) {
        $('#pop_product_select_list li[data-num="' + p_num + '"]').remove();
    }//end of product_select_delete()


    //document.ready
    $(function(){
        //엔터키로 상품검색
        $(form + ' [name="kwd"]').on('keypress', function(e){
            if( e.keyCode == 13 ) {
                product_search();
            }
        });

        //submit check
        $(form).on('submit', function(){
            info_message_all_clear();
            Pace.restart();
        });

        //ajax form
        $(form).ajaxForm({
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            beforeSubmit: function(formData, jqForm, options) {
            },
            success: function(result) {
                if( result.message ) {
                    if( result.message_type == 'alert' ) {
                        alert(result.message);
                    }
                }

                if( result.status == status_code['success'] ) {
                    $('#search_form').submit();
                    modalPop.hide();
                }
                else {
                    if( result.error_data ) {
                        $.each(result.error_data, function(key, msg){
                            if( $('#field_' + key).length ) {
                                error_message($('#field_' + key), msg);
                            }
                        });
                    }
                }//end of if()
            },
            complete : function() {
                //$(this).attr('action', this_form_action);
                Pace.stop();
            }
        });//end of ajax_form()
    });//end of document.ready()
</script>