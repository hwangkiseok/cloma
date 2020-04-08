"use strict";


//상품문의
var cmt_page = 1;
var my_cmt_page = 1;

//리뷰
var local_data = [];
var p = 1;
var bFirst = true;

//탭
var onTab = false;

$(document).ready(function(){

    // var select2_obj = {placeholder: "문의 종류를 선택하세요.",minimumResultsForSearch : -1};
    // $('select[name="cmt_gubun"]').select2(select2_obj);

    chkDetailTap();
    $(window).scroll(function(){chkDetailTap();});

    //상품상세 정보제공
    $('.offered_info_title').on('click',function(){
        if($('.offered_info').hasClass('active')){
            $('.offered_info').removeClass('active');
            $(this).find('i').removeClass('on');
        } else {
            $('.offered_info').addClass('active');
            $(this).find('i').addClass('on');
        }
    });

    //상품문의등록 클릭
    $('.upsertFormCmt').on('click',function(){
        if(isLogin == 'N'){
            alert('로그인 후 작성가능합니다.');
            goLogin();
            return false;
        }else{
            $(this).hide();
            $('.cmtUpsertFormArea').show();
        }


    });

    //상품문의등록 취소클릭
    $('.cmt-cancel').on('click',function(){
        $('.upsertFormCmt').show();
        $('.cmtUpsertFormArea').hide();
        clearCmtForm();
    });


    //베스트코멘트
    //상세 리뉴얼로 주석처리
    /*
    $('.more_comm').on('click',function(){
        if(isApp == 'Y'){
            $('html, body').scrollTop(parseInt($('.comment_list_wrap').offset().top,10)-48);
        }else{
            $('html, body').scrollTop(parseInt($('.comment_list_wrap').offset().top,10)-96);
        }
    });

    $.ajax({
        url : '/comment/list_ajax',
        data : {best:'Y', tb_num :p_num ,tb : 'product'},
        type : 'post',
        dataType : 'json',
        success : function (result) {

            var lists           = result.list_data ;
            var html            = '';
            var best_cmt_cnt    = 0;

            for(var i = 0 ; i < lists.length ; i++){ var row = lists[i];

                var addStyle    = '';

                if(row.cmt_name != '미스할인' && best_cmt_cnt < 5){

                    best_cmt_cnt++;
                    if(best_cmt_cnt == 1) addStyle += ' padding-top:5px!important; ';

                    html += '   <li class="cmt-item" style="'+addStyle+'" >' +
                        '           <a style="width: 100%">' +
                        '               <strong class="sky_badge">Best</strong><span class="name">'+row.m_nickname+'</span><br>' +
                        '               <span class="cmt-txt">' + row.cmt_content + '</span>' +
                        '           </a>' +
                        '       </li>';
                }

            }

            if(best_cmt_cnt > 0){

                $('.best_comment_list .comment_list').html(html);
                $('.best_comment_list').show();
                $('.best_comment_list_bar').show();

                var cmt_add_btn  = '';
                cmt_add_btn += '<p class="more-less-btn-area">';
                cmt_add_btn += '<span style="text-decoration:underline;float: right;" class="cmt_more_btn" >더보기</span>';
                cmt_add_btn += '<span style="text-decoration:underline;float: right;display: none;" class="cmt_close_btn" >접기</span>';
                cmt_add_btn += '<span class="clear" style="display: block"></span>';
                cmt_add_btn += '</p>';

                //더보기 버튼
                $('.cmt-item .cmt-txt').each(function(){
                    if($(this).height() > 40){
                        $(this).css({'display':'-webkit-box' , '-webkit-line-clamp':'2'});
                        $(this).parent().parent().css({'height':'91px'});
                        $(this).parent().append(cmt_add_btn);
                    }
                });

            }

            $('.cmt_more_btn').on('click',function(){
                $(this).parent().parent().find('.cmt-txt').css({'display':'inline-block' , 'height':'100%' , 'max-height':''});
                $(this).parent().parent().parent().css({'height':''});
                //$(this).parent().parent().find('.cmt-txt').addClass('more');
                $(this).hide();
                $(this).parent().find('.cmt_close_btn').show();

            });

            $('.cmt_close_btn').on('click',function(){
                $(this).parent().parent().find('.cmt-txt').css({'display':'-webkit-box' , 'height':'', 'max-height':'40px'});
                $(this).parent().parent().parent().css({'height':'91px'});
                //$(this).parent().parent().find('.cmt-txt').removeClass('more');
                $(this).hide();
                $(this).parent().find('.cmt_more_btn').show();
            });

        }
    });
    */
    //상품문의등록 ajaxform
    $('form[name="cmtUpsertForm"]').ajaxForm({
        type: 'post',
        dataType: 'json',
        beforeSubmit: function(formData, jqForm, options) {
        },
        success: function(result) {

            if(result.msg) alert(result.msg);
            if(result.error_data) error_message_alert(result.error_data);

            if(result.success == true){
                $('.upsertFormCmt').show();
                $('.cmtUpsertFormArea').hide();
                clearCmtForm();

                $('.comment_cont table.cmt-all tbody tr').remove();
                $('.comment_cont table.cmt-my tbody tr').remove();
                //$('.comment_cont_tab span.active').click();

                var val = $('.comment_cont_tab2 span.active').data('val');
                /**
                 * @date 190806
                 * @author 황기석
                 * @desc 문의수 갱신이 안돼 ajax 재호출로 변경
                 **/
                if(val == 'my'){

                    my_cmt_page = 1;
                    $.ajax({
                        url : '/comment/product_list_ajax',
                        data : {tb_num : p_num , tb : 'product' , page : my_cmt_page , my : 'Y'},
                        type : 'post',
                        dataType : 'json',
                        success : function(result) {
                            comment_list_print(result,'my');
                            my_cmt_page++;
                        }
                    });

                }else if(val == 'all'){

                    cmt_page = 1;
                    $.ajax({
                        url : '/comment/product_list_ajax',
                        data : {tb_num : p_num , tb : 'product' , page : cmt_page},
                        type : 'post',
                        dataType : 'json',
                        success : function(result) {
                            comment_list_print(result,'all');
                            cmt_page++;
                        }
                    });

                }
            }
        },
        complete: function() {
        }
    });//end of ajax_form()

    //문의유형 팝업
    $('.cmt-gubun').on('click',function(){

        var container = $('<div></div>');
        var html = $('.cmt-gubun-pop-hidden').html();

        $(container).append(html);

        modalPop.createPop("문의유형", container);
        modalPop.show({'hide_header':true,'hide_footer':true,'body_class':'no_padding','center':true});

        $('#init-cmt-popup li').on('click',function(){
            var val = $(this).data('val');
            var text = $(this).html();

            $('form[name="cmtUpsertForm"] input[name="cmt_gubun"]').val(val);
            $('.cmtUpsertFormArea .cmt-gubun button').html(text+'</i><i>');
            modalPop.hide();

        });

    });


    //상품문의 전체 더보기
    $('.more_comment_all').on('click',function(){

        $.ajax({
            url : '/comment/product_list_ajax',
            data : {tb_num : p_num , tb : 'product' , page : cmt_page},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                comment_list_print(result,'all');
                cmt_page++;
            }
        });

    });

    //상품문의 탭
    $('.comment_cont_tab span').on('click',function(){

        $(this).addClass('active');
        $(this).siblings().removeClass('active');
        var val = $(this).data('val');

        if($('.comment_cont table.cmt-my tbody tr').length < 1 && val == 'my'){

            my_cmt_page = 1;
            $.ajax({
                url : '/comment/product_list_ajax',
                data : {tb_num : p_num , tb : 'product' , page : my_cmt_page , my : 'Y'},
                type : 'post',
                dataType : 'json',
                success : function(result) {
                    comment_list_print(result,'my');
                    my_cmt_page++;
                }
            });

        }else if($('.comment_cont table.cmt-all tbody tr').length < 1 && val == 'all'){

            cmt_page = 1;
            $.ajax({
                url : '/comment/product_list_ajax',
                data : {tb_num : p_num , tb : 'product' , page : cmt_page},
                type : 'post',
                dataType : 'json',
                success : function(result) {
                    comment_list_print(result,'all');
                    cmt_page++;
                }
            });

        }

        $('.comment_cont table').hide();
        $('.comment_cont table.cmt-'+val).show();

    });

    //구매평 더보기
    $('.more_review').on('click',function(){
        getReviewLists();
    })

});


