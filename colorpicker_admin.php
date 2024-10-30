<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
/* class for picker admin */
class Colorpicker { 
    /**
     * PHP4 compatibility layer for calling the PHP5 constructor.
     *
     */
    function Colorpicker() {
        return $this->__construct();
    }
    /**
     * Colorpicker::__construct()
     *
     * @return void
     */
    function __construct() {
       	// same as $_SERVER['REQUEST_URI'], but should work under IIS 6.0
	   $this->filepath    = admin_url() . 'admin.php?page=' . $_GET['page']; 
           $this->colorpicker_content();
  		//code for POST updates
		if ( !empty($_POST) )
			$this->processor();
    }
	/**
	 * Perform the upload and add a new hook for plugins
	 *
	 * @return void
	 */
	function processor() { 
      global $wpdb,$message; $errors = new WP_Error();
        $cp_select = $_POST['cp_select'];
		$cp_id = $_POST['cp_id'];
		$default_color= $_POST['default_color'];
		if($cp_id=='' || $default_color=='' ||  $cp_select=='')
         {
		 	 $cp_Error .='Please enter required fields'; 
			 wp_redirect($this->filepath);  exit;
		 }
		if($cp_select=='1')
			$cp_type = "textfield";
		else
			$cp_type = "icon";
		if(isset($_GET['mode']) && $_GET['mode']=='edit')
		{    
		    $id =$_GET['id'];
			$shortcode = '[color-picker id="'.$cp_id.'" type="'.$cp_type.'" color="'.$default_color.'"]';
			$tablename= $wpdb->prefix.'colorpicker';
		    $type = $wpdb->get_row( "SELECT nCpId FROM $tablename WHERE sPickername='$cp_id' and nCpId!='$id'",ARRAY_A);
                if(empty($type['nCpId'])){
                        $data = $wpdb->update( $tablename, array(
                                    'nPickerType'=>$nPickerType,
                                    'sPickername' => $cp_id,
									'sDefaultColor'=>$default_color,
									'sShortcode' => $shortcode),array('nCpId'=>$id));
								
                        if ($data) { 
							
                          $cp_Message .= 'Color Picker updated Successfully';
						  wp_redirect(admin_url() . 'admin.php?page=' . $_GET['page']);  exit; 
						}
				}else{
					 $cp_Error .= 'Color Picker Id already exists!.'; 			 		 
				}
		}else{
			$shortcode = '[color-picker id="'.$cp_id.'" type="'.$cp_type.'" color="'.$default_color.'"]';
			$tablename= $wpdb->prefix.'colorpicker';
			$type = $wpdb->get_row( "SELECT nCpId FROM $tablename WHERE sPickername='$cp_id'",ARRAY_A);
                if(empty($type['nCpId'])){
                        $data = $wpdb->insert( $tablename, array(
                                    'nPickerType'=>$nPickerType,
                                    'sPickername' => $cp_id,
									'sDefaultColor'=>$default_color,
									'sShortcode' => $shortcode));
									
                        if ($data) { 
						 	$cp_Message .= 'Color Picker added Successfully';
							wp_redirect($this->filepath);  exit;
                       
							 
						}
				}else{
					 $cp_Error.= 'Color Picker Id already exists!'; 			 		
				}
		}
		
 		}
    /**
     * Render the page content
     *
     * @return void
     */ 
    function colorpicker_content() {
        global $message,$wpdb;
       $tablename= $wpdb->prefix.'colorpicker';
        if(isset($_GET['mode']) && $_GET['mode']=='delete')
                {  $id =$_GET['id'];
                 $delete= $wpdb->query( $wpdb->prepare("DELETE FROM $tablename WHERE nCpId = %d",$id ));                 
				?><div class="sucess">
                     <?php $cp_Message.= 'Color Picker deleted Successfully';
                ?></div>   
                <?php 
          		}else if(isset($_GET['mode']) && $_GET['mode']=='edit')
					{ 
						$id=$_GET['id'];        
						$edit_colorpick = $wpdb->get_row( "SELECT * FROM $tablename WHERE nCpId=$id",ARRAY_A  );
			            $sPickername = $edit_colorpick['sPickername'];      
			            $nPickerType  = $edit_colorpick['nPickerType'];
			            $sShortcode = $edit_colorpick['sShortcode'];
			            $sDefaultColor = $edit_colorpick['sDefaultColor'];
			         } 
			    ?>
	       <form name="cp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" class="cp_form">
            <div class="entry-edit-head">
					<h4 class="head-edit-form fieldset-legend">Color Picker Settings</h4>         
			</div>
            <div class="colorpicker_form" id="colorpicker_form">
              <div class="main-content">
				<label for="cp_select" class="lable01">
				      <span class="ttl02">Color Picker selection<span class="required">*</span></span>
				<select id="cp_select" name="cp_select">
					<option	value="">SELECT</option>
                    <option	value="1" <?php if($nPickerType=='1'){ echo "selected='selected'"; } ?>>Text Field</option>
					<option value="2" <?php if($nPickerType=='2'){ echo "selected='selected'"; } ?>>Icon</option>
				</select></label>
				<label for="cp_id" class="lable01"><span class="ttl02">Color Picker ID & Name<span class="required">*</span></span>
					<input type="text" name="cp_id" value="<?php echo $sPickername; ?>" ></span></label>
				<label for="default_color"  class="lable01"><span class="ttl02">Default color<span class="required">*</span></span>
						<span class="ttl01"># <input type="text" name="default_color" value="<?php echo $sDefaultColor; ?>"></span></label>
			  </div>
				<input type="submit" name="category_save" value="Save" class="button"/><br/><br/>
		   </div>
		  </form><?php  if($_GET['mode']!='edit'){ ?> <div class="container">
          <div class="heading">
              <div class="col">Picker Type</div>
              <div class="col">Picker ID</div>
			  <div class="col">Default Color</div>
              <div class="col">ShortCode</div>
              <div class="col">Action</div>
          </div>
          <?php global $wpdb;$tablename= $wpdb->prefix.'colorpicker';
          $pickers = $wpdb->get_results( "SELECT * FROM $tablename");
          foreach ( $pickers as $picker ) 
	         { 
			   $pickertype= ($picker->nPickerType == '1' ? 'Text' : 'Icon');
			   $delete= plugins_url('images/delete.png',__FILE__);
			   $edit= plugins_url('images/edit.png',__FILE__)
           ?>
          <div class="table-row">
              <div class="col"><?php echo $pickertype; ?></div>
              <div class="col"><?php echo $picker->sPickername; ?></div>
			  <div class="col"><?php echo $picker->sDefaultColor; ?></div>
              <div class="col"><?php echo $picker->sShortcode; ?></div>
              <div class="col"><a href="<?php echo $this->filepath."&mode=edit&id=".$picker->nCpId;?>" class="edit"><img src="<?php echo $edit;?>" /></a> &nbsp;
                  <a onclick="return confirm('Are you sure want to delete?');" href="<?php echo $this->filepath."&mode=delete&id=".$picker->nCpId;?>" class="delete"><img src="<?php echo $delete;?>" /></a></div>
          </div>
          <?php
          } ?>
      </div>
<?php
    }
  }
}
?>

