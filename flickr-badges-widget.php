<?php
/**
 * Widget - Flickr Badge Widget
 * 
 * @package zFrame
 * @subpackage Classes
 * For another improvement, you can drop email to zourbuth@gmail.com or visit http://zourbuth.com
**/
 
class Flickr_Badges_Widget extends WP_Widget {
	var $prefix; 
	var $textdomain;
	
	/** Set up the widget's unique name, ID, class, description, and other options. **/
	function __construct() {
		
		/* Set up the widget control options. */
		$control_options = array(
			'width' => 525,
			'height' => 350,
			'id_base' => "zflickr"
		);
		/** Add some informations to the widget **/
		$widget_options = array('classname' => 'widget_flickr', 'description' => __( '[+] Displays a Flickr photo stream from an ID', $this->textdomain ) );
		
		/* Create the widget. */
		$this->WP_Widget('zflickr', __('Flickr Badge', $this->textdomain), $widget_options, $control_options );
		
		add_action( 'load-widgets.php', array(&$this, 'widget_admin') );
		
		if ( is_active_widget(false, false, $this->id_base, true) && !is_admin() ) {
			/* load the widget stylesheet for the widgets screen. */
			wp_enqueue_style( 'z-flickr', FLICKR_BADGES_WIDGET_URL . 'css/widget.css', false, 0.7, 'screen' );
		}
	}
	

	/* Push the widget stylesheet widget.css into widget admin page */
	function widget_admin() {
		wp_enqueue_style( 'z-flickr-admin', FLICKR_BADGES_WIDGET_URL . 'css/dialog.css' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'z-flickr-admin', FLICKR_BADGES_WIDGET_URL . 'js/jquery.dialog.js' );
	}	
	