//상세 설명으로 이동
$(document).on('click','.funcGoDetail', function(){
    $('.funcGoDetail').addClass('on').siblings().removeClass('on');
    $('html, body').stop(true,true).animate({scrollTop:$('.detail_title').offset().top - 53 + 'px'}, 500);
    $('.detail_wrap').show();
    $('.center_wrap').hide();
    $('.review_wrap').hide();
    $('.comment_wrap').hide();

    //$('#product_mostview_3').show();
    $('.review_list_wrap').show();
    $('.category_bestPdt_view').show();
    $('.bestPdt_view').show();

    $('.cate_pdtList').show(); // 큐레이션 활성
});

//고객센터정보로 이동
$(document).on('click','.funcGoCenter', function(){
    $('.funcGoCenter').addClass('on').siblings().removeClass('on');
    $('html, body').stop(true,true).animate({scrollTop:$('.detail_title').offset().top - 53 + 'px'}, 500);
    $('.detail_wrap').hide();
    $('.center_wrap').show();
    $('.review_wrap').hide();
    $('.comment_wrap').hide();

    //$('#product_mostview_3').hide();
    $('.review_list_wrap').hide();
    $('.category_bestPdt_view').hide();
    $('.bestPdt_view').hide();

    $('.cate_pdtList').hide(); // 큐레이션 비활성

});
//구매평로 이동
$(document).on('click','.funcGoReview', function(){

    //리뷰불러오기
    if( $('.review_wrap ul.review_list li').length < 1 ){
        getReviewLists();
    }

    $('.funcGoReview').addClass('on').siblings().removeClass('on');
    $('body').stop(true,true).animate({scrollTop:$('.detail_title').offset().top - 53 + 'px'}, 500);
    $('.detail_wrap').hide();
    $('.center_wrap').hide();
    $('.review_wrap').show();
    $('.comment_wrap').hide();
    //$('#product_mostview_3').hide();
    $('.review_list_wrap').hide();
    $('.category_bestPdt_view').hide();
    $('.bestPdt_view').hide();

    $('.cate_pdtList').hide(); // 큐레이션 비활성
});

