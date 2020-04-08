<?php if ( !$no_footer ) { ?>
    </div>
    <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->

<script>
    /**
     * 회원 수정 팝업
     * @param m_num
     */
    function member_update_pop(m_num) {
        if( empty(m_num) || m_num == 0 ) {
            return false;
        }

        new_win_open('/member/update/?m_num=' + m_num + '&pop=y', 'mem_win', 1200, 800);
    }//end of member_update_pop()
</script>

<?php }//end of if( no_footer ) ?>
<iframe name="actionFrame" id="actionFrame" class="actionFrame hide"></iframe>
</body>
</html>