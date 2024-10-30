<?php
/*
Plugin Name: LH Multisite User Bio
Plugin URI: https://lhero.org/plugins/lh-multisite-user-bio/
Description: Allows users on a multisite to have different biographies or description on different sites
Author: Peter Shaw
Version: 1.03
Author URI: https://shawfactor.com/
Network: True
*/


if (!class_exists('LH_Multisite_user_bio_plugin')) {

class LH_Multisite_user_bio_plugin {

var $namespace = 'lh_multisite_user_bio';


public function add_bio_fields( $user ) { 

if (!is_network_admin() ) {

$content = get_user_option( $this->namespace."-site_specific-bio", $user->ID );

?>

	<h3>Bio Fields</h3>

	<table class="form-table">

		<tr>
			<th><label for="<?php echo $this->namespace."-site_specific-bio";   ?>">Your Bio</label></th>

			<td>
<?php 
 $settings = array( 'media_buttons' => false );
  
  wp_editor( $content, $this->namespace."-site_specific-bio", $settings); ?><br />
				<span class="description">Enter your site specific bio.</span>
<?php wp_nonce_field( $this->namespace."-site_specific-bio-nonce", $this->namespace."-site_specific-bio-nonce"); ?>
			</td>
		</tr>

	</table>
<?php
}
 }



public function save_bio_fields( $user_id ) {

if ( current_user_can( 'edit_user', $user_id ) and isset($_POST[$this->namespace.'-site_specific-bio']) and wp_verify_nonce( $_POST[$this->namespace."-site_specific-bio-nonce"], $this->namespace."-site_specific-bio-nonce") ){

$new_value = wp_kses_post($_POST[$this->namespace.'-site_specific-bio']);
	
update_user_option( $user_id, $this->namespace."-site_specific-bio", $new_value );

}
}


public function modifyMeta( $null, $object_id, $meta_key, $single ){

if (($meta_key === 'description') and !is_admin()){

$bio = get_user_option( $this->namespace."-site_specific-bio", $object_id );

if ($bio != ''){

return $bio;


} else {

return $null;

}



} else {

return $null;

}
    }



public function __construct() {


add_action( 'show_user_profile', array($this,'add_bio_fields') );
add_action( 'edit_user_profile', array($this,'add_bio_fields') );

add_action( 'personal_options_update', array($this,'save_bio_fields') );
add_action( 'edit_user_profile_update', array($this,'save_bio_fields') );

add_filter('get_user_metadata', array($this, 'modifyMeta'), 10, 4 );


}

}


$lh_multisite_user_bio_instance = new LH_Multisite_user_bio_plugin();

}


?>