//상품문의로 이동
$(document).on('click','.funcGoComment', function(){

    if($('.comment_cont table.cmt-all tbody tr').length < 1){

        $.ajax({
            url : '/comment/product_list_ajax',
            data : {tb_num : p_num , tb : 'product' , page : cmt_page},
            type : 'post',
            dataType : 'json',
            success : function(result) {
                comment_list_print(result,'all');
                cmt_page++;
            }
        });

    }

    $('.funcGoComment').addClass('on').siblings().removeClass('on');
    $('html, body').stop(true,true).animate({scrollTop:$('.detail_title').offset().top - 53 + 'px'}, 500);
    $('.detail_wrap').hide();
    $('.center_wrap').hide();
    $('.review_wrap').hide();
    $('.comment_wrap').show();
    //$('#product_mostview_3').hide();
    $('.review_list_wrap').hide();
    $('.category_bestPdt_view').hide();
    $('.bestPdt_view').hide();

    $('.cate_pdtList').hide(); // 큐레이션 비활성
});

$(document).on('click','.like_btn', function(){
    var seq = $(this).data('seq');
    var obj = this;

    if(isLogin == 'Y'){

        isShowLoader = false;

        $.ajax({
            url : '/review/setLike',
            data : {seq : seq},
            type : 'post',
            dataType : 'json',
            success : function(result) {

                var cnt = parseInt($(obj).find('em').html());

                if(result.success == true){

                    if($(obj).find('.fa-heart-o').hasClass('active') == true){ //도움x  -> 도움o
                        $(obj).find('.fa-heart-o').removeClass('active');
                        $(obj).find('.fa-heart').addClass('active');
                        $(obj).find('em').html(cnt+1);
                    }else{//도움o -> 도움x
                        $(obj).find('.fa-heart').removeClass('active');
                        $(obj).find('.fa-heart-o').addClass('active');
                        $(obj).find('em').html(cnt-1);
                    }

                }

            }

        });

    }else{ //비로그인인 경우 처리

        if(app_versioncode > 61){
            window.AndroidApp.login_pop_opne_1();
        }else{
            alert('로그인 후 가능합니다.');
            goLogin();
        }

    }

})



