<?php
include_once 'batchcontroller.php';
try
{
    $ctrl = new batchcontroller();
    $ctrl->runBatch('autobypass');
}
catch (Exception $e)
{
    echo "[BATCH][".date("Y-m-d H:i:s")."][autobypass] Exception " . $e->getMessage();
}
?>
