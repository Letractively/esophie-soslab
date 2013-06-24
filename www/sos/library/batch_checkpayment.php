<?php
include_once 'batchcontroller.php';
try
{
    $ctrl = new batchcontroller();
    $ctrl->runBatch('paymchecking');
}
catch (Exception $e)
{
    echo "[BATCH][".date("Y-m-d H:i:s")."][paymchecking] Exception " . $e->getMessage();
}
?>