//상품문의 form init
function clearCmtForm(){
    $('form[name="cmtUpsertForm"] [name="cmt_gubun"]').val('');
    $('form[name="cmtUpsertForm"] [name="cmt_blind"]').prop('checked',true);
    $('form[name="cmtUpsertForm"] .cmt-gubun button').html('문의 종류를 선택하세요<i></i>');
    $('form[name="cmtUpsertForm"] [name="cmt_content"]').val('');
}

//스크롤체크
function chkDetailTap(){
    var checkPoint = $('#checkPoint').offset().top;
    var x = $(document).scrollTop();

    var html  = '';
        html += '<div class="detail_title fixed">';
        html += '</div>';

    if(x+68 >= checkPoint){
        if(onTab == false){
            var innerHtml   = $('.detail_title').not('.fixed').html();
            onTab       = true;

            $('.pdt_detail').append(html);
            $('.detail_title.fixed').html(innerHtml);
        }

    }else{
        onTab = false;
        $('.detail_title.fixed').remove();
    }

    if(x+68 >= checkPoint){
        onTab = true;
        if($('.detail_title.fixed').html() == '') $('.detail_title.fixed').html($('.detail_title').not('.fixed').html());
    }else{
        onTab = false;
        if($('.detail_title.fixed').html() != '') $('.detail_title.fixed').html('');
    }

}

//코멘트 print

function comment_list_print(data,type){

    var req = data.req;
    var list_data = data.list_data;
    var page_data = data.page_data;

    $('.funcGoComment span').html(page_data.all_count);
    $('.comment_cont_tab span[data-val="all"] i').html(page_data.all_count);
    $('.comment_cont_tab span[data-val="my"] i').html(page_data.my_count);

    var html = '';

    if(list_data.length < 1){

        html += '<tr>';
        html += '<td colspan="3"><img src="'+img_http+'/images/icon_none.png" width="60px" /> <br><br>등록된 상품문의가 없습니다.</td>';
        html += '</tr>';

    }else{

        $.each(list_data, function (index, row) {

            var secret_icon = row.cmt_blind == 'N'?'':'<i></i>';
            var add_delete = '';

            html += '<tr onclick="answer_open(this,\''+row.is_mine+'\',\''+row.cmt_blind+'\');">';
            if(row.reply_cmt.length > 0){
                html += '<td class="flag"><span class="complete">답변<br>완료</span></td>';
            }else{
                html += '<td class="flag"><span class="ing">검토중</span></td>';
            }

            html += '   <td class="cont">';
            html += '       <div class="contents_top">'+row.m_nickname+'&nbsp;'+row.comment_date+'</div>';
            html += '       <div class="contents_title">'+row.cmt_gubun_str+'입니다.'+secret_icon+'</div>';
            html += '   </td>';
            html += '   <td class="arrow">';
            html += '   <button><img src="'+img_http+'/images/arrow_down_grey.png" /></button>';
            html += '   </td>';
            html += '</tr>';

            if(row.is_mine == 'Y'){
                add_delete = '<div style="text-align: right;"><img src="/images/icon_trash_re.png" style="height :30px; margin: 0 10px;cursor: pointer;" onclick="cmt_delete('+row.cmt_num+');" /></div> ';
            }

            html += '<tr class="cont_detail">';
            html += '   <td class="cont" colspan="3">';
            html += '       <div class="contents_title question">'+row.cmt_content+add_delete+'</div>';

            if(row.reply_cmt.length > 0){
                var reply_cmt = row.reply_cmt;
                $.each(reply_cmt,function(kk,rr){
                    html += '       <div class="contents_top"><span class="admin">판매자답변</span>&nbsp;'+rr.comment_date+'</div>';
                    html += '       <div class="contents_title">';
                    html +=             rr.cmt_content;
                    html += '<br />';
                    if(rr.cmt_happy_talk == 'Y'){
                    html += '       <a class="cmt-happy-btn" style="cursor: pointer;" onclick="happy_start();" role="button">카카오톡 실시간상담</a>';
                    }
                });
            }

            html += '       </div>';
            html += '   </td>';
            html += '</tr>';

        });
    }

    $('.comment_wrap .comment_cont .cmt-'+type+' tbody').append(html);

    if(page_data.total_page <= cmt_page){
        $('.comment_wrap .comment_cont .more_comment_'+type).hide();
    }else{
        $('.comment_wrap .comment_cont .more_comment_'+type).show();
    }

}

