<?php
/**
 * The Categories widget replaces the default WordPress Categories widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_list_categories() function.
 *
 */
class Sticky_Post_Widget extends WP_Widget {

	// Prefix for the widget.
	var $prefix;

	// Textdomain for the widget.
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.6.0
	 */
	function __construct() {
	
		// Give your own prefix name eq. your-theme-name-
		$prefix = '';
		
		// Set up the widget options
		$widget_options = array(
			'classname' => 'sticky-post',
			'description' => esc_html__( 'A list of sticky post in a widget.', $this->textdomain )
		);

		// Set up the widget control options
		$control_options = array(
			'width' => 460,
			'height' => 350,
			'id_base' => "{$this->prefix}sticky-post"
		);

		// Create the widget
		$this->WP_Widget( "{$this->prefix}sticky-post", esc_attr__( 'Sticky Post', $this->textdomain ), $widget_options, $control_options );
		
		// Load the widget stylesheet for the widgets admin screen
		add_action( 'load-widgets.php', array(&$this, 'sticky_post_widget_admin_script_style') );
		add_action( 'admin_print_styles', array(&$this, 'sticky_post_widget_admin_style') );
		
		// Print the user costum style sheet
		if ( is_active_widget(false, false, $this->id_base) ) {
			wp_enqueue_style( 'sticky-post', STICKY_POST_URL . 'css/sticky-post.css' );
			wp_enqueue_script( 'jquery' );
			add_action( 'wp_head', array( &$this, 'print_script') );
		}
	}

	// Push the widget stylesheet widget.css into widget admin page
	function sticky_post_widget_admin_script_style() {
		wp_enqueue_style( 'sticky-post-dialog', STICKY_POST_URL . 'css/dialog.css' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'sticky-post-dialog', STICKY_POST_URL . 'js/jquery.dialog.js' );
	}
	
	// Push the widget stylesheet widget.css into widget admin page
	function sticky_post_widget_admin_style() {
		echo '<style type="text/css"> .spControls .timestamp { background-image: url(images/date-button.gif); background-position: left top; background-repeat: no-repeat; padding-left: 18px; }</style>';
	}
	
	function print_script() {
		$settings = $this->get_settings();
		foreach ($settings as $key => $setting){
			$widget_id = $this->id_base . '-' . $key;
			if( is_active_widget( false, $widget_id, $this->id_base ) ) {
				// Print the custom style and script
				if ( !empty( $setting['customstylescript'] ) ) echo $setting['customstylescript'];
			}
		}
	}
	
	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6.0
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Set up the arguments for wp_list_categories(). */
		$args = array(
			'title_icon'		=> $instance['title_icon'],
			'order'				=> $instance['order'],
			'order_by'			=> $instance['order_by'],
			'taxonomy' 			=> !empty( $instance['taxonomy'] ) ? join( ', ', $instance['taxonomy'] ) : '',
			'items'				=> !empty( $instance['items'] ) ? intval( $instance['items'] ) : 4,
			'show_excerpt' 		=> !empty( $instance['show_excerpt'] ) ? true : false,
			'excerpt_length'	=> !empty( $instance['excerpt_length'] ) ? intval( $instance['excerpt_length'] ) : 15,
			'excerpt_more'		=> $instance['excerpt_more'],
			'show_thumbnail' 	=> !empty( $instance['show_thumbnail'] ) ? true : false,
			'show_date' 		=> !empty( $instance['show_date'] ) ? true : false,
			'date_icon' 		=> $instance['date_icon'],
			'date_format' 		=> $instance['date_format'],
			'show_comments' 	=> !empty( $instance['show_comments'] ) ? true : false,
			'comment_icon' 		=> $instance['comment_icon'],
			'icon_height' 		=> $instance['icon_height'],
			'icon_width' 		=> $instance['icon_width'],
			'icon_empty' 		=> $instance['icon_empty'],
			'template'			=> $instance['template'],
			'toggle_active'		=> $instance['toggle_active'],
			'intro_text' 		=> $instance['intro_text'],
			'outro_text' 		=> $instance['outro_text'],
			'customstylescript'	=> $instance['customstylescript']
		);

		// Output the theme's widget wrapper
		echo $before_widget;
		
		// If a title was input by the user, display it.
		if ( !empty( $instance['title_icon'] ) )
			$titleIcon = '<img class="titleIcon" alt="" src="' . $instance['title_icon'] . '" />';
		else
			$titleIcon = '';		

		// If a title was input by the user, display it
		if ( !empty( $instance['title'] ) )
			echo $before_title . $titleIcon . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		// Print intro text if exist
		if ( !empty( $instance['intro_text'] ) )
			echo '<p class="'. $this->id . '-intro-text intro-text">' . $instance['intro_text'] . '</p>';
			
