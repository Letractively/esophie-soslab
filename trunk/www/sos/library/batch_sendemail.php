<?php
include_once 'batchcontroller.php';
try
{
    $ctrl = new batchcontroller();
    $ctrl->runBatch('sendemail');
}
catch (Exception $e)
{
    echo "[BATCH][".date("Y-m-d H:i:s")."][sendemail] Exception " . $e->getMessage();
}
?>
