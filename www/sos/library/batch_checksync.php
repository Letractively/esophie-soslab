<?php
include_once 'batchcontroller.php';
try
{
    $ctrl = new batchcontroller();
    $ctrl->runBatch('checksync');
}
catch (Exception $e)
{
    echo "[BATCH][".date("Y-m-d H:i:s")."][checksync] Exception " . $e->getMessage();
}
?>
