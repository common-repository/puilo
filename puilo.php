<?php
/*
 * Plugin Name:			Puilo
 * Plugin URI:			https://www.helper-wp.com/plugins/puilo/
 * Description:			This plugin replaces the surname of Russian dictator Vladimir Putin in WordPress publications. More see <a href="https://en.wikipedia.org/wiki/Putin_khuilo!" target="_blank">Putin khuilo!</a> in Wikipedia.
 * Version:				1.3.1
 * Requires at least:	4.8.3
 * Requires PHP:		5.6
 * Author:				Webamator
 * Author URI:			https://www.helper-wp.com/wordpress-freelancer/
 * License:				GPL v2 or later
 * License URI:			http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:			puilo
 * Domain Path:			/languages/
*/

/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	load_plugin_textdomain( 'puilo', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	register_activation_hook(__FILE__, 'puilo_set_options');
	register_deactivation_hook(__FILE__, 'puilo_unset_options');

	add_action('admin_menu', 'puilo_admin_page');
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'puilo_action_links' );
	add_filter('the_title', 'puilo_mod_title');
	add_filter('the_content', 'puilo_mod_content');


	function puilo_set_options() {
	
		add_option('puilo_version', '1.3.1');
		add_option('puilo_modify_title', 0);
		add_option('puilo_modify_content', 1);
		add_option('puilo_modify_word', 'puilo');

	}


	function puilo_unset_options() {

		delete_option('puilo_version');
		delete_option('puilo_modify_title');
		delete_option('puilo_modify_content');
		delete_option('puilo_modify_word');
	}


	function puilo_admin_page() {
	
		$puiloSettings = __( 'Puilo Settings', 'puilo' );
		add_options_page($puiloSettings, $puiloSettings, 'manage_options', __FILE__, 'puilo_options_page');

	}

	function puilo_action_links( $links ) {

		$links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=puilo/puilo.php') ) .'">'.__( 'Settings', 'puilo' ).'</a>';
		return $links;

	}

	
	function puilo_mod_title($title){
    
		if (get_option('puilo_modify_title')) {
        
			$puiloModifyWord = get_option('puilo_modify_word');
			
			$puiloList = array("Putin", "putin");
			
			$puiloWordEn = ($puiloModifyWord == 'пу') ? 'puilo' : 'khuilo' ;
			
			$title = str_replace($puiloList,$puiloWordEn,$title);
			
			$title = puilo_mod_data($title, $puiloModifyWord);
	
		}
	
		return $title;

	}


	function puilo_mod_content($content){
    
		if (get_option('puilo_modify_content')) {
		
			$puiloModifyWord = get_option('puilo_modify_word');

			$puiloList = array("Putin", "putin");
			
			$puiloWordEn = ($puiloModifyWord == 'пу') ? 'puilo' : 'khuilo' ;

			$content = str_replace($puiloList,$puiloWordEn,$content);
			
			$content = puilo_mod_data($content, $puiloModifyWord);

	
		}
	
		return $content;

	}


	function puilo_mod_data($data, $fp){
	
		$puiloArray = array(
			"путин" => $fp."йло", "путін" => $fp."йло", "Путин" => $fp."йло", "Путін" => $fp."йло", 
			"путина" => $fp."йла", "путіна" => $fp."йла", "Путина" => $fp."йла", "Путіна" => $fp."йла",
			"путину" => $fp."йлу", "путіну" => $fp."йлу", "Путину" => $fp."йлу", "Путіну" => $fp."йлу",
			"путиным" => $fp."йлом", "путіним" => $fp."йлом", "Путиным" => $fp."йлом", "Путіним" => $fp."йлом",
			"путине" => $fp."йле", "путіні" => $fp."йлі", "Путине" => $fp."йле", "Путіні" => $fp."йлі"
			);
				
		$data = strtr($data, $puiloArray);
				
		return $data;
	
	}


	function puilo_options_page() {

		$puiloModifyTitle = get_option('puilo_modify_title');
		$puiloModifyContent = get_option('puilo_modify_content');
		$puiloModifyWord = get_option('puilo_modify_word');


		if ( ! isset( $_REQUEST['settingsUpdated'] ) )
			$_REQUEST['SettingsUpdated'] = false;

		if ( ! isset( $_REQUEST['defaultSettings'] ) )
			$_REQUEST['DefaultSettings'] = false;

		if ( ! isset( $_REQUEST['puiloModifyTitle'] ) )
			$_REQUEST['puiloModifyTitle'] = false;

		if ( ! isset( $_REQUEST['puiloModifyContent'] ) )
			$_REQUEST['puiloModifyContent'] = false;
			
		if ( ! isset( $_REQUEST['puiloModifyWord'] ) )
			$_REQUEST['puiloModifyWord'] = 'puilo';


		if ( isset ($_REQUEST['settingsUpdated']) && $_REQUEST['settingsUpdated'] == true ){
		
			update_option('puilo_modify_title', $_REQUEST['puiloModifyTitle']);
			update_option('puilo_modify_content', $_REQUEST['puiloModifyContent']);
			update_option('puilo_modify_word', $_REQUEST['puiloModifyWord']);
?>
			<div class="updated"><p><strong> <?php _e('Your settings are saved', 'puilo'); ?></strong></p></div>
<?php
		}
		
		if ( isset ($_REQUEST['defaultSettings']) && $_REQUEST['defaultSettings'] == true ){

			update_option('puilo_modify_title', false);
			update_option('puilo_modify_content', true);
			update_option('puilo_modify_word', 'puilo');
?>
			<div class="updated"><p><strong> <?php _e('Your settings are dropped', 'puilo'); ?></strong></p></div>
<?php
		}
?>

		<div class="wrap">
		
			<h2 id="title"><?php _e( 'Puilo Settings', 'puilo' ) ?></h2>
			
			<form method="post" action="<? echo $_SERVER['REQUEST_URI'];?>">
			
				<table>
				
				<tr>
				    <td>
					<input name="puiloModifyTitle" type="checkbox" <?php if($puiloModifyTitle || $_REQUEST['puiloModifyTitle']) echo "checked";?>> <?php echo __('Replaces in post title', 'puilo'); ?>
					</td>
				</tr>
				
				<tr>
				    <td>
					<input name="puiloModifyContent" type="checkbox" <?php if($puiloModifyContent || $_REQUEST['puiloModifyContent']) echo "checked";?>> <?php echo __('Replaces in post content', 'puilo'); ?>
					</td>
				</tr>

				<tr>
				    <td>
					<?php _e('Replace on', 'puilo') ?>: <select name="puiloModifyWord">
						<option class="level-0" value="пу" <?php print ($puiloModifyWord == "пу" || $_REQUEST['puiloModifyWord'] == "пу") ? "selected" : false ?>>puilo/пуйло</option>
						<option class="level-0" value="х@" <?php print ($puiloModifyWord == "х@" || $_REQUEST['puiloModifyWord'] == "х@") ? "selected" : false ?>>khuilo/х@йло</option>
					</select>
					</td>
				</tr>
				
				</table>
			
				<input type="submit" class="button button-primary" name="settingsUpdated" value="<?php _e('Save Changes', 'puilo') ?>" />
				
				<input type="submit" class="button button-primary" name="defaultSettings" value="<?php _e('Default Settings', 'puilo') ?>" />
			
			</form>
		
		</div>

<?php 
	}
?>