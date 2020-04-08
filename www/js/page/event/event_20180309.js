$(document).ready(function(){

    $('.megashow_ticket_issue').on('click',function(){

        $.ajax({
            url: '/event/event_megashow_tk_insert',
            data: {},
            type: 'post',
            dataType: 'json',
            cache: false,
            success: function (result) {

                var data = result.data;

                if(result.status == status_code['success'] ){

                    if(data.type == 'confirm'){
                        if(confirm('축하합니다!\n입장권이 발급되었습니다.\n바로 확인하시겠습니까?')){
                            go_link(result.goUrl);
                        }
                    }else{
                        alert('입장권이 이미 발급되었습니다.\n나의당첨내역으로 이동합니다.');
                        go_link(result.goUrl);
                    }

                    return false;
                }else{
                    alert(result.message);
                    return false;
                }
            }
        });

    });

});