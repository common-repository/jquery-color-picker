<?php
/*
 * core class for color picker 
 */
class ColorPickerCore{
   /**
	* Show a error messages
	*/
	function show_error($message) {
		echo '<div class="error">'.$message.'</div>';}
	/**
	* Show a system messages
	*/
	function show_message($message) {
            echo '<div class="success">'.$message.'</div>';
			
         }    
}
?>

