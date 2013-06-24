<?php
include_once 'batchcontroller.php';
try
{
    $ctrl = new batchcontroller();
    $ctrl->runBatch('sendsms');
}
catch (Exception $e)
{
    echo "[BATCH][".date("Y-m-d H:i:s")."][sendsms] Exception " . $e->getMessage();
}
?>
