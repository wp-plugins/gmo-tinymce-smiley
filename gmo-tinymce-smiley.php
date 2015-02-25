<?php
/*
Plugin Name: GMO TinyMCE Smiley
Plugin URI: https://www.wpcloud.jp/en/themes
Description: This is a plug-in that can be inserted emoticons easily.

Version: 1.1
Author: GMO WP Cloud
Author URI: https://www.wpcloud.jp/en/
Text Domain: gmo-tinymce-smiley
Domain Path: /languages/
*/

new gmoTinymceSmiley();

class gmoTinymceSmiley {
	public function __construct() {
		add_action( 'plugins_loaded', array(&$this, 'init') );
		add_action( 'plugins_loaded', array(&$this, 'load_textdomain') );
		add_action( 'admin_head-post.php', array(&$this, 'mce_style') );
		add_action( 'admin_head-post-new.php', array(&$this, 'mce_style') );
		add_action( 'admin_menu', array(&$this, 'plugin_menu') );
	}
	
	public function init() {
		add_action( 'init', array(&$this, 'add_buttons') );
	}
	
	public function add_buttons() {
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) 
			return;
		
		if ( get_user_option('rich_editing') == 'true' ) {
			add_filter( 'mce_external_plugins', array(&$this, 'mce_external_plugins') );
			add_filter( 'mce_buttons_2', array(&$this, 'mce_buttons') );
			add_filter( 'tiny_mce_before_init', array(&$this, 'mce_before_init') );
		}
	}
	public function mce_external_plugins( $plugin_array ) {
		$plugin_array['gmo_tinymce_smiley'] = plugins_url( 'editor_plugin.js', __FILE__ );
		return $plugin_array;
	}
	public function mce_buttons( $buttons ) {
		array_push( $buttons, 'gmo-tinymce-smiley' );
		return $buttons;
	}
	public function mce_before_init( $init_array ) {
		$emotion = get_option('emotion');
		if ( !empty($emotion) ) {
			$emotion = trim( $emotion );
			$emotion = str_replace( array("\r\n", "\r"), "\n", $emotion );
			$emotion = explode( "\n", $emotion );
			$emotion = json_encode($emotion);
		} else {
			$emotion = $this->get_default_emotion('json');
		}
		$init_array['smiley_emotion'] = $emotion;
		return $init_array;
	}
	
	public function load_textdomain() {
		load_plugin_textdomain( 'gmo-tinymce-smiley', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	public function mce_style() {
		wp_enqueue_style( 'gmo-tinymce-smiley-style', plugins_url('css/gmo-tinymce-smiley.min.css', __FILE__) );
	}
	public function plugin_menu() {
		add_posts_page( 'setting smiley', 'TinyMCE Smiley', 8, 'gmo-tinymce-smiley', array(&$this, 'plugin_options') );
	}
	public function plugin_options() {
		wp_enqueue_style( 'gmo-tinymce-smiley-update', plugins_url('css/gmo-tinymce-smiley-update.min.css', __FILE__) );
		wp_enqueue_script( 'gmo-tinymce-smiley-update', plugins_url('js/gmo-tinymce-smiley-update.js', __FILE__) );
		
	    $params = array();
	    
	    $params['title'] = 'GMO TinyMCE Smiley';
		
		$params['default']['emotion'] = $this->get_default_emotion();
		
		if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
			$param['message'] = '<div id="message" class="updated below-h2"><p>'.__( 'complete!', 'gmo-tinymce-smiley' ).'</p></div>';
			
			if ( empty($_POST['regist']['emotion']) ) {
				$params['regist']['emotion'] = $params['default']['emotion'];
			} else {
				$params['regist']['emotion'] = stripslashes($_POST['regist']['emotion']);
			}
			update_option( 'emotion', $params['regist']['emotion'] );
		} else {
			$param['message'] = '';
			$params['regist']['emotion'] = get_option('emotion');
			if ( empty($params['regist']['emotion']) ) {
				$params['regist']['emotion'] = $params['default']['emotion'];
			}
		}
		$params['content']  = '<div id="gmoTinymceSmiley">';
		$params['content'] .= '<form name="tinymcesmiley" method="post" action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ).'">';
		$params['content'] .= '<textarea id="gmo_tinymce_emotion" name="regist[emotion]">'.htmlspecialchars( $params['regist']['emotion'], ENT_QUOTES, 'utf-8' ).'</textarea><br>';
		$params['content'] .= '<input type="hidden" name="default_emotion" value="'.htmlspecialchars( $params['default']['emotion'], ENT_QUOTES, 'utf-8' ).'">';
		$params['content'] .= '<button type="button" name="mode" value="reset" class="button" onclick="reset_emotion()">'.__( 'reset', 'gmo-tinymce-smiley' ).'</button>';
		$params['content'] .= '<button type="submit" name="mode" value="submit" class="button-primary">'.__( 'submit', 'gmo-tinymce-smiley' ).'</button>';
		$params['content'] .= '</form>';
		$params['content'] .= '</div>';
		
	    wp_enqueue_style( 'gmo-page-transitions-style', plugins_url('css/gmo-page-transitions.min.css', __FILE__) );
		$params['gmotransitions']['image']['url1'] = plugins_url('images/wpshop_logo.png', __FILE__);
		$params['gmotransitions']['image']['url2'] = plugins_url('images/wpshop_bnr_themes.png', __FILE__);
		$params['gmotransitions']['image']['url3'] = plugins_url('images/wpshop_bnr_plugins.png', __FILE__);
		
		echo <<<EOD
<div id="gmotransitions" class="wrap">
<h2>{$params['title']}</h2>
{$param['message']}
<div id="gmoplugLeft">
{$params['content']}
</div>
<!-- #gmoplugLeft -->
<div id="gmoplugRight">
<h3>WordPress Themes</h3>
<ul>
<li><a href="https://wordpress.org/themes/kotenhanagara" target="_blank">Kotehanagara</a></li>
<li><a href="https://wordpress.org/themes/madeini" target="_blank">Madeini</a></li>
<li><a href="https://wordpress.org/themes/azabu-juban" target="_blank">Azabu Juban</a></li>
<li><a href="http://wordpress.org/themes/de-naani" target="_blank">de naani</a></li>
</ul>
<a href="http://wpshop.com/themes?=vn_wps_pagetrasitions" target="_blank"><img src="{$params['gmotransitions']['image']['url2']}" alt="WPShop by GMO WordPress Themes for Everyone!"></a>
<ul><li class="bnrlink"><a href="http://wpshop.com/themes?=wps_pagetrasitions" target="_blank">Visit WP Shop Themes</a></li></ul>
<h3>WordPress Plugins</h3>
<ul>
<li><a href="http://wordpress.org/plugins/gmo-showtime/" target="_blank">GMO Showtime</a></li>
<li><a href="http://wordpress.org/plugins/gmo-font-agent/" target="_blank">GMO Font Agent</a></li>
<li><a href="http://wordpress.org/plugins/gmo-share-connection/" target="_blank">GMO Share Connection</a></li>
<li><a href="http://wordpress.org/plugins/gmo-ads-master/" target="_blank">GMO Ads Master</a></li>
<li><a href="http://wordpress.org/plugins/gmo-page-transitions/" target="_blank">GMO Page Trasitions</a></li>
<li><a href="http://wordpress.org/plugins/gmo-go-to-top/" target="_blank">GMO Go to Top</a></li>
</ul>
<a href="http://wpshop.com/plugins?=vn_wps_pagetrasitions" target="_blank"><img src="{$params['gmotransitions']['image']['url3']}" alt="WPShop by GMO WordPress Plugins for Everyone!"></a>
<ul><li class="bnrlink"><a href="http://wpshop.com/plugins?=wps_pagetrasitions" target="_blank">Visit WP Shop Plugins</a></li></ul>
<h3>Contact Us</h3>
<a href="http://support.wpshop.com/?page_id=15" target="_blank"><img src="{$params['gmotransitions']['image']['url1']}" alt="WPShop by GMO"></a>
</div><!-- #gmoplugRight -->
</div>
<!-- #gmotransitions -->
EOD;
	}
	
	private function read_file( $file ){
		$source = @file_get_contents(plugins_url( $file, __FILE__) );
		$source = trim( $source );
		$source = str_replace( array("\r\n", "\r"), "\n", $source );
		$source = explode( "\n", $source );
		return $source;
	}
	private function get_default_emotion( $type = 'text' ) {
	    $params = array();
		
		$params['file']['common'] = "emotion/original_emotion.txt";
		$params['file']['ja'] = "emotion/original_emotion_ja.txt";
		
		$params['emotion']['common'] = $this-> read_file($params['file']['common']);
		$params['emotion']['default'] = $params['emotion']['common'];
		
		if (get_locale() == 'ja') {
			$params['emotion']['ja'] = $this-> read_file($params['file']['ja']);
			$params['emotion']['default'] = array_merge( $params['emotion']['common'], $params['emotion']['ja'] );
		}
		
		switch($type) {
			case 'text':
				$params['emotion']['default'] = implode( "\r\n", $params['emotion']['default'] );
				break;
			
			case 'json':
				$params['emotion']['default'] = json_encode($params['emotion']['default']);
				break;
			
			case 'array':
				break;
		}
		
		return $params['emotion']['default'];
	}
}
