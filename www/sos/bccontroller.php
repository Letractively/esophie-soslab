<?
	include_once "library/bccontroller.php";
	include_once "library/bcrptcontroller.php";
	if (file_exists("process/" . basename($_SERVER["PHP_SELF"])))
        {
            include_once "process/" . basename($_SERVER["PHP_SELF"]);
            $classname = str_replace(".php","",basename($_SERVER["PHP_SELF"]));
            $ctrl = new $classname;
            $ctrl->run();
        }
        else throw new Exception("Internal Error");
?>