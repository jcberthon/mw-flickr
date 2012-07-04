<?php
/*
Plugin Name: WP Flickr Widget
Plugin URI: http://www.sooource.net/wordpress-flickr-widget
Description: Widget in the sidebar shows a few of your photos from Flickr.
Author URI: http://www.sooource.net
Author: YandexBot.
Version: 1.0.0
*/

add_action( 'widgets_init', 'wpfw_flickr_widget' );

function wpfw_flickr_widget() {
	register_widget( 'wpfw_flickr_Widget' );
}

class wpfw_flickr_widget extends WP_Widget {
		
	function wpfw_flickr_Widget() {
		$widget_style = array('classname' => 'wpfw_flickr_widget',
							  'description' => 'Your photos from Flickr.');
							  
		$widget_define = array('show_id' => 'single_flickr',
							   'get_tips' => 'true',
							   'get_title' => 'true');
							   
		$control_styles = array('width' => 300,
								'height' => 350,
								'id_base' => 'wpfw_flickr_widget');
								
		$widget_change = array('change1' => 'delay',
							   'change2' => 'effect',
							   'change3' => 'slide',
							   'change4' => 100,
							   'change5' => 0);
							   
		$this->WP_Widget( 'wpfw_flickr_widget', 'WP Flickr Widget ', $widget_style, $control_styles );	
	}
		
	function widget( $args, $cur_instance ) {
		extract( $args );
		
		$title = apply_filters( 'widget_title', $cur_instance['title'] );
		$class = $cur_instance['class'];
		$flickrID = $cur_instance['flickrID'];
		$postcount = $cur_instance['postcount'];
		$type = $cur_instance['type'];
		$display = $cur_instance['display'];

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;	
		echo '<div class="'. $class. '">'; ?>
			<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=<?php echo $postcount ?>&amp;display=<?php echo $display ?>&amp;size=s&amp;layout=v&amp;source=<?php echo $type ?>&amp;<?php echo $type ?>=<?php echo $flickrID ?>"></script><?php 
		echo '</div>';	
		echo $after_widget;	
	}
		
	function update( $new_instance, $org_instance ) {
		$cur_instance = $org_instance;
		$cur_instance['title'] = strip_tags( $new_instance['title'] );
		$cur_instance['class'] = strip_tags( $new_instance['class'] );
		$cur_instance['flickrID'] = strip_tags( $new_instance['flickrID'] );
		$cur_instance['show'] = $new_instance['slide'];
		$cur_instance['postcount'] = $new_instance['postcount'];
		$cur_instance['type'] = $new_instance['type'];
		$cur_instance['inline'] = $new_instance['true'];
		$cur_instance['display'] = $new_instance['display'];
		return $cur_instance;
	}
		 
	function form( $cur_instance ) {
		$defaults = array('title' => 'Flickr',
					      'class' => 'flickr',
						  'flickrID' => '65961696@N02',
						  'postcount' => '3',
						  'type' => 'user',
						  'display' => 'latest');
		
		$cur_instance = wp_parse_args( (array) $cur_instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __('Title'); ?>:</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $cur_instance['title']; ?>" />
		</p>		
		
		<p>
			<label for="<?php echo $this->get_field_id( 'class' ); ?>">Name of CSS class:</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" value="<?php echo $cur_instance['class']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'flickrID' ); ?>">Flickr ID: (see <a href="http://idgettr.com/">idGettr</a>)</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'flickrID' ); ?>" name="<?php echo $this->get_field_name( 'flickrID' ); ?>" value="<?php echo $cur_instance['flickrID']; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'postcount' ); ?>">Number of photos:</label>
			<select id="<?php echo $this->get_field_id( 'postcount' ); ?>" name="<?php echo $this->get_field_name( 'postcount' ); ?>" class="widefat">
				<option <?php if ( '1' == $cur_instance['postcount'] ) echo 'selected="selected"'; ?>>1</option>
				<option <?php if ( '2' == $cur_instance['postcount'] ) echo 'selected="selected"'; ?>>2</option>
				<option <?php if ( '3' == $cur_instance['postcount'] ) echo 'selected="selected"'; ?>>3</option>
				<option <?php if ( '4' == $cur_instance['postcount'] ) echo 'selected="selected"'; ?>>4</option>
				<option <?php if ( '5' == $cur_instance['postcount'] ) echo 'selected="selected"'; ?>>5</option>
				<option <?php if ( '6' == $cur_instance['postcount'] ) echo 'selected="selected"'; ?>>6</option>
				<option <?php if ( '7' == $cur_instance['postcount'] ) echo 'selected="selected"'; ?>>7</option>
				<option <?php if ( '8' == $cur_instance['postcount'] ) echo 'selected="selected"'; ?>>8</option>
				<option <?php if ( '9' == $cur_instance['postcount'] ) echo 'selected="selected"'; ?>>9</option>
				<option <?php if ( '9' == $cur_instance['postcount'] ) echo 'selected="selected"'; ?>>10</option>
			</select>		
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>">Type (user or group):</label>
			<select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" class="widefat">	
				<option <?php if ( 'user' == $cur_instance['type'] ) echo 'selected="selected"'; ?>>user</option>
				<option <?php if ( 'group' == $cur_instance['type'] ) echo 'selected="selected"'; ?>>group</option>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'display' ); ?>">Show (random or most recent):</label>
			<select id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>" class="widefat">
				<option <?php if ( 'random' == $cur_instance['display'] ) echo 'selected="selected"'; ?>>random</option>
				<option <?php if ( 'latest' == $cur_instance['display'] ) echo 'selected="selected"'; ?>>latest</option>
			</select>
		</p><?php
	}
}
?>