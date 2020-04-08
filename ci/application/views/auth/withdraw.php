<script>
    /**
     * 회원탈퇴 실행
     */
    //document.ready
    $(function () {
        if( appCheck() ) {
            appUnlink();
        }
        else {
            location_replace('<?php echo $this->config->item("error_url"); ?>');
        }
    });//end of document.ready
</script>