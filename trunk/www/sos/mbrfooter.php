            <div class="footer">
                <?if (!isset($footer_params) || $footer_params != 'nolinks') { ?>
                <div> 
                    <a href="http://sophiemobile.com">Sophie Mobile</a>
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