/**
 * 댓글 삭제
 */
function cmt_delete(cmt_num) {
    if( empty(cmt_num) ) {
        return false;
    }

    if( !confirm('해당 댓글을 삭제하시겠습니까?') ) {
        return false;
    }

    $.ajax({
        url : '/comment/delete_proc',
        data : {cmt_num:cmt_num},
        type : 'post',
        dataType : 'json',
        cache : false,
        async : false,
        success : function (result) {

            if( result.status == status_code['success'] ) {

                $('.comment_cont table.cmt-all tbody tr').remove();
                $('.comment_cont table.cmt-my tbody tr').remove();
                $('.comment_cont_tab span.active').click();

            }

        }
    });
}//end of comment_delete()

//구매평 ajax
function getReviewLists(opener_chk){

    if(opener_chk == true) p = 1

    $.ajax({
        url: '/review/getProductReview/',
        data: {p_num : p_num , page : p},
        type: 'post',
        dataType: 'json',
        success: function (result) {
            print_review_lists(result , opener_chk);
            p++;
        }
    });

}
//구매평 print
function print_review_lists(result,opener_chk){
    var data = result.data;
    var req = result.req;

    if(req.tot_cnt > 0){

        var html = '';

        $.each(data,function(index,row){

            local_data[row.re_num] = row;

            var imgClass        = '';
            var recommendClass  = '';
            var recommendStr    = '';
            var img             = [];

            if(empty(row.re_img) == false && row.re_img != 'null') {
                img = JSON.parse(row.re_img);
            }

            if(row.re_grade == 'B'){
                recommendClass += 'good';
                recommendStr += '추천해요!';
            }else if(row.re_grade == 'C'){
                recommendClass += 'notbad';
                recommendStr += '아쉬워요';
            }else{
                recommendClass += 'verygood';
                recommendStr += '완전 추천해요!';
            }

            html += '<li class="'+imgClass+'">';
            html += '<div class="review_grade clear '+recommendClass+' "><i></i> <span class="fl">'+recommendStr+'</span></div>';

            /*<!--구매평 도움되요 버튼-->*/
            html += '<div class="like_btn zs-cp" data-seq="'+row.re_num+'">';
            html += '   <div class="heart_icon">';

            if(row.my_like_cnt > 0 ){
                html += '       <i class="fa fa-heart-o"></i>';
                html += '       <i class="fa fa-heart active"></i>';
            }else{
                html += '       <i class="fa fa-heart-o active"></i>';
                html += '       <i class="fa fa-heart"></i>';
            }
            html += '   </div>';
            html += '   <em>'+row.re_heart+'</em>';
            html += '   <div class="clearfix"></div>';
            html += '</div>';
            /*<!--구매평 도움되요 버튼-->*/


            if(img.length > 0){
                html += '<div class="re_photo">';
                $.each(img,function(k,v){
                    html += '<div class="photo_area" onclick="photo_showPop('+row.re_num+','+k+');" >';
                    html += '<img src="'+v+'" alt="">';
                    html += '</div>';
                });
                html += '</div>';
            }
            html += '<p class="re_txt" style="max-height: 100%;">'+row.re_content_rep+'</p>';
            html += '<span></span>';
            html += '<span class="nick">'+row.re_name+'</span><span style="color:#aaa;font-size: 13px;">|</span><span class="date">'+row.re_regdatetime_rep+'</span>';
            html += '</li>';
        });

        if(opener_chk == true){
            $('.review_wrap ul.review_list').html(html);
        }else{
            $('.review_wrap ul.review_list').append(html);
        }
        $('.review_wrap ul.review_list').show();

        var enable_expend = false;
        if($('.review_wrap ul.review_list li').length > 0){

            var review_add_btn  = '';
            review_add_btn += '<p class="more-less-btn-area">';
            review_add_btn += '<span style="text-decoration:underline;float: right;" class="review_more_btn zs-cp" >더보기</span>';
            review_add_btn += '<span style="text-decoration:underline;float: right;display: none;" class="review_close_btn zs-cp" >접기</span>';
            review_add_btn += '<span class="clear" style="display: block"></span>';
            review_add_btn += '</p>';

            /*더보기 버튼*/
            $('.review_wrap ul.review_list li').each(function(){

                if($(this).find('.re_txt').height() > 40){
                    $(this).find('.re_txt').css({'display':'-webkit-box' , '-webkit-line-clamp':'2', 'overflow-y':'hidden','height':'40px'});

                    if( $(this).find('.re_txt').next().find('.more-less-btn-area').length < 1){
                        $(this).find('.re_txt').next().append(review_add_btn);
                        enable_expend = true;
                    }
                }
            });

        }

        if(enable_expend == true){
            $('.review_more_btn').on('click',function(){
                $(this).parent().parent().parent().find('.re_txt').css({'display':'inline-block' , 'height':'100%' , 'max-height':''});
                $(this).hide();
                $(this).parent().find('.review_close_btn').show();

            });
            $('.review_close_btn').on('click',function(){
                $(this).parent().parent().parent().find('.re_txt').css({'display':'-webkit-box' , '-webkit-line-clamp':'2', 'overflow-y':'hidden','height':'40px'});
                $(this).hide();
                $(this).parent().find('.review_more_btn').show();
            });
        }

        var photo_w = $('.photo_area').width();
        $('.photo_area').height(photo_w);

        if($('.review_wrap ul.review_list li').length == req.tot_cnt ){
            $('.more_review').remove();
        }


    }else{
        if(bFirst == true){

            $('.review_wrap ul.review_list').css('line-height','68px');
            $('.review_wrap ul.review_list').append('<li style="text-align: center; display: block; margin: 0 auto; padding: 0px 0px 20px 0px ; border:none;">작성된 리뷰가 없습니다.</li>');
            $('.review_wrap ul.review_list').show();
        }
        bFirst = false;
        $('.more_review').remove();
    }

}

