<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Soundcloud_Importer
 *
 * @wordpress-plugin
 * Plugin Name:       Soundcloud Importer
 * Plugin URI:        http://example.com/soundcloud-importer-uri/
 * Description:       Import Soundcloud RSS feeds and publish posts by them. 
 * Version:           1.0.0
 * Author:            Ian De Vos
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       soundcloud-importer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */
 
 


/**
 * top level menu
 */
add_action( 'admin_init', 'idv_SCImporter_options_init' );
add_action( 'admin_menu', 'idv_SCImporter_options_page' );
function idv_SCImporter_options_init() {
    /* Register our script. */
    wp_register_script( 'idv_SCImporter_script', plugins_url('/js/soundcloud-importer.js', __FILE__) );
}
function idv_SCImporter_options_page() {
 // add top level menu page
	$page =  add_menu_page(
	 'Soundcloud Importer',
	 'Soundcloud Importer Options',
	 'manage_options',
	 'idv_SCImporter_page',
	 'idv_SCImporter_options_page_html'
	 );
	 
	 add_action('admin_print_scripts-' . $page, 'idv_SCImporter_options_scripts');
}
 function idv_SCImporter_options_scripts() {
    /*
     * It will be called only on your plugin admin page, enqueue our script here
     */
    wp_enqueue_script( 'idv_SCImporter_script' );
}
/**
 * register our idv_SCImporter_options_page to the admin_menu action hook
 */



add_action( 'admin_init', 'idv_SCImporter_settings_init' );
function idv_SCImporter_settings_init() {
	 // register a new setting for "idv_SCImporter" page
	 register_setting( 'idv_SCImporter_page', 'idv_SCImporter_options', 'idv_SCImporter_options_save' );
	 
	 // register a new section in the "idv_SCImporter" page
	 add_settings_section(
		 'idv_SCImporter_section_addNew',
		 __( 'Feed source', 'idv_SCImporter' ),
		 'idv_SCImporter_section_addNew_cb',
		 'idv_SCImporter_page'
	 );
	 
	 // register a new field in the "idv_SCImporter_section_addNew" section, inside the "idv_SCImporter" page
	 add_settings_field(
		 'idv_SCImporter_field_addNew_url', // as of WP 4.6 this value is used only internally
		 // use $args' label_for to populate the id inside the callback
		 __( 'Feed URL', 'idv_SCImporter' ),
		 'idv_SCImporter_field_addNew_url_cb',
		 'idv_SCImporter_page',
		 'idv_SCImporter_section_addNew',
		 [
			 'label_for' => 'idv_SCImporter_field_addNew_url',
			 'class' => 'idv_SCImporter_row',
			 'idv_SCImporter_custom_data' => 'custom',
		 ]
	 );
	 
	 add_settings_field(
		 'idv_SCImporter_field_addNew_category', // as of WP 4.6 this value is used only internally
		 // use $args' label_for to populate the id inside the callback
		 __( 'Category', 'idv_SCImporter' ),
		 'idv_SCImporter_field_addNew_category_cb',
		 'idv_SCImporter_page',
		 'idv_SCImporter_section_addNew',
		 [
			 'label_for' => 'idv_SCImporter_field_addNew_category',
			 'class' => 'idv_SCImporter_row',
			 'idv_SCImporter_custom_data' => 'custom',
		 ]
	 );
	 
	
	 
	 
}



 
/**
 * custom option and settings:
 * callback functions
 */
 
// developers section cb
 
// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function idv_SCImporter_section_addNew_cb( $args ) {
 ?>
 <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'This is the feed you want to import.', 'idv_SCImporter' ); ?></p>
 <?php
}
 

function idv_SCImporter_field_addNew_url_cb( $args ) {
 // get the value of the setting we've registered with register_setting()
 $options = get_option( 'idv_SCImporter_options' );
 // output the field
 

 ?>
 
 
 <?php $value = isset( $options[ $args['label_for'] ] ) ?  $options[ $args['label_for'] ] : ""; ?>
 
 
 
 <input type="url" value="<?php echo $value; ?>" placeholder="" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="idv_SCImporter_options[<?php echo esc_attr( $args['label_for'] ); ?>]" data-custom="<?php echo esc_attr( $args['idv_SCImporter_custom_data'] ); ?>"/>
 

 <p class="description">
 <?php esc_html_e( 'The url of the Soundcloud RSS feed.', 'idv_SCImporter' ); ?>
 </p>
 
 <?php
}

