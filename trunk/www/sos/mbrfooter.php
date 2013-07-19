            <div class="footer">
                <?if (!isset($footer_params) || $footer_params == 'nofooter') { ?>
                <div> 
                    <?if ($ctrl->filename() == 'mbrcekdata.php' || $ctrl->filename() == 'mbrpilihitem.php' || $ctrl->filename() == 'mbrordercheck.php' || $ctrl->filename() == 'mbrpaymentconfirm.php' ) { ?>
                            Pesan Online 
                    <? } else { 
                            if ($ctrl->filename() == 'mbrviewhistory.php' || isset($ctrl->lastorderstatus) ) 
                            {
                                    if ( isset($ctrl->lastorderstatus) )
                                    {
                                            if ( $ctrl->lastorderstatus >= 1 && $ctrl->lastorderstatus <= 6 )
                                            {
                    ?>
                                                    Pesan Online
                    <?
                                            }
                                            else
                                            {
                    ?>
                                                    <a href="mbrpilihitem.php">Pesan Online</a>
                    <?
                                            }
                                    }
                                    else
                                    {
                    ?>
                                            Pesan Online
                    <?
                                    }
                            }
                            else if ( isset($ctrl->statuscode) )
                            {
                                    if ($ctrl->statuscode == $ctrl->sysparam['salesstatus']['openorder'] || 
                                        $ctrl->statuscode == $ctrl->sysparam['salesstatus']['ordered'] )
                                    {
                    ?>
                            Pesan Online
                    <?		
                                    }
                                    else
                                    {
                    ?>
                            <a href="mbrpilihitem.php">Pesan Online</a>
                    <?		
                                    }
                            }
                            else
                            {
                    ?>
                            <a href="mbrpilihitem.php">Pesan Online</a>
                    <?  }
                       }
                    ?>
                    &nbsp;&nbsp;|&nbsp;&nbsp; 
                    <?if ($ctrl->filename() == 'mbrdisclaimer.php') { ?>
                            Terms and Conditions 
                    <? } else { ?>
                            <a href="mbrdisclaimer.php">Terms and Conditions</a>
                    <? } ?>
                    &nbsp;&nbsp;|&nbsp;&nbsp; 
                    <a href="mbrlogout.php">Log Out</a>
                </div>
                <br/>
                <? } ?>
                <div>Sophie Care: (021) 2922-7777 | Sen-Sab 08:00-20:00 WIB</div>
                <div>sophie.care@sophieparis.com | FB: <a href="http://www.facebook.com/sophieparisindonesia">Sophie Paris Indonesia</a></div>
            </div>
        </div>
    </form>
    </body>
</html>