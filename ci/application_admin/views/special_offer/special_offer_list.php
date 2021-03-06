<div class="container-fluid">
    <div class="row">
        <h4 class="page-header">기획전관리 > 목록</h4>
    </div>

    <div class="row well pd10">
        <div class="row">
            <div class="col-sm-10 col-xs-9" style="line-height:29px;">
                <span style="padding-right:10px;" class="activate_num">활성화 특가전 : <b></b> 건</span>
            </div>

            <div class="pull-right text-right mgr15">
                <button type="button" class="btn btn-info btn-sm mgl10" style="width:100px;" onclick="special_offer_insert();">등록</button>
                <button type="button" class="btn btn-warning btn-sm mgl10" style="width:100px;" onclick="special_offer_sorting();">순서변경</button>
            </div>
        </div>
    </div>

    <div class="row">

        <form name="search_form" id="search_form" method="post" action="<?php echo $this->page_link->list_ajax; ?>" class="form-horizontal">
            <input type="hidden" name="list_per_page" value="<?php echo $list_per_page; ?>" />
            <input type="hidden" name="page" value="<?php echo $req['page']; ?>" />
            <input type="hidden" name="sort_field" value="" />
            <input type="hidden" name="sort_type" value="" />

            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="kwd">검색분류</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-btn" style="width:auto">
                            <select id="kfd" name="kfd" class="form-control" style="border-bottom-left-radius:3px;border-top-left-radius:3px;">
                                <option value="thema_name">특가명</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="kwd" name="kwd" value="<?php echo $req['kwd']; ?>" style="width:auto;border-left:0;" />
                    </div>
                </div>
            </div>


            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="activate_flag">활성화 여부</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select name="activate_flag" id="activate_flag" class="form-control" style="width:auto;">
                            <option value="">전체</option>
                            <option value="Y">활성화</option>
                            <option value="N">비활성화</option>
                        </select>
                    </div>
                </div>
            </div>


            <?/*
            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label" for="date_type">일자검색</label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <select name="date_type" class="form-control" style="width:auto;">
                            <option value="reg_date">등록일</option>
                            <option value="start_date">노출시작일</option>
                            <option value="end_date">노출종료일</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary btn-outline btn-sm mgl5" onclick="set_date_term();">오늘</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3d');">3일</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-7d');">7일</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-1m');">1개월</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="set_date_term('-3m');">3개월</button>
                    <button type="button" class="btn btn-primary btn-outline btn-sm" onclick="clear_date_term();">전체</button>
                </div>
            </div>


            <div class="form-group form-group-sm">
                <label class="col-sm-2 control-label hidden-xs"></label>
                <div class="col-sm-10">
                    <div class="pull-left">
                        <div class="input-group date" style="width:133px;">
                            <input type="text" class="form-control" style="width:100px;" name="date1" value="<?php echo $req['date1']; ?>" />
                            <span class="input-group-btn text-left">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="pull-left text-center" style="width:20px;">~</div>
                    <div class="pull-left">
                        <div class="input-group date" style="width:133px;">
                            <input type="text" class="form-control" style="width:100px;" name="date2" value="<?php echo $req['date2']; ?>" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
*/?>
            <hr />

            <div class="form-group form-group-sm text-center">
                <button type="submit" class="btn btn-primary btn-sm" style="width:100px;">검색</button>
                <button type="button" class="btn btn-success btn-sm mgl10" style="width:100px;" onclick="location.href=''">전체보기</button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="row mgb10">
            <div class="col-md-2 pull-right">
                <select id="list_per_page" class="form-control input-sm">
                    <?php echo get_select_option("", $this->config->item('list_per_page'), $list_per_page); ?>
                </select>
            </div>
        </div>

        <div id="special_offer_list" style="position:relative;"></div>
    </div>
</div>

<script type="text/javascript">

    function special_offer_insert(){
        location.href="/special_offer/detail/";
    }
    function special_offer_sorting(){
        location.href="/special_offer/sorting/";
    }

    $(document).on('click','.special-offer-activate',function(){

        var cf = confirm('활성여부을 변경하시겠습니까 ? ');

        if(cf == false) return false;

        var flag = $(this).data('flag');
        var seq = $(this).parent().parent().data('seq');

        if(flag == 'Y'){
            var set_flag = 'N';
        }else{
            var set_flag = 'Y';
        }

        obj = {setFlag : set_flag , seq : seq};

        $.ajax({
            url: '/special_offer/set_activate',
            data: obj,
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (result) {

                if(result.status == '000'){
                    location.reload();
                }

            }
        });
    });

    $(document).on('click','.special-offer-update',function(){
        var seq = $(this).parent().parent().data('seq');
        location.href="/special_offer/detail/?seq="+seq;
    });

    $(document).on('click','.special-offer-delete',function(){

        var cf = confirm('해당 특가전을 삭제하시겠습니까 ? \n삭제 후 복구는 불가능합니다.');

        if(cf == false) return false;

        var seq = $(this).parent().parent().data('seq');

        obj = {seq : seq};

        $.ajax({
            url: '/special_offer/delete',
            data: obj,
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (result) {

                if(result.status == '000'){
                    location.reload();
                }

            }
        });

    });


    /**
     * form submit
     */
    function form_submit(str) {
        var arr1 = str.split('&');
        for(var i in arr1) {
            if( !empty(arr1[i]) ) {
                var arr2 = arr1[i].split('=');
                var name = arr2[0];
                var val = arr2[1];

                $('#search_form [name="' + name + '"]').val(val);
            }
        }//end of for()

        $('#search_form').submit();
    }//end of form_submit()

    //document.ready
    $(function () {

        //목록 갯수보기 select change
        $('#list_per_page').on('change', function(){
            $('#search_form [name="list_per_page"]').val($(this).val());
            $('#search_form').submit();
        });

        //Ajax Form
        if( $('#search_form').length > 0 ) {
            $('#search_form').ajaxForm({
                type: 'post',
                dataType: 'html',
                beforeSubmit: function(formData, jqForm, options) {
                    loadingBar.show($('#special_offer_list'));
                },
                success: function(result) {
                    $('#special_offer_list').html(result);
                },
                complete: function() {
                    loadingBar.hide();
                }
            });//end of ajax_form()

            $('#search_form').submit();

            //ajax page
            $(document).on('click', '#special_offer_list .pagination.ajax a', function(e){
                e.preventDefault();

                if( $(this).attr('href') == '#none' ) {
                    return false;
                }

                //$('#search_form').attr('action', $(this).attr('href'));
                //$('#search_form').submit();
                form_submit($(this).attr('href'));
            });
        }//end of if()

    });//end of document.ready

</script>