function idv_SCImporter_field_addNew_category_cb( $args ) {
 // get the value of the setting we've registered with register_setting()
 $options = get_option( 'idv_SCImporter_options' );
 // output the field

 $categories = get_categories();
 //print_r($categories);
 ?>
 
 
 <?php $value = isset( $options[ $args['label_for'] ] ) ?  $options[ $args['label_for'] ] : ""; ?>
 

 <select id="<?php echo esc_attr( $args['label_for'] ); ?>" name="idv_SCImporter_options[<?php echo esc_attr( $args['label_for'] ); ?>]" data-custom="<?php echo esc_attr( $args['idv_SCImporter_custom_data'] ); ?>">
	<option></option>
	<?php 
		foreach($categories as &$category) {
			?>
				
				<option value="<?php echo $category->term_id; ?>" <?php echo $value == $category->term_id ? 'selected' : ''; ?>><?php echo $category->name; ?></option>
			<?php
		}
	?>
 
 </select>
 

 <p class="description">
 <?php esc_html_e( 'The post category of the Soundcloud RSS feed when imported to your website.', 'idv_SCImporter' ); ?>
 </p>
 
 <?php
}

 
/**
 * top level menu:
 * callback functions
 */
function idv_SCImporter_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	 
	// add error/update messages
	 
	// check if the user have submitted the settings
	// wordpress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) && empty(get_settings_errors('idv_SCImporter_messages'))  ) {
		// add settings saved message with the class of "updated"
		
		
		
		
		add_settings_error( 'idv_SCImporter_messages', 'idv_SCImporter_message', __( 'New feed added', 'idv_SCImporter' ), 'updated' );
	}
	 
	// show error/update messages
	settings_errors( 'idv_SCImporter_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
		<?php
			// output security fields for the registered setting "idv_SCImporter"
			settings_fields( 'idv_SCImporter_page' );
			// output setting sections and their fields
			// (sections are registered for "idv_SCImporter", each field is registered to a specific section)
			do_settings_sections( 'idv_SCImporter_page' );
			
			
			
			// output save settings button
			submit_button( 'Add new feed' );
		 ?>
		</form>
		
		<div>
		<h1><?php _e('Existing feeds','idv_SCImporter'); ?></h1>
		
		<form action="options.php" method="post">
		<?php
			settings_fields( 'idv_SCImporter_page' );
			$options = get_option( 'idv_SCImporter_options' );
			
			$urls = isset($options['idv_SCImporter_fields']) ? $options['idv_SCImporter_fields'] : array();
			$c = 0;
			
			?>
			
			<table class="widefat fixed" cellspacing="0">
				<thead>
					<th><?php esc_html_e( 'Feed', 'idv_SCImporter' ); ?> </th>
					<th><?php esc_html_e( 'RSS', 'idv_SCImporter' ); ?></th>
					<th><?php esc_html_e( 'Category', 'idv_SCImporter' ); ?></th>
					<th><?php esc_html_e( 'Last Update (RSS)', 'idv_SCImporter' ); ?></th>
					<th><?php esc_html_e( 'Last fetched', 'idv_SCImporter' ); ?></th>
					<th><?php esc_html_e( 'Links', 'idv_SCImporter' ); ?></th>
					<th><?php esc_html_e( 'Update', 'idv_SCImporter' ); ?></th>
					<th></th>
				</thead>
				<tbody>
			<?php 
			foreach($urls as &$feed) {
				
				$cat = get_the_category_by_ID($feed['category']);
				
				if(is_wp_error($cat)) {
					$cat = "";
				}
				
				$lastBuildDate = $feed['lastBuildDate'];
				$lastFetchDate = $feed['lastFetchDate'];
				
				
				?>
				<tr>
					<td><a href="<?php echo $feed['link']; ?>" target="_blank"><?php echo $feed['title']; ?></a>
					<td><a href="<?php echo $feed['url']; ?>"  target="_blank">RSS</a></td>
					<td><?php echo $cat; ?></td>
					<td><?php echo $lastBuildDate; ?></td>
					<td><?php echo $lastFetchDate; ?></td>
					<td><?php echo $feed['links']; ?></td>
					<td><input type="checkbox" value="update" name="idv_SCImporter_options[idv_SCImporter_fields][<?php echo $c; ?>][update]"  /> Update feed
					<td>
						<button class="delete_button">Delete</button>
						<input type="hidden" value="<?php echo $feed['link']; ?>" name="idv_SCImporter_options[idv_SCImporter_fields][<?php echo $c; ?>][link]" />
						<input type="hidden" value="<?php echo $feed['title']; ?>" name="idv_SCImporter_options[idv_SCImporter_fields][<?php echo $c; ?>][title]" />
						<input type="hidden" value="<?php echo $feed['url']; ?>" name="idv_SCImporter_options[idv_SCImporter_fields][<?php echo $c; ?>][url]" />
						
						
						<input type="hidden" value="<?php echo $feed['lastBuildDate']; ?>" name="idv_SCImporter_options[idv_SCImporter_fields][<?php echo $c; ?>][lastBuildDate]" />
						<input type="hidden" value="<?php echo $feed['lastFetchDate']; ?>" name="idv_SCImporter_options[idv_SCImporter_fields][<?php echo $c; ?>][lastFetchDate]" />
						
						<input type="hidden" value="<?php echo $feed['links']; ?>" name="idv_SCImporter_options[idv_SCImporter_fields][<?php echo $c; ?>][links]" />
						<input type="hidden" value="<?php echo $feed['category']; ?>" name="idv_SCImporter_options[idv_SCImporter_fields][<?php echo $c; ?>][category]" />
						
						
						
						<input type="hidden" value="<?php echo $feed['data']; ?>" name="idv_SCImporter_options[idv_SCImporter_fields][<?php echo $c; ?>][data]" />
						
					</td>
				</tr>
				<?php 
				$c++;
			}
			
			
			?>
			</tbody>
			</table>
			<div id="setting-error-idv_SCImporter_message_updated" class="notice-warning " style="display: none;"> 
			<p><strong> <?php esc_html_e( 'Feeds updated. Click the button below', 'idv_SCImporter' ); ?></strong></p></div>
			<?php 
			
			
			submit_button( 'Update list' );
		?>
		</form>
		</div>
	</div>
	<?php
}