		// Print the custom post
		if(!empty($instance['items']))
			echo sticky_post_widget( $args );
		
		// Print outro text if exist
		if ( !empty( $instance['outro_text'] ) )
			echo '<p class="'. $this->id . '-outro_text outro_text">' . $instance['outro_text'] . '</p>';

		// Close the theme's widget wrapper
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Set the instance to the new instance. */
		$instance = $new_instance;
		
		/* If new taxonomy is chosen, reset includes and excludes. */
		if ( $instance['order'] !== $old_instance['order'] && '' !== $old_instance['order'] ) {
			$instance['taxonomy'] = array();
		}		

		$instance['title'] 				= strip_tags( $new_instance['title'] );
		$instance['title_icon']			= strip_tags( $new_instance['title_icon'] );
		$instance['items'] 				= $new_instance['items'];
		$instance['order_by'] 			= $new_instance['order_by'];
		$instance['show_thumbnail'] 	= ( isset( $new_instance['show_thumbnail'] ) ? 1 : 0 );
		$instance['show_date'] 			= ( isset( $new_instance['show_date'] ) ? 1 : 0 );
		$instance['date_icon'] 			= $new_instance['date_icon'];
		$instance['date_format'] 		= $new_instance['date_format'];
		$instance['show_excerpt'] 		= ( isset( $new_instance['show_excerpt'] ) ? 1 : 0 );
		$instance['excerpt_length'] 	= $new_instance['excerpt_length'];
		$instance['excerpt_more'] 		= $new_instance['excerpt_more'];
		$instance['show_comments'] 		= ( isset( $new_instance['show_comments'] ) ? 1 : 0 );
		$instance['comment_icon'] 		= $new_instance['comment_icon'];
		$instance['icon_height'] 		= $new_instance['icon_height'];
		$instance['icon_width'] 		= $new_instance['icon_width'];
		$instance['icon_empty'] 		= $new_instance['icon_empty'];
		$instance['template']			= $new_instance['template'];
		$instance['toggle_active'] 		= $new_instance['toggle_active'];
		$instance['intro_text'] 		= $new_instance['intro_text'];
		$instance['outro_text'] 		= $new_instance['outro_text'];
		$instance['customstylescript']	= $new_instance['customstylescript'];
		
		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6.0
	 */
	function form( $instance ) {

		// Set up the default form values
		// date-time: mm jj aa hh mn
		$defaults = array(
			'title' 			=> '',
			'title_icon'		=> '',
			'order' 			=> '',
			'order_by' 			=> 'DESC',
			'taxonomy' 			=> array(),
			'items' 			=> '5',
			'show_excerpt' 		=> true,
			'excerpt_length' 	=> 15,
			'excerpt_more' 		=> '...',
			'show_thumbnail' 	=> true,
			'show_date' 		=> true,
			'date_icon' 		=> STICKY_POST_URL . 'images/date.png',
			'date_format' 		=> get_option( 'date_format' ),
			'show_comments' 	=> true,
			'comment_icon' 		=> STICKY_POST_URL . 'images/comments.png',
			'icon_height' 		=> 40,
			'icon_width' 		=> 40,
			'icon_empty' 		=> STICKY_POST_URL . 'images/thumbnail.png',
			'template'			=> 'left',
			'toggle_active'		=> array(0 => true, 1 => false, 2 => false, 3 => false, 4 => false, 5 => false, 6 => false),
			'intro_text' 		=> '',
			'outro_text' 		=> '',
			'customstylescript'	=> ''
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		// Set the default value of each widget input
		$templates = array( 
			'left' => esc_attr__( 'Left', $this->textdomain ) , 
			'right' => esc_attr__( 'Right', $this->textdomain ), 
			'block' => esc_attr__( 'Block', $this->textdomain ) 
		);
		$order_by = array(
			'ASC'	=> esc_attr__( 'Ascending', $this->textdomain ), 
			'DESC'	=> esc_attr__( 'Descending', $this->textdomain )
		);
		$taxonomies = get_taxonomies( array( 'show_tagcloud' => true ), 'objects' );
		$terms = get_terms( $instance['order'] );
		?>

		<span class="spWidgetVersion"><?php echo STICKY_POST_VERSION; ?></span>
		<script type="text/javascript">
			// Tabs function
			jQuery(document).ready(function($){
				// Tabs function
				$('ul.nav-tabs li').each(function(i) {
					$(this).bind("click", function(){
						var liIndex = $(this).index();
						var content = $(this).parent("ul").next().children("li").eq(liIndex);
						$(this).addClass('active').siblings("li").removeClass('active');
						$(content).show().addClass('active').siblings().hide().removeClass('active');
	
						$(this).parent("ul").find("input").val(0);
						$('input', this).val(1);
					});
				});
				
				// Farbtastic function
				$("#sp-<?php echo $this->id; ?> .pickcolor").click(function() {
					$(this).next().slideToggle();					
					$(this).next().farbtastic($(this).prev());	
					return false;
				});
				$('html').click(function() { $('.tipsy').remove(); $('.farbtastic-wrapper').fadeOut(); });
				$('.farbtastic').click(function(event){ event.stopPropagation(); });
				
				// Image uploader/picker/remove
				$("#sp-<?php echo $this->id; ?> a.addImage").spAddImages();
				$("#sp-<?php echo $this->id; ?> a.removeImage").spRemoveImages();
				
				// Widget background
				$("#sp-<?php echo $this->id; ?>").closest(".widget-inside").addClass("spWidgetBg");
			});
		</script>

		<div id="sp-<?php echo $this->id ; ?>" class="spControls tabbable tabs-left">
			<ul class="nav nav-tabs">
				<li class="<?php if ( $instance['toggle_active'][0] ) : ?>active<?php endif; ?>">General<input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo esc_attr( $instance['toggle_active'][0] ); ?>" /></li>
				<li class="<?php if ( $instance['toggle_active'][1] ) : ?>active<?php endif; ?>">Excerpts<input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo esc_attr( $instance['toggle_active'][1] ); ?>" /></li>
				<li class="<?php if ( $instance['toggle_active'][2] ) : ?>active<?php endif; ?>">Thumbnails<input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo esc_attr( $instance['toggle_active'][2] ); ?>" /></li>
				<li class="<?php if ( $instance['toggle_active'][3] ) : ?>active<?php endif; ?>">Comments<input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo esc_attr( $instance['toggle_active'][3] ); ?>" /></li>
				<li class="<?php if ( $instance['toggle_active'][4] ) : ?>active<?php endif; ?>">Date<input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo esc_attr( $instance['toggle_active'][4] ); ?>" /></li>				
				<li class="<?php if ( $instance['toggle_active'][5] ) : ?>active<?php endif; ?>">Customs<input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo esc_attr( $instance['toggle_active'][6] ); ?>" /></li>
				<li class="<?php if ( $instance['toggle_active'][6] ) : ?>active<?php endif; ?>">Upgrade<input type="hidden" name="<?php echo $this->get_field_name( 'toggle_active' ); ?>[]" value="<?php echo esc_attr( $instance['toggle_active'][7] ); ?>" /></li>
			</ul>
			<ul class="tab-content">
				<li class="tab-pane <?php if ( $instance['toggle_active'][0] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', $this->textdomain ); ?></label>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
						</li>						
						<li>
							<label for="<?php echo $this->get_field_id('items'); ?>"><?php _e( 'Post Number', $this->textdomain ); ?> </label>
							<span class="controlDesc"><?php _e( 'The total post to display in a widget.', $this->textdomain ); ?></span>
							<input class="smallfat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo esc_attr($instance['items']); ?>" />
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'order_by' ); ?>"><?php _e( 'Sort Order', $this->textdomain ); ?></label> 
							<span class="controlDesc"><?php _e( 'The page order in ascending or descending ordering', $this->textdomain ); ?></span>
							<select id="<?php echo $this->get_field_id( 'order_by' ); ?>" name="<?php echo $this->get_field_name( 'order_by' ); ?>">
								<?php foreach ( $order_by as $option_value => $option_label ) { ?>
									<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['order_by'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
								<?php } ?>
							</select>
						</li>	
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][1] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['show_excerpt'], true ); ?> id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" /><?php _e( 'Show Excerpt', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'Display the post excerpt.', $this->textdomain ); ?></span>
						</li>					
						<li>
							<label for="<?php echo $this->get_field_id('excerpt_length'); ?>"><?php _e( 'Excerpt Lenght', $this->textdomain ); ?> </label>
							<span class="controlDesc"><?php _e( 'The excerpt total spaces to generate.', $this->textdomain ); ?></span>
							<input class="smallfat" id="<?php echo $this->get_field_id('excerpt_length'); ?>" name="<?php echo $this->get_field_name('excerpt_length'); ?>" type="text" value="<?php echo esc_attr( $instance['excerpt_length'] ); ?>" />
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'excerpt_more' ); ?>"><?php _e( 'More Text', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'Put the more text at the end of the excerpt.', $this->textdomain ); ?></span>
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'excerpt_more' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_more' ); ?>" value="<?php echo esc_attr( $instance['excerpt_more'] ); ?>" />
						</li>					
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][2] ) : ?>active<?php endif; ?>">
					<ul>					
						<li>
							<label for="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['show_thumbnail'], true ); ?> id="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'show_thumbnail' ); ?>" /><?php _e( 'Show thumbnail', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'Display the standard post featured image or thumbnail.', $this->textdomain ); ?></span>
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][3] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'show_comments' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['show_comments'], true ); ?> id="<?php echo $this->get_field_id( 'show_comments' ); ?>" name="<?php echo $this->get_field_name( 'show_comments' ); ?>" /><?php _e( 'Show comments number', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'Display the comments for each post.', $this->textdomain ); ?></span>
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][4] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'show_date' ); ?>">
							<input class="checkbox" type="checkbox" <?php checked( $instance['show_date'], true ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" /><?php _e( 'Show date', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'Display the post date for each post.', $this->textdomain ); ?></span>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'date_format' ); ?>"><?php _e( 'Date Format', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'Controls the format of the Page date set by the <b>show date</b> parameter. This parameter defaults to the date format configured in your WordPress options. See <a title="Formatting Date and Time" href="http://codex.wordpress.org/Formatting_Date_and_Time">Formatting Date and Time</a> and the <a title="http://php.net/date" class="external text" href="http://php.net/date">date format page on the php web site</a>.', $this->textdomain ); ?></span>
							<input type="text" style="width: 48%;" class="smallfat code" id="<?php echo $this->get_field_id( 'date_format' ); ?>" name="<?php echo $this->get_field_name( 'date_format' ); ?>" value="<?php echo esc_attr( $instance['date_format'] ); ?>" />
						</li>	
					</ul>
				</li>				
				<li class="tab-pane <?php if ( $instance['toggle_active'][5] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id('intro_text'); ?>"><?php _e( 'Intro text:', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'This option will display addtional text before the widget title and HTML supports.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'intro_text' ); ?>" id="<?php echo $this->get_field_id( 'intro_text' ); ?>" rows="4" class="widefat"><?php echo esc_textarea($instance['intro_text']); ?></textarea>
							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('outro_text'); ?>"><?php _e( 'Outro text:', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'This option will display addtional text after widget and HTML supports.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'outro_text' ); ?>" id="<?php echo $this->get_field_id( 'outro_text' ); ?>" rows="4" class="widefat"><?php echo esc_textarea($instance['outro_text']); ?></textarea>
							
						</li>				
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['toggle_active'][6] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<p><a target="_blank" href="http://codecanyon.net/item/sticky-post-pro-wordpress-premium-plugin/3145967?ref=zourbuth"><span class="upgrade"></span></a></a>Upgrade to <a target="_blank" href="http://codecanyon.net/item/sticky-post-pro-wordpress-premium-plugin/3145967?ref=zourbuth">Sticky Post Pro</a> for more plugin options and customizations.<p>
							<p><label>Taxonomy</label><span class="controlDesc">Display sticky post based on category or tag selection or even custom taxonomy.</span></p>
							<p><label>Thumbnail Resizer</label><span class="controlDesc">Easy post thumbnail resizer for all custom size.</span></p>							
							<p><label>Custom Image for no Thumbnail</label><span class="controlDesc">Will use the default image if the post has no thumbnail attached.</span></p>							
							<p><label>Post Date & Comment Icon</label><span class="controlDesc">Easy to use custom icon for date and comments.</span></p>							
							<p><label>Custom Layouts</label><span class="controlDesc">3 predifined custom layout, right thumbnail, left thumbnail or block thumbnail</span></p>							
							<p><label>Custom Style & Script</label><span class="controlDesc">Easy to add your custom style and script for each selector.</span></p>							
							<p><label>Full Supports</label><span class="controlDesc">You will get full and easy supports for this plugin.</span></p>							
							<p><label>Plugin Updates</label><span class="controlDesc">Notification for every available update.</span></p>
							<p><label>And Many More</label><span class="controlDesc">Full supports, documentation and more...</span></p>
							<p><h3><a target="_blank" href="http://codecanyon.net/item/sticky-post-pro-wordpress-premium-plugin/3145967?ref=zourbuth">Upgrade Now!</a></h3></p>
							
							<input type="hidden" id="<?php echo $this->get_field_id( 'icon_height' ); ?>" name="<?php echo $this->get_field_name( 'icon_height' ); ?>" value="<?php echo esc_attr( $instance['icon_height'] ); ?>" />
							<input type="hidden" id="<?php echo $this->get_field_id( 'icon_width' ); ?>" name="<?php echo $this->get_field_name( 'icon_width' ); ?>" value="<?php echo esc_attr( $instance['icon_width'] ); ?>" />
							<input type="hidden" id="<?php echo $this->get_field_id( 'template' ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" value="<?php echo esc_attr( $instance['template'] ); ?>" />
							<input type="hidden" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" value="<?php echo esc_attr( $instance['order'] ); ?>" />					
						</li>
					</ul>
				</li>				
			</ul>
		</div>
	<?php
	}
}

?>