//구매평 best print
function print_review_best_lists(result){
    var data = result.data;
    var req = result.req;

    if(req.tot_cnt > 0){

        //console.log(req);
        $('.review_list_wrap .review_all_cnt').html(req.tot_cnt);
        //$('.review_list_wrap .review_count').html(req.tot_cnt);
        var html = '';

        $.each(data,function(index,row){

            local_data[row.re_num] = row;

            var imgClass        = '';
            var recommendClass  = '';
            var recommendStr    = '';
            var img             = [];

            if(empty(row.re_img) == false && row.re_img != 'null') {
                img = JSON.parse(row.re_img);
            }

            if(img.length > 0) imgClass += ' photo_review ';

            if(row.re_grade == 'B'){
                recommendClass += 'good';
                recommendStr += '추천해요!';
            }else if(row.re_grade == 'C'){
                recommendClass += 'notbad';
                recommendStr += '아쉬워요';
            }else{
                recommendClass += 'verygood';
                recommendStr += '완전 추천해요!';
            }

            html += '<li class="'+imgClass+'">';
            html += '<div class="review_grade clear '+recommendClass+' "><i></i> <span class="fl">'+recommendStr+'</span></div>';

                /*<!--구매평 도움되요 버튼-->*/
                html += '<div class="like_btn zs-cp" data-seq="'+row.re_num+'">';
                html += '   <div class="heart_icon">';

                if(row.my_like_cnt > 0 ){
                    html += '       <i class="fa fa-heart-o"></i>';
                    html += '       <i class="fa fa-heart active"></i>';
                }else{
                    html += '       <i class="fa fa-heart-o active"></i>';
                    html += '       <i class="fa fa-heart"></i>';
                }
                html += '   </div>';
                html += '   <em>'+row.re_heart+'</em>';
                html += '   <div class="clearfix"></div>';
                html += '</div>';
                /*<!--구매평 도움되요 버튼-->*/

            if(img.length > 0){
                html += '<div class="re_photo">';
                $.each(img,function(k,v){
                    html += '<div class="photo_area" onclick="photo_showPop('+row.re_num+','+k+');" >';
                    html += '<img src="'+v+'" alt="">';
                    html += '</div>';
                });
                html += '</div>';
            }
            html += '<p class="re_txt">'+row.re_content_rep+'</p>';
            html += '<span></span>';
            html += '<span class="nick">'+row.re_name+'</span>|<span class="date">'+row.re_regdatetime_rep+'</span>';
            html += '</li>';
        });

        $('.review_list_wrap ul.review_list').html(html);
        $('.review_list_wrap ul.review_list').show();

        var enable_expend = false;
        if($('.review_list_wrap ul.review_list li').length > 0){

            var review_add_btn  = '';
            review_add_btn += '<p class="more-less-btn-area">';
            review_add_btn += '<span style="text-decoration:underline;float: right;" class="review_more_btn zs-cp" >더보기</span>';
            review_add_btn += '<span style="text-decoration:underline;float: right;display: none;" class="review_close_btn zs-cp" >접기</span>';
            review_add_btn += '<span class="clear" style="display: block"></span>';
            review_add_btn += '</p>';

            /*더보기 버튼*/
            $('.review_list_wrap ul.review_list li').each(function(){
                if($(this).find('.re_txt').height() > 40){
                    $(this).find('.re_txt').css({'display':'-webkit-box' , '-webkit-line-clamp':'2'});


                    if( $(this).find('.re_txt').next().find('.more-less-btn-area').length < 1){
                        $(this).find('.re_txt').next().append(review_add_btn);
                        enable_expend = true;
                    }
                }
            });

        }

        if(enable_expend == true){
            $('.review_more_btn').on('click',function(){
                $(this).parent().parent().parent().find('.re_txt').css({'display':'inline-block' , 'height':'100%' , 'max-height':''});
                $(this).hide();
                $(this).parent().find('.review_close_btn').show();

            });
            $('.review_close_btn').on('click',function(){
                $(this).parent().parent().parent().find('.re_txt').css({'display':'-webkit-box' , 'height':'', 'max-height':'40px'});
                $(this).hide();
                $(this).parent().find('.review_more_btn').show();
            });
        }

        var photo_w = $('.photo_area').width();
        $('.photo_area').height(photo_w);

    }

}