function idv_SCImporter_options_save($input) {
	//print_r($input);
	
	
	
	if(isset($input['idv_SCImporter_field_addNew_url'])) {
		$input = idv_SCImporter_enterNewUrl($input);
	} else {
		$input = idv_SCImporter_updateFeedList($input);
	}
	
	return $input;
	
}


function idv_SCImporter_enterNewUrl($input) {
	$options = get_option( 'idv_SCImporter_options' );	
		if(!isset($options['idv_SCImporter_fields'])) {
			$input['idv_SCImporter_fields'] = array();
		} else {
			$input['idv_SCImporter_fields'] = $options['idv_SCImporter_fields']; 
		}
		
		if(strlen($input['idv_SCImporter_field_addNew_url']) > 0 ) {
		
			if(isset($input['idv_SCImporter_field_addNew_category']) && strlen($input['idv_SCImporter_field_addNew_category']) > 0 ) {
				
			
			
				
				if(in_array ($input['idv_SCImporter_field_addNew_url'], array_column($input['idv_SCImporter_fields'], 'url')) == false ) {
					$new_entry = array();
					$new_entry['url'] = $input['idv_SCImporter_field_addNew_url'];
					$new_entry['category'] = $input['idv_SCImporter_field_addNew_category'];
					$new_entry = idv_SCImporter_updateFeed($new_entry);
					if($new_entry == false) {
						
						add_settings_error( 'idv_SCImporter_messages', 'idv_SCImporter_message', __( 'This url is not valid RSS', 'idv_SCImporter' ), 'error' );
				
					} else {
						$input['idv_SCImporter_fields'][] = $new_entry;
						
					
					}
					} else {
						
						add_settings_error( 'idv_SCImporter_messages', 'idv_SCImporter_message', __( 'This feed is already added', 'idv_SCImporter' ), 'error' );
				}
			} else {
				add_settings_error( 'idv_SCImporter_messages', 'idv_SCImporter_message', __( 'You need to choose a category', 'idv_SCImporter' ), 'error' );
			}
		} else {
			add_settings_error( 'idv_SCImporter_messages', 'idv_SCImporter_message', __( 'You need to enter an url', 'idv_SCImporter' ), 'error' );
			
		}
		$input = idv_SCImporter_emptyAddNew($input);
		return $input;
}

function idv_SCImporter_emptyAddNew($input) {
	$input['idv_SCImporter_field_addNew_category'] = "";
	$input['idv_SCImporter_field_addNew_url'] = "";
	return $input; 
	
}

function idv_SCImporter_updateFeedList($input) {
	
	add_settings_error( 'idv_SCImporter_messages', 'idv_SCImporter_message', __( 'Feeds updated', 'idv_SCImporter' ), 'updated' );
		
		$input['idv_SCImporter_field_addNew_category'] = "";
		$input['idv_SCImporter_field_addNew_url'] = "";
		$c = 0;
			
			
		foreach($input['idv_SCImporter_fields'] as &$feed) {
			if(isset($feed['update']) && $feed['update'] == 'update') {
				
				$feed = idv_SCImporter_updateFeed($feed);
				 
				
			}
			
			$c++;
		}
	
	return $input;
}


