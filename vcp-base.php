<?php
/*
Plugin Name: Vkontakte Cut Post Part
Plugin URI: http://zorge.biz/vk-cut/
Description: Plugin allows to hide part of any posts (or pages), which will be available to the user after he clicks a button "I like" (vKontakte) on this post (page)
Version: 1.0.1
Author: Mikhail Zorge
Author URI: http://zorge.biz
License: GPL2
*/

/*  Copyright 2011  Mikhail Zorge  (email : zorgebiz@hotmail.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

  load_plugin_textdomain( 'vkontakte-cut-post', false, dirname(plugin_basename( __FILE__ )) . '/lang/');

function vcp_shortcode ($atts = array(), $content = '') {
  global $post;
  $vcp = false;
  
  if (!empty($atts)) extract ($atts);
    
  $options = get_option('vcp_options');
  $vcp_text = (isset($text) && !empty($text)) ? $text : $options['vcp_default_text'];
  
  if (!empty($_COOKIE['vkcut'])) {
    $vkcut_arr = explode ("|", $_COOKIE['vkcut']); 
    if (in_array($post->ID, $vkcut_arr))
      $vcp = true;
  }
  
  if ($vcp)
    return $content;
  else
    return '<a href = "javascript:void(0);" class = "vcp_button" rel = "#vcp_facebox">'.$vcp_text.'</a>';
}
add_shortcode('vkcut', 'vcp_shortcode');



add_action('wp_footer','vcp_overlay',20); 
function vcp_overlay() {
  $options = get_option('vcp_options');
  
  ?> 
  <div id="vcp_facebox" >
    <div class = "fb_inner">
      <h2><?php echo $options['vcp_popup_title']; ?></h2>
      <p><?php echo $options['vcp_popup_text']; ?></p>
      <div id="vcp_vk_like"></div>
      <div class = "vcp_buttons">
        <input type="button" value="<?php _e('Refresh', 'vkontakte-cut-post'); ?>" class="vcp_reload" />
        &nbsp;&nbsp;
        <input class="close vcp_cancel" type = "button" value = "<?php _e('Cancel', 'vkontakte-cut-post'); ?>" />
      </div>
    </div>
  </div>
<?php 
 
};


register_activation_hook(__FILE__,'vcp_activate');
function vcp_activate (){
  
  $options = get_option('vcp_options');
  
  if (!$options['vcp_default_text']) 
    $options['vcp_default_text'] = 'Click to see hidden text';
  
  if (!$options['vcp_popup_title'])
    $options['vcp_popup_title'] = 'Show Hidden Text';
  
  if (!$options['vcp_popup_text'])
    $options['vcp_popup_text'] = 'To see hidden text, click on "I like" button, and then refresh the page';
    
  add_option('vcp_options', $options);  
}

add_action( 'init', 'vcp_init');
function vcp_init () {

  add_action( 'wp_head', 'vcp_options' );
  add_action( 'wp_enqueue_scripts', 'vcp_scripts' );
  add_action( 'wp_print_styles', 'vcp_styles' );
}

function vcp_scripts() {
  
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-tools', 'http://cdn.jquerytools.org/1.2.6/tiny/jquery.tools.min.js', array('jquery'));  
  wp_enqueue_script('jquery-cookie', plugins_url('/inc/jquery.cookie.min.js',__FILE__), array('jquery'));  
  wp_enqueue_script('jquery-vcp', plugins_url('/inc/jquery.vcp.min.js',__FILE__), array('jquery', 'jquery-tools'));
  
}

function vcp_styles() {
  
  $myStyleUrl = plugins_url('/inc/style.css', __FILE__); 
  $myStyleFile = WP_PLUGIN_DIR . '/vkontakte-cut-post/inc/style.css';  
  
  if ( file_exists($myStyleFile) ) {
    wp_register_style('vcp-style', $myStyleUrl);
    wp_enqueue_style( 'vcp-style');
  }

} 


add_action( 'admin_init', 'vcp_init_admin');
function vcp_init_admin () {

  add_action( 'admin_print_scripts', 'vcp_quicktags' );
  add_filter('mce_external_plugins', "vcp_register");
  add_filter('mce_buttons', 'vcp_add_button', 0);
}

function vcp_quicktags() {
  wp_enqueue_script( 'vcp_quicktags', plugins_url( '/mce-buttons/vcp-quicktags.js',__FILE__ ), array( 'quicktags')); 
}

function vcp_add_button($buttons) {
    array_push($buttons, "separator", "vcp_name");
    return $buttons;
}

function vcp_register($plugin_array) {
    $plugin_array['vcp_name'] = plugins_url( '/mce-buttons/vcp-mce-button.js',__FILE__ );
    return $plugin_array;
}


function vcp_options() { 
  global $post;
  
  $options = get_option('vcp_options');
  $vcp_app_id = $options['vcp_app_id'];
   
  ?>
  <script src="http://vkontakte.ru/js/api/openapi.js" type="text/javascript"></script>  
  <script type="text/javascript">
    /* <![CDATA[ */   
      VK.init({
          apiId: <?php echo $vcp_app_id; ?>,
          onlyWidgets: true
      });    
      
      var postId = <?php echo $post->ID; ?>;
      var vkcut = '';

    /* ]]> */
  </script>  
  <?php
}



