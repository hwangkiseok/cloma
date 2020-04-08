
<script>
    /**
     * 로그아웃 실행
     */
    //document.ready
    $(function () {
        if( appCheck() ) {
            appLogout();
        }
        else {
            location_replace('/');
        }
    });//end of document.ready
</script>