function idv_SCImporter_updateFeed($feed) {
	$file = idv_SCImporter_readFeed($feed['url']);
	
		
	if($file) {
		 
		$feed['data'] = base64_encode($file);
		$feed = idv_SCImporter_parseFeed($feed);
		
	}
	return $feed;
}

function idv_SCImporter_readFeed($feed_url) {
 
    if (strlen($feed_url) > 0) {
		 
		$content = @file_get_contents($feed_url);
		
		return $content;
		
		
		
	} else {
		return false;
   
		
	}
}

function idv_SCImporter_parseFeed($feed) {
	$file = base64_decode($feed['data']);
	libxml_use_internal_errors(true);
	 $x = simplexml_load_string($file);
	 	
	 if ($x === false) {
		 return false;
	 } else {
		 $feed['title'] = $x->channel->title->__toString();
		 $feed['link'] = $x->channel->link->__toString();
		 $lastBuildDate = DateTime::createFromFormat("D, d M Y H:i:s T", $x->channel->lastBuildDate->__toString());
		 $lastFetchDate = new DateTime();
		 $feed['lastBuildDate'] =  date_i18n( 'D, d M Y H:i:s T', $lastBuildDate->getTimestamp() );//$lastBuildDate->format("Y-m-d H:i:s");
		 $feed['lastFetchDate'] = date_i18n( 'D, d M Y H:i:s T', current_time( 'U' )); 
		 
		 $feed['links'] = count($x->channel->item);
		$items = array();
		
		/*foreach($x->channel->item as $entry) {
			//echo "<li><a href='$entry->link' title='$entry->title'>" . $entry->title . "</a></li>";
			$items[] = $entry;
			
		}*/
		idv_SCImporter_importFeed($x, $feed);
		return $feed;
	 }
	
}

function idv_SCImporter_importFeed($x, $feed) {
	
	
	$args = array(
		'numberposts'   => -1,
		'offset'           => 0,
		'category'         => '',
		'category_name'    => '',
		'orderby'          => 'date',
		'order'            => 'DESC',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => 'sc_feed_link',
		'meta_value'       => $feed['link'],
		'post_type'        => 'post',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'author'	   => '',
		'author_name'	   => '',
		'post_status'      => 'publish',
		'suppress_filters' => true 
	);
	$posts_array = get_posts( $args ); 

	$post_array_guids = array();
	
	foreach($posts_array as $post) {
		$post_array_guids[$post->ID] = get_post_meta( $post->ID,'sc_guid' , true );
	}	
	
	
	
	foreach($x->channel->item as $entry) {
			//echo "<li><a href='$entry->link' title='$entry->title'>" . $entry->title . "</a></li>";
			
			$post_entry = idv_SCImporter_makePost($entry, $feed);
			$id = array_search($post_entry['meta_input']['sc_guid'],$post_array_guids);
			
			$should_update = true;
			if($id != false) {
				// post bestaat reeds;
				$post_entry['ID'] = $id;
				
				
				
			}
			if($should_update) {
				wp_insert_post($post_entry);
			}
			//add_settings_error( 'idv_SCImporter_messages', 'idv_SCImporter_message', print_r($post_entry,true), 'updated' );
	}
}

function idv_SCImporter_makePost($entry, $feed) {
	$sc_title = $entry->title->__toString();
	$sc_link = $entry->link->__toString();
	$sc_pubdate = DateTime::createFromFormat("D, d M Y H:i:s T", $entry->pubDate->__toString());
	
	$sc_guid = $entry->guid->__toString();
	$sc_description = $entry->description->__toString();
	$sc_feed_link = $feed['link'];
	$sc_category = $feed['category'];
	
	$post_entry = array();
	$post_entry['post_title'] = $sc_title;
	$post_entry['meta_input']['sc_link'] = $sc_link;
	$post_entry['post_date'] = $sc_pubdate->format('Y-m-d H:i:s');
	$post_entry['meta_input']['sc_guid'] = $sc_guid;
	$post_entry['post_content'] = $sc_description;
	$post_entry['meta_input']['sc_feed_link'] = $sc_feed_link;
	$post_entry['post_category'] = array($sc_category);
	$post_entry['post_status'] = 'publish';
	
	return $post_entry;
		
}