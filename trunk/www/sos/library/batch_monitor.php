<?php
include_once 'batchcontroller.php';
try
{
    $ctrl = new batchcontroller();
    $ctrl->runBatch('monitor');
}
catch (Exception $e)
{
    echo "[BATCH][".date("Y-m-d H:i:s")."][monitor] Exception " . $e->getMessage();
}
?>
