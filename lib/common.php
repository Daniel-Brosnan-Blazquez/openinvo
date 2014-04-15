<?

function get_value ($var) {
    
    $res = "";

	if (isset ($_GET{$var})) {
        $res = $_GET{$var};
    }

    return $res;

}

?>