	function widget( $args, $instance ) {
		extract( $args );

		/* Set up the arguments for wp_list_categories(). */
		$cur_arg = array(
			'title'			=> $instance['title'],
			'type'			=> empty( $instance['type'] ) ? 'user' : $instance['type'],
			'flickr_id'		=> $instance['flickr_id'],
			'count'			=> (int)$instance['count'],
			'display'		=> empty( $instance['display'] ) ? 'latest' : $instance['display']
		);
		
		extract( $cur_arg );

		// if the photo is < 1, set it to 1
		if ( $count < 1 ) $count = 1;
		
		/** if the widget have an ID, we can continue **/
		if ( !empty( $instance['flickr_id'] ) ) {
		
			// print the before widget
			echo $before_widget;
			
			if ( $title ) echo $before_title . $title . $after_title;
		
			/// get the user direction, rtl or ltr
			if ( function_exists( 'is_rtl' ) ) $dir= is_rtl() ? 'rtl' : 'ltr';

			// wrap the widget
			if (!empty( $instance['intro_text'] ) ) echo '<p>' . do_shortcode( $instance['intro_text'] ) . '</p>';
			
			echo "<div class='zframe-flickr-wrap-$dir'>";
				echo "<script type='text/javascript' src='http://www.flickr.com/badge_code_v2.gne?count=$count&amp;display=$display&amp;size=s&amp;layout=x&amp;source=$type&amp;$type=$flickr_id'></script>";
			echo '</div>';
			
			if (!empty( $instance['outro_text'] ) ) echo '<p>' . do_shortcode( $instance['outro_text'] ) . '</p>';

			// print the after widget
			echo $after_widget;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['type'] 			= strip_tags($new_instance['type']);
		$instance['flickr_id'] 		= strip_tags($new_instance['flickr_id']);
		$instance['count'] 			= (int) $new_instance['count'];
		$instance['display'] 		= strip_tags($new_instance['display']);
		$instance['title']			= strip_tags($new_instance['title']);
		$instance['tab']			= $new_instance['tab'];
		$instance['intro_text'] 	= $new_instance['intro_text'];
		$instance['outro_text']		= $new_instance['outro_text'];
		$instance['custom']			= $new_instance['custom'];
		
		return $instance;
	}

	function form( $instance ) {
		/* Set up the default form values. */
		$defaults = array(
			'title'			=> esc_attr__( 'Flickr Widget', $this->textdomain ),
			'type'			=> 'user',
			'flickr_id'		=> '', // 71865026@N00
			'count'			=> 9,
			'display'		=> 'display',
			'tab'			=> array( 0 => true ),
			'intro_text'	=> '',
			'outro_text'	=> '',
			'custom'		=> ''
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		$types = array( 
			'user'  => esc_attr__( 'user', $this->textdomain ), 
			'group' => esc_attr__( 'group', $this->textdomain )
		);
		$displays = array( 
			'latest' => esc_attr__( 'latest', $this->textdomain ),
			'random' => esc_attr__( 'random', $this->textdomain )
		);		
		?>
		
		<div class="pluginName">Flickr Badges Widget<span class="pluginVersion"><?php echo FLICKR_BADGES_WIDGET_VERSION; ?></span></div>
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
				
				// Widget background
				$("#fbw-<?php echo $this->id; ?>").closest(".widget-inside").addClass("ntotalWidgetBg");
			});
		</script>
		
		<div id="fbw-<?php echo $this->id ; ?>" class="totalControls tabbable tabs-left">
			<!-- Tab List -->
			<ul class="nav nav-tabs">
				<li class="<?php if ( $instance['tab'][0] ) : ?>active<?php endif; ?>">General<input type="hidden" name="<?php echo $this->get_field_name( 'tab' ); ?>[]" value="<?php echo esc_attr( $instance['tab'][0] ); ?>" /></li>
				<li class="<?php if ( $instance['tab'][1] ) : ?>active<?php endif; ?>">Customs<input type="hidden" name="<?php echo $this->get_field_name( 'tab' ); ?>[]" value="<?php echo esc_attr( $instance['tab'][1] ); ?>" /></li>
				<li class="<?php if ( $instance['tab'][2] ) : ?>active<?php endif; ?>">About<input type="hidden" name="<?php echo $this->get_field_name( 'tab' ); ?>[]" value="<?php echo esc_attr( $instance['tab'][2] ); ?>" /></li>
			</ul>	
			
			<ul class="tab-content">
				<li class="tab-pane <?php if ( $instance['tab'][0] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', $this->textdomain); ?></label>
							<span class="controlDesc"><?php _e( 'Give the widget title, or leave it empty for no title.', $this->textdomain ); ?></span>
							<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e( 'Type', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'The type of images from user or group.', $this->textdomain ); ?></span>
							<select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
								<?php foreach ( $types as $option_value => $option_label ) { ?>
									<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['type'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
								<?php } ?>
							</select>				
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('flickr_id'); ?>"><?php _e('Flickr ID', $this->textdomain); ?></label>
							<span class="controlDesc"><?php _e( 'Put the flickr ID here, go to <a href="http://www.idgettr.com" target="_blank">idGettr</a> if you don\'t know your ID.', $this->textdomain ); ?></span>
							<input id="<?php echo $this->get_field_id('flickr_id'); ?>" name="<?php echo $this->get_field_name('flickr_id'); ?>" type="text" value="<?php echo esc_attr( $instance['flickr_id'] ); ?>" />
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number', $this->textdomain); ?></label>
							<span class="controlDesc"><?php _e( 'Number of photo to show.', $this->textdomain ); ?></span>
							<input class="column-last" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr( $instance['count'] ); ?>" size="3" />
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('display'); ?>"><?php _e('Display Method', $this->textdomain); ?></label>
							<span class="controlDesc"><?php _e( 'Get the image from recent or use random function.', $this->textdomain ); ?></span>
							<select id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>">
								<?php foreach ( $displays as $option_value => $option_label ) { ?>
									<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['display'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
								<?php } ?>
							</select>	
						</li>
					</ul>
				</li>

				<li class="tab-pane <?php if ( $instance['tab'][1] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id('intro_text'); ?>"><?php _e( 'Intro Text', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'This option will display addtional text before the widget content and HTML supports.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'intro_text' ); ?>" id="<?php echo $this->get_field_id( 'intro_text' ); ?>" rows="2" class="widefat"><?php echo esc_textarea($instance['intro_text']); ?></textarea>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('outro_text'); ?>"><?php _e( 'Outro Text', $this->textdomain ); ?></label>
							<span class="controlDesc"><?php _e( 'This option will display addtional text after widget and HTML supports.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'outro_text' ); ?>" id="<?php echo $this->get_field_id( 'outro_text' ); ?>" rows="2" class="widefat"><?php echo esc_textarea($instance['outro_text']); ?></textarea>
							
						</li>				
						<li>
							<label for="<?php echo $this->get_field_id('custom'); ?>"><?php _e( 'Custom Script & Stylesheet', $this->textdomain ) ; ?></label>
							<span class="controlDesc"><?php _e( 'Use this box for additional widget CSS style of custom javascript. Current widget selector: ', $this->textdomain ); ?><?php echo '<code>#' . $this->id . '</code>'; ?></span>
							<textarea name="<?php echo $this->get_field_name( 'custom' ); ?>" id="<?php echo $this->get_field_id( 'custom' ); ?>" rows="5" class="widefat code"><?php echo htmlentities($instance['custom']); ?></textarea>
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['tab'][2] ) : ?>active<?php endif; ?>">
					<ul>
						<li>	
							<a href="http://feedburner.google.com/fb/a/mailverify?uri=zourbuth&amp;loc=en_US">Subscribe to zourbuth by Email</a><br />
							<?php _e( 'Like my work? Please consider to ', $this->textdomain ); ?><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W6D3WAJTVKAFC" title="Donate"><?php _e( 'donate', $this->textdomain ); ?></a>.<br /><br />
							<span style="color: #0063DC; font-weight: bold;">Flick</span><span style="color: #FF0084; font-weight: bold;">r</span> Badge Widget &copy; Copyright <a href="http://zourbuth.com">zourbuth</a> <?php echo date("Y"); ?>.
						</li>
					</ul>
				</li>

			</ul>
		</div>
		<?php
	}
}