//상품문의 상세
function answer_open(obj,is_mine,cmt_blind){

    if(is_mine == 'N' && cmt_blind == 'Y'){
        alert('비밀글입니다.');
        return false;
    }

    if($(obj).next().hasClass('cont_detail') == true){
        if( $(obj).next().hasClass('active') == true ){
            $(obj).next().removeClass('active');
            $(obj).find('.arrow>button').removeClass('on');
        }else{
            $(obj).next().addClass('active');
            $(obj).find('.arrow>button').addClass('on');

        }
    }

}

//구매평 이미지 상세
function photo_showPop(seq,moveTo){
    var url = '/product/getReviewPop/?seq=' + seq + '&img_select=' + moveTo;
    if( appCheck() ) {

        if(app_dv == "ios"){
            go_link(url);
        }else{
            go_link(url, 'N2');
        }


    }else{
        window.open(url, '_blank');
    }
}

//코멘트 등록전 유효성검사
function cmt_vaild_chk(){

    if( $('form[name="cmtUpsertForm"] [name="cmt_gubun"]').val() == ''){
        alert('문의종류를 선택해주세요!');
        return false;
    }

    if( $('form[name="cmtUpsertForm"] [name="cmt_content"]').val() == ''){
        alert('문의내용을 입력해주세요!');
        return false;
    }

}