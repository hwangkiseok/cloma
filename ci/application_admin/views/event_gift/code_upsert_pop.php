<style>
    .gifticon_list li{margin-bottom: 5px;}
    .gifticon_list li:last-child{margin-bottom: 0px;}

    .delGifticon {vertical-align: baseline;margin-left: 5px;}
</style>

<form name="gift_code_update_form" id="gift_code_update_form" method="post" class="form-horizontal" role="form" action="/event_gift/code_upsert/" onsubmit="return form_valid();">

    <div class="form-group form-group-sm">
        <label class="col-sm-3 control-label">이벤트</label>
        <div class="col-sm-9">
            <div id="field_event_code">
                <select name="event_code" class="form-control" style="width:auto;">
                    <option value="">* 선택 *</option>
                    <? foreach ($aEventLists as $row) { ?>
                        <option value="<?=$row['e_code']?>"><?=$row['e_subject']?></option>
                    <?} ?>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group form-group-sm">
        <label class="col-sm-3 control-label">기프티콘 추가</label>
        <div class="col-sm-9">
            <button class="btn btn-sm btn-primary addGifticon">추가</button>
            <button class="btn btn-sm btn-primary copyGifticon" style="display: none;">금월 코드복사</button>
        </div>
    </div>

    <div class="form-group form-group-sm gifticon_list_area" style="display: none;">
        <label class="col-sm-3 control-label">기프티콘 리스트</label>
        <div class="col-sm-9">
            <ul class="list-unstyled gifticon_list"></ul>
        </div>
    </div>

</form>



<script type="text/javascript">

    $(function(){

        //ajaxform
        $('#gift_code_update_form').ajaxForm({
            type: 'post',
            dataType: 'json',
            beforeSubmit: function(formData, jqForm, options) {
                loadingBar.show($('#gift_code_update_form'));
                //form_valid();
            },
            success: function(result) {
                if(result.msg) alert(result.msg);
                setGifticonList();
            },
            complete: function() {
                loadingBar.hide();
            }
        });//end of ajax_form()

        $('select[name="event_code"]').on('change', setGifticonList );
    });

    function form_valid(){

        if($('select[name="event_code"]').val() == ''){
            alert('이벤트를 선택해주세요 !');
            return false;
        }
        if ( $('.gifticon_list li').length < 1 ){
            alert('등록할 기프티콘을 추가/입력해주세요 !');
            return false;
        }

    }

    function empty_row_chk(){

        //if ( $('.gifticon_list li').length < 1 ){
            var html = '<li class="form-control-static row_empty" style="padding-left: 0px;">등록된 기프티콘 코드가 없습니다.</li>';
        //     $('.gifticon_list_area .gifticon_list').html(html);
        // }
        return html;

    }


    function setGifticonList(){

        if($('select[name="event_code"]').val() == '') return false;

        $.ajax({
            url: '/event_gift/getGiftCode',
            data: {e_code : $('select[name="event_code"]').val()},
            type: 'post',
            dataType: 'json',
            success: function (result) {

                $('.gifticon_list_area').show();

                if ( result.info.e_content_type == 2 ){

                    $('.gifticon_list_area .gifticon_list').html('');
                    $('.addGifticon').hide();
                    $('.copyGifticon').show();

                    var data = result.data;
                    var i = 0;
                    $.each(data,function(k,r){
                        print_code_list_v2(k,r,i);
                        i++;
                    });

                    //$('.gifticon_list .alert[data-ym="201811"]').parent().nextAll().remove();
                }else{

                    $('.addGifticon').show();
                    $('.copyGifticon').hide();

                    print_code_list(result.data);

                }

            }
        })

    }

    function print_code_list_v2(ym,records,seq){

        var addStyle = '';
        if(seq > 0) addStyle += ' margin-top:20px; ';

        var html  = "<li><p class='form-control-static alert alert-warning' style='margin-bottom: 5px;"+addStyle+"' data-ym='"+ym+"'>"+ym+" 기프티콘</p></li>";
            html += print_code_list(records,'return');
        $('.gifticon_list_area .gifticon_list').append(html);

    }


    function print_code_list(data,view_type){

        if( data.length > 0 ){

            var html = '';
            $.each(data,function(index,row){

                var state_Y = '';
                var state_N = '';

                var type_1 = '';
                var type_2 = '';
                var type_3 = '';

                if(row.use_flag == 'Y') state_Y = ' selected ';
                else state_N = ' selected ';

                if(row.type == '1') type_1 = ' selected ';
                else if(row.type == '2') type_2 = ' selected ';
                else type_3 = ' selected ';


                html += '<li data-seq="'+row.seq+'" data-ym="'+row.gift_ym+'">' +
                    '   <input type="text" class="form-control" name="code_name['+row.seq+']" style="width: 35%;display: inline-block;" placeholder="이름" value="'+row.gift_name+'" > ' +
                    '   <input type="text" class="form-control" name="code['+row.seq+']" style="width: 20%;display: inline-block;"  placeholder="코드" value="'+row.gift_code+'" > ' +
                    '   <select class="form-control" style="width: 95px;display: inline-block;" name="code_use_flag['+row.seq+']"><option value="Y" '+state_Y+'>사용함</option><option value="N" '+state_N+'>사용안함</option></select> ' +
                    '   <select class="form-control" style="width: 95px;display: inline-block;" name="code_type['+row.seq+']"><option value="1" '+type_1+'>상품</option><option value="2" '+type_2+'>쿠폰</option><option value="3" '+type_3+'>적립금</option></select> ' +
                    '   <button class="btn btn-sm btn-danger delGifticon" type="button">삭제</button>' +
                    '</li>';
            });

            if(view_type == 'return'){
                return html;
            }else{
                $('.gifticon_list_area .gifticon_list').html(html);
            }
        }else{

            if(view_type == 'return'){
                return empty_row_chk();
            }else{
                $('.gifticon_list_area .gifticon_list').html(empty_row_chk());
                //empty_row_chk();
            }

        }

    }


</script>