// plugin row links
add_filter('plugin_row_meta', 'vcp_donate_link', 10, 2);
function vcp_donate_link($links, $file) {
  if ($file == plugin_basename(__FILE__)) {
    $links[] = '<a href="'.admin_url('options-general.php?page=vcp').'">'.__('Settings', 'vkontakte-cut-post').'</a>';
    $links[] = '<a href="#">'.__('Donate', 'vkontakte-cut-post').'</a>';
  }
  return $links;
}

// action links
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'vcp_settings_link', 10, 1);
function vcp_settings_link($links) {
  $links[] = '<a href="'.admin_url('options-general.php?page=vcp').'">'.__('Settings', 'vkontakte-cut-post').'</a>';
  return $links;
}

// add the admin options page
add_action('admin_menu', 'vcp_admin_add_page');
function vcp_admin_add_page() {
  global $vcp_options_page;
  $vcp_options_page = add_options_page(__('vKontakte Cut Post Options', 'vkontakte-cut-post'), __('vKontakte Cut Post', 'vkontakte-cut-post'), 'manage_options', 'vcp', 'vcp_options_page');
}


// add the admin settings and such
add_action('admin_init', 'vcp_admin_init'); 
function vcp_admin_init(){
  $options = get_option('vcp_options');
  if (empty($options['vcp_app_id'])) {
    add_action('admin_notices', create_function( '', "echo '<div class=\"error\"><p>".sprintf(__('vKontakte Cut Post needs configuration information on its <a href="%s">settings</a> page.', 'vkontakte-cut-post'), admin_url('options-general.php?page=vcp'))."</p></div>';" ) );
  }
  wp_enqueue_script('jquery');
  register_setting( 'vcp_options', 'vcp_options', 'vcp_options_validate' );
  add_settings_section('vcp_main', __('Main Settings', 'vkontakte-cut-post'), 'vcp_section_text', 'vcp');
  if (!defined('VCP_APP_ID')) add_settings_field('vcp_app_id', __('vKontakte App Id', 'vkontakte-cut-post'), 'vcp_setting_app_id', 'vcp', 'vcp_main');
  add_settings_field('vcp_default_text', __('Default Text', 'vkontakte-cut-post'), 'vcp_setting_default_text', 'vcp', 'vcp_main');
  add_settings_field('vcp_popup_title', __('Pop-Up title', 'vkontakte-cut-post'), 'vcp_setting_popup_title', 'vcp', 'vcp_main');
  add_settings_field('vcp_popup_text', __('Pop-Up text', 'vkontakte-cut-post'), 'vcp_setting_popup_text', 'vcp', 'vcp_main');
}


// display the admin options page
function vcp_options_page() {
?>
  <div class="wrap">
  <h2><?php _e('vKontakte Cut Post Options','vkontakte-cut-post'); ?></h2>
  <p><?php _e('This plugin allows you to hide part of any posts (or pages), which will be available to the user after he clicks a button "I like" (vKontakte) on this post (page).','vkontakte-cut-post'); ?></p>
  <form method="post" action="options.php">
  <?php settings_fields('vcp_options'); ?>
  <table><tr><td style='vertical-align:top;'>
  <?php do_settings_sections('vcp'); ?>
  </td><td style='vertical-align:top;'>
  <div style='width:20em; float:right; background: #ffc; border: 1px solid #333; margin: 2px; padding: 5px'>
    <h3 align='center'><?php _e('Need More Features?','vkontakte-cut-post'); ?></h3>
    <p><?php  printf(__('Please contact with me via <a href="%s" >email</a>.','vkontakte-cut-post'), 'mailto:zorgebiz@hotmail.com?subject=NeedMore'); ?></p>
    <h3 align='center'><?php _e('About the Author','vkontakte-cut-post'); ?></h3>
    <p><?php  printf(__('<a href="%s" target"_blank">vKontakte Cut Post</a> is developed and maintained by <a href="%s" target = "_blank">Mikhail Zorge</a>.','vkontakte-cut-post'), 'http://zorge.biz/vk-cut/', 'http://zorge.biz'); ?></p>
    </div>
    <div style = "width:20em; float:right; border: 1px solid #333; margin: 2px; padding: 5px;">
      <h3 align='center'><?php _e('News','vkontakte-cut-post'); ?></h3>
      <?php wp_widget_rss_output('http://feeds2.feedburner.com/zorgebiz',array('show_date' => 1, 'items' => 6) ); ?>
    </div>
  </td></tr></table>
  <p class="submit">
  <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
  </p>
  </form>

  </div>

<?php
}

