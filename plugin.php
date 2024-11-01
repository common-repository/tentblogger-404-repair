<?php
/*
Plugin Name: TentBlogger 404 Repair
Plugin URI: http://tentblogger.com/404-plugin
Description: Repair 404 Errors WordPress Plugin simply notifies you of any page or file requests that a user (or search engine) makes to your blog.
Version: 2.4
Author: TentBlogger
Author URI: http://tentblogger.com
License:

    Copyright 2011 - 2013 TentBlogger (info@tentblogger.com)

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

class TentBlogger_404_Repair {
	 
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	public function __construct() {
	
	    $this->init_plugin_constants();
	    
	    $this->load_file(REPAIR_PLUGIN_SLUG, '/tentblogger-404-repair/css/admin.css');
	    $this->load_file(REPAIR_PLUGIN_SLUG, '/tentblogger-404-repair/js/admin.js', true);
  
		load_plugin_textdomain(REPAIR_PLUGIN_LOCALE, false, dirname(plugin_basename(__FILE__)) . '/lang');
		
	    add_action('admin_menu', array($this, 'admin'));
	    add_filter('wp_head', array($this, 'locate_404s'));
    
	} // end constructor
	
	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/
	
  /**
   * Adds the plugin menu to the TentBlogger plugin menu.
   */
  public function admin() {
    if(is_admin()) {
      if(!$this->my_menu_exists('tentblogger-handle')) {
        add_menu_page('TentBlogger', 'TentBlogger', 'administrator', 'tentblogger-handle', array($this, 'display'));
      } // end if
      add_submenu_page('tentblogger-handle', 'TentBlogger', REPAIR_PLUGIN_NAME, 'administrator', REPAIR_PLUGIN_SLUG, array($this, 'display'));
    } // end if
  } // end admin
  
  /**
   * Loads the admin dashboard. Triggers the page removal if the query string variable
   * is specified.
   */
  public function display() {
  
    if(isset($_GET['remove_page'])) {
      $this->repair_page($_GET['remove_page']);
    } // end if
  
    include_once('views/admin.php');
  } // end display

  /**
   * If the current page is a 404, log it.
   */
	public function locate_404s($content) {
    if(is_404()) {
      $url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
      $this->log_bad_url($url);
    } // end if
	} // end action_method_name
  
  
	/*--------------------------------------------*
	 * Private Functions
	 *---------------------------------------------*/
  
  /**
   * Writes the incoming URL to the options array.
   *
   * @url The URL to log.
   */
  private function log_bad_url($url) {
    $options = get_option(REPAIR_PLUGIN_SLUG);
    $options[$url] = $url;
    update_option(REPAIR_PLUGIN_SLUG, $options);  
  } // end save_page_to_database
  
  /**
   * Loads the missing pages in the admin dashboard and builds the table displaying the pages
   * and the removal links.
   */
  private function load_missing_pages() {
    
    $options = get_option(REPAIR_PLUGIN_SLUG);

    if(count($options) == 0 || $options == null) {
      $output = '<p>' . __("No 404's have been found on your site!", REPAIR_PLUGIN_LOCALE) . '</p>';
    } else {
    
      $output = '<table id="repair-list" class="wp-list-table widefat fixed posts">';
        $output .= '<thead>';
          $output .= '<tr>';
            $output .= '<th class="manage-column">';
              $output .= __('Missing URL', REPAIR_PLUGIN_LOCALE);
            $output .= '</th>';
            $output .= '<th class="manage-column">';
              $output .= __('Mark as Repaired', REPAIR_PLUGIN_LOCALE);
            $output .= '</th>';
          $output .= '</tr>';
        $output .= '</thead>';
        
        $output .= '<tbody id="the-list">';
          foreach($options as $url => $id) {
            $output .= '<tr>';
              $output .= '<td>';
                $output .= $url;
              $output .= '</td>';
              $output .= '<td>';
                $output .= '<a href="javascript:;" class="repair-link ' . $url . '">';
                  $output .= __('Repaired', REPAIR_PLUGIN_LOCALE);
                $output .= '</a>';
              $output .= '</td>';
            $output .= '</tr>';
          } // end foreach
        $output .= '</tbody>';
      $output .= '</table>';
    
    } // end if/else
    
    return $output;
    
  } // end load_missing_pages
  
  /**
   * Removes the incoming URL from the options.
   *
   * @incoming_url  The URL to remove from the options
   */
  private function repair_page($incoming_url) {
    
    $options = get_option(REPAIR_PLUGIN_SLUG);
    
    // if the incoming_url is 'all' then unset everything
    if(trim(strtolower(stripslashes($incoming_url))) == 'all') {
    
      foreach($options as $opt) {
        unset($options[$opt]);
      } // end foreach
      $options = array();
      
    // otherwise remove the url that's specified
    } else {
    
      foreach($options as $opt => $url) {
        if(trim(strtolower($url)) == stripslashes($incoming_url)) {
          unset($options[$opt]);
        } // end if
      } // end foreach
    
    } // end if/else
    
    update_option(REPAIR_PLUGIN_SLUG, $options);  
    
  } // end repair_page
  
  /**
   * @returns Whether or not there are actually any 404's found.
   */
  private function has_404s() {
    return get_option(REPAIR_PLUGIN_SLUG) != null && count(get_option(REPAIR_PLUGIN_SLUG)) > 0;
  } // end has_404s
  
  /**
   * Initializes constants used for convenience throughout 
   * the plugin.
   */
  private function init_plugin_constants() {
    
    if(!defined('REPAIR_PLUGIN_LOCALE')) {
      define('REPAIR_PLUGIN_LOCALE', 'tentblogger-404-repair-locale');
    } // end if

    if(!defined('REPAIR_PLUGIN_NAME')) {
      define('REPAIR_PLUGIN_NAME', '404 Repair');
    } // end if
    
    if(!defined('REPAIR_PLUGIN_SLUG')) {
      define('REPAIR_PLUGIN_SLUG', 'tentblogger-404-repair-slug');
    } // end if
  
  } // end init_plugin_constants
	
	/**
	 * Helper function for registering and loading scripts and styles.
	 *
	 * @name	The 	ID to register with WordPress
	 * @file_path		The path to the actual file
	 * @is_script		Optional argument for if the incoming file_path is a JavaScript source file.
	 */
	private function load_file($name, $file_path, $is_script = false) {
		$url = WP_PLUGIN_URL . $file_path;
		$file = WP_PLUGIN_DIR . $file_path;
		if(file_exists($file)) {
			if($is_script) {
				wp_register_script($name, $url);
				wp_enqueue_script($name);
			} else {
				wp_register_style($name, $url);
				wp_enqueue_style($name);
			} // end if
		} // end if
	} // end _load_file
  
  /**
   * http://wordpress.stackexchange.com/questions/6311/how-to-check-if-an-admin-submenu-already-exists
   */
  private function my_menu_exists( $handle, $sub = false){
    if( !is_admin() || (defined('DOING_AJAX') && DOING_AJAX) )
      return false;
    global $menu, $submenu;
    $check_menu = $sub ? $submenu : $menu;
    if( empty( $check_menu ) )
      return false;
    foreach( $check_menu as $k => $item ){
      if( $sub ){
        foreach( $item as $sm ){
          if($handle == $sm[2])
            return true;
        }
      } else {
        if( $handle == $item[2] )
          return true;
      }
    }
    return false;
  } // end my_menu_exists
  
} // end class
new TentBlogger_404_Repair();
?>