<?php
class tallythemesetup_import extends tallythemesetup_WP_Import
{
    function check(){
    //you can add any extra custom functions after the importing of demo coment is done
	return true;
    }
	
	function mp_request_timeout(){
		return 120;	
	}
}