function vcp_setting_app_id() {
  if (defined('VCP_APP_ID')) return;
  $options = get_option('vcp_options');
  echo "<nobr><input type='text' id='vcpappid' name='vcp_options[vcp_app_id]' value='{$options['vcp_app_id']}' size='40' />&nbsp;(".__('required', 'vkontakte-cut-post').")</nobr>";
  printf(__('<p>Get vKontakte appId on <a href = "%s" target = "_blank">link</a>.</p>', 'vkontakte-cut-post'), 'http://vkontakte.ru/editapp?act=create&site=1');  
}

function vcp_setting_default_text() {
  $options = get_option('vcp_options');
  echo "<input type='text' id='vcpdefaulttext' name='vcp_options[vcp_default_text]' value='{$options['vcp_default_text']}' size='40' />";
  echo '<p>'.__('Text to be displayed instead of hidden part of post', 'vkontakte-cut-post').'.</p>';  
}

function vcp_setting_popup_title() {
  $options = get_option('vcp_options');
  echo "<input type='text' id='vcppopuptitle' name='vcp_options[vcp_popup_title]' value='{$options['vcp_popup_title']}' size='40' />";
  echo '<p>'.__('Title of Pop-Up window', 'vkontakte-cut-post').'.</p>';  
}

function vcp_setting_popup_text() {
  $options = get_option('vcp_options');
  echo "<textarea rows='3' cols='40' id='vcppopuptext' name='vcp_options[vcp_popup_text]'>{$options['vcp_popup_text']}</textarea>";
  echo '<p>'.__('Text in Pop-Up window', 'vkontakte-cut-post').'.</p>';  
}

// validate our options
function vcp_options_validate($input) {
  if (!defined('VCP_APP_ID')) {
    $input['vcp_app_id'] = trim($input['vcp_app_id']);
    if(! preg_match('/^[0-9]+$/i', $input['vcp_app_id'])) {
      $input['vcp_app_id'] = '';
    }
  }
  return $input;
}

add_action('contextual_help', 'vcp_plugin_help', 10, 3);
function vcp_plugin_help($contextual_help, $screen_id, $screen) {

  global $vcp_options_page;
  if ($screen_id == $vcp_options_page) {

    $home = home_url('/');
    $contextual_help = __("<p><strong>Summary</strong></p>
    <p>This plugin allows you to hide part of any posts (or pages), which will be available to the user after he clicks a button \"I like\" (vKontakte) on this post (page). Instead, this text user see the link, click that opens a window with instructions to be executed to see the hidden text.</p>
    <p><strong>How to hide the text</strong></p>    
    <ol>
    <li>The text that you want to hide you must enclose the tag: <b>[vcut] hidden text [/vcut]</b>. You can also use the button in the visual editor or quicktag.</li>
    <li>An optional parameter <em>text</em> to specify the text to be displayed instead of hidden. For example, <b>[vcut text = 'Click Me'] hidden text [/vcut]</b>.</li>
    </ol>", 'vkontakte-cut-post');
  }
  return $contextual_help;
}

function vcp_section_text() {
  $options = get_option('vcp_options');
  
  if (empty($options['vcp_app_id'])) {
    _e("<p><strong>Summary</strong></p>
    <p>This plugin allows you to hide part of any posts (or pages), which will be available to the user after he clicks a button \"I like\" (vKontakte) on this post (page). Instead, this text user see the link, click that opens a window with instructions to be executed to see the hidden text.</p>
    <p><strong>How to hide the text</strong></p>    
    <ol>
    <li>The text that you want to hide you must enclose the tag: <b>[vcut] hidden text [/vcut]</b>. You can also use the button in the visual editor or quicktag.</li>
    <li>An optional parameter <em>text</em> to specify the text to be displayed instead of hidden. For example, <b>[vcut text = 'Click Me'] hidden text [/vcut]</b>.</li>
    </ol>", 'vkontakte-cut-post');
  }
}
