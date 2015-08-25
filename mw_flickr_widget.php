<?php
/*
Plugin Name: Magical World Flickr Plugin
Plugin URI: https://github.com/jcberthon/mw-flickr
Description: Display photos for Flickr (filtering possible) in a widget, one can place in a compatible Theme.
Version: 1.2
Author: Jean-Christophe Berthon
Author URI: http://www.berthon.eu/
License: GPL3
*/

/*
    Wordpress Plug-in to interact with Flickr
    Copyright (C) 2012-2015  Jean-Christophe Berthon

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class MW_Flickr_Widget extends WP_Widget {

  const FLCKR_API_KEY            = '0fce216627cfb9e95bc32abd0594e483';
  const FLCKR_API_REST_URL       = 'https://api.flickr.com/services/rest/';
  const FLCKR_SQR_THUMBNAIL_SIZE = 75;
  /* TODO: add other sizes here */
  const FLCKR_URL_SCHEME         = 'https://';
  const FLCKR_IMG_URL_DOMAIN     = '.staticflickr.com';
  const FLCKR_PGE_URL_DOMAIN     = 'www.flickr.com';

  /**
   * Register widget with WordPress.
   */
  public function __construct() {
    parent::__construct(
      'mw_flickr_widget', // Base ID
      'Wordpress Flickr Widget', // Name
      array( 'description' => __( 'Display photos from Flickr.', 'text_domain' ) ) // Args
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    extract( $args );
    $title     = apply_filters( 'widget_title', $instance['title'] );
    $class     = $instance['class'];
    $flickrID  = $instance['flickrID'];
    $postcount = $instance['postcount'];
    $type      = $instance['type'];
    $display   = $instance['display'];
    $size      = $instance['size'];
    $tag       = $instance['tag'];
    $fapi      = $instance['fapi'];

    echo $before_widget;
    if ( ! empty( $title ) ) {
      echo $before_title . $title . $after_title;
    }
    echo '<div class="' . $class . '">';

    if ( $fapi ) {
      /* TODO: verify browser compatibility with the CSS3 'transition' */
      ?>
      <style>
        div.<?php echo $class; ?> img {
          opacity:0;
          -webkit-transition: opacity 2s;
          -moz-transition: opacity 2s;
          -o-transition: opacity 2s;
          transition: opacity 2s;
        }

        div.<?php echo $class; ?> img.fadein {
          opacity:1;
        }
      </style>
      <?php
      for ($i = 0; $i < $postcount; $i++) {
        /* TODO:
         *   1. use constant for the img dimensions, depending on user's choice
         *   2. use javascript, when mouse hover link, call preconnect at least...
         */
        echo '<a id="flckrlnk' . $i . '" href="#" rel="external">';
        echo '  <img id="flckrmg' . $i . '" width="75" height="75" />';
        echo '</a>';
      }
      /* TODO: in the JS code, only do random if requested by user
       * Only random is currently implemented, implement latest as well.
       * Random is implemented so it looks random. An area for improvement!
       */
      ?>
      <script type="text/javascript">
      function jsonFlickrApi(rsp) {
        if (rsp.stat != "ok"){
          return;
        }

        for (var i = 0; i < <?php echo $postcount; ?> ; i++) {
          var rand = Math.random();
          rand = rand * 128;
          rand = Math.ceil(rand);

          var photo = rsp.photos.photo[ rand ];

          var i_url = "<?php echo self::FLCKR_URL_SCHEME; ?>farm" + photo.farm +
              "<?php echo self::FLCKR_IMG_URL_DOMAIN; ?>/" + photo.server + "/" +
              photo.id + "_" + photo.secret + "_s.jpg";
          var a_url = "<?php echo self::FLCKR_URL_SCHEME . self::FLCKR_PGE_URL_DOMAIN; ?>/photos/" +
              photo.owner + "/" + photo.id + "/";

          var image = document.getElementById('flckrmg' + i);
          image.src = i_url;
          image.title = photo.title;
          image.alt = "Photo by Magical-World with title: " + photo.title;
          if (image.className) {
            image.className = '';
          } else {
            image.className = 'fadein';
          }
          document.getElementById('flckrlnk' + i).href = a_url;
        }
      }
      </script>
      <?php
      echo '<script async type="text/javascript"';
      /* TODO: here we should call other functions of the Flickr API if need be */
      echo '  src="' . self::FLCKR_API_REST_URL . '/?format=json&sort=interestingness-desc&' .
          'user_id=' . $flickrID . '&method=flickr.photos.search&page=1&per_page=128&api_key=' .
          self::FLCKR_API_KEY . '">';
      echo '</script>';

    }
    else {
      ?>
      <!-- Preconnect to the images farm servers to increase parallelism and performance -->
      <link href="https://farm1.staticflickr.com" rel="preconnect" pr="0.5">
      <link href="https://farm2.staticflickr.com" rel="preconnect" pr="0.5">
      <link href="https://farm3.staticflickr.com" rel="preconnect" pr="0.75">
      <link href="https://farm4.staticflickr.com" rel="preconnect" pr="0.75">
      <link href="https://farm5.staticflickr.com" rel="preconnect" pr="0.5">
      <?php
      if ( empty( $tag ) ) {
        ?>
        <script
          type="text/javascript"
          src="https://www.flickr.com/badge_code_v2.gne?count=<?php echo $postcount ?>&amp;display=<?php echo $display ?>&amp;size=<?php echo $size ?>&amp;layout=v&amp;source=<?php echo $type ?>&amp;<?php echo $type ?>=<?php echo $flickrID ?>">
        </script>
        <?php
      }
      elseif ( ! empty( $tag ) ) {
        ?>
        <script
          type="text/javascript"
          src="https://www.flickr.com/badge_code_v2.gne?count=<?php echo $postcount ?>&amp;display=<?php echo $display ?>&amp;size=<?php echo $size ?>&amp;layout=v&amp;source=<?php echo $type ?>_tag&amp;<?php echo $type ?>=<?php echo $flickrID ?>&amp;tag=<?php echo $tag ?>">
        </script>
        <?php
      }
    }

    echo '</div>';
    echo $after_widget;
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title']     = strip_tags( $new_instance['title'] );
    $instance['class']     = strip_tags( $new_instance['class'] );
    $instance['flickrID']  = self::sanitize( strip_tags( $new_instance['flickrID'] ) );
    $instance['show']      = $new_instance['slide'];
    $instance['postcount'] = $new_instance['postcount'];
    $instance['type']      = isset( $new_instance['type'] ) ? $new_instance['type'] : __( 'user', 'text_domain' );
    $size = $new_instance['size'];
    if ( 'thumbnail' == $size )
      $instance['size']    = 't';
    elseif ( 'square' == $size )
      $instance['size']    = 's';
    elseif ( 'mid-size' == $size )
      $instance['size']    = 'm';
    else
      $instance['size']    = 's';
    $instance['tag']       = self::sanitize( strip_tags( $new_instance['tag'] ) );
    $instance['inline']    = $new_instance['true'];
    $instance['display']   = $new_instance['display'];
    $fapi = $new_instance['fapi'];
    if ( 'Flickr API' == $fapi )
      $instance['fapi'] = True;
    elseif ( 'Flickr Badge' == $fapi )
      $instance['fapi'] = False;
    else
      $instance['fapi'] = True;


    return $instance;
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    $title      = isset( $instance[ 'title' ] )     ? $instance[ 'title' ]     : __( 'Photos', 'text_domain' );
    $class      = isset( $instance[ 'class' ] )     ? $instance[ 'class' ]     : __( 'flickr', 'text_domain' );
    $flickr_id  = isset( $instance[ 'flickrID' ] )  ? $instance[ 'flickrID' ]  : '10630381@N03';
    $post_count = isset( $instance[ 'postcount' ] ) ? $instance[ 'postcount' ] : 5;
    $type       = isset( $instance[ 'type' ] )      ? $instance[ 'type' ]      : __( 'user', 'text_domain' );
    $display    = isset( $instance[ 'display' ] )   ? $instance[ 'display' ]   : __( 'latest', 'text_domain' );
    $size       = isset( $instance[ 'size' ] )      ? $instance[ 'size' ]      : __( 's', 'text_domain' );
    $tag        = isset( $instance[ 'tag' ] )       ? $instance[ 'tag' ]       : __( '', 'text_domain' );
    $fapi       = isset( $instance[ 'fapi'] )       ? $instance[ 'fapi' ]       : True;
    ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'class' ); ?>"><?php _e( 'Name of CSS class:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" type="text" value="<?php echo esc_attr( $class ); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'flickrID' ); ?>"><?php _e( 'Flickr ID: (see ' ); ?><a href="http://idgettr.com/" title="Get your Flickr ID">idGettr</a>)</label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'flickrID' ); ?>" name="<?php echo $this->get_field_name( 'flickrID' ); ?>" type="text" value="<?php echo esc_attr( $flickr_id ); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'postcount' ); ?>"><?php _e( 'Number of photos:' ); ?></label>
      <select class="widefat" id="<?php echo $this->get_field_id( 'postcount' ); ?>" name="<?php echo $this->get_field_name( 'postcount' ); ?>">
        <option <?php if (  1 == $post_count ) echo 'selected="selected"'; ?>>1</option>
        <option <?php if (  2 == $post_count ) echo 'selected="selected"'; ?>>2</option>
        <option <?php if (  3 == $post_count ) echo 'selected="selected"'; ?>>3</option>
        <option <?php if (  4 == $post_count ) echo 'selected="selected"'; ?>>4</option>
        <option <?php if (  5 == $post_count ) echo 'selected="selected"'; ?>>5</option>
        <option <?php if (  6 == $post_count ) echo 'selected="selected"'; ?>>6</option>
        <option <?php if (  7 == $post_count ) echo 'selected="selected"'; ?>>7</option>
        <option <?php if (  8 == $post_count ) echo 'selected="selected"'; ?>>8</option>
        <option <?php if (  9 == $post_count ) echo 'selected="selected"'; ?>>9</option>
        <option <?php if ( 10 == $post_count ) echo 'selected="selected"'; ?>>10</option>
      </select>
    </p>
    <!--
    <p>
      <label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Type (user or group):' ); ?></label>
      <select class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
        <option <?php if ( 'user'  == $type ) echo 'selected="selected"'; ?>>user</option>
        <option <?php if ( 'group' == $type ) echo 'selected="selected"'; ?>>group</option>
      </select>
    </p>
    -->
    <p>
      <label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e( 'Show (random or latest):' ); ?></label>
      <select class="widefat" id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>">
        <option <?php if ( 'random' == $display ) echo 'selected="selected"'; ?>>random</option>
        <option <?php if ( 'latest' == $display ) echo 'selected="selected"'; ?>>latest</option>
      </select>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Size of photos:' ); ?></label>
      <select class="widefat" id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>">
        <option <?php if ( 's' == $size ) echo 'selected="selected"'; ?>>square</option>
        <option <?php if ( 't' == $size ) echo 'selected="selected"'; ?>>thumbnail</option>
        <option <?php if ( 'm' == $size ) echo 'selected="selected"'; ?>>mid-size</option>
      </select>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'tag' ); ?>"><?php _e( 'Filter by tag (leave empty for no filter):' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'tag' ); ?>" name="<?php echo $this->get_field_name( 'tag' ); ?>" type="text" value="<?php echo esc_attr( $tag ); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'fapi' ); ?>"><?php _e( 'Use Flickr API (modern way) or old Flickr Badge JS:' ); ?></label>
      <select class="widefat" id="<?php echo $this->get_field_id( 'fapi' ); ?>" name="<?php echo $this->get_field_name( 'fapi' ); ?>">
        <option <?php if ( True == $fapi )  echo 'selected="selected"'; ?>>Flickr API</option>
        <option <?php if ( False == $fapi ) echo 'selected="selected"'; ?>>Flickr Badge</option>
      </select>
    </p>
    <?php
  }

  /**
   * Function: sanitize
   * Returns a sanitized string, typically for URLs.
   *
   * Parameters:
   *     $string - The string to sanitize.
   *     $force_lowercase - Force the string to lowercase?
   *     $anal - If set to *true*, will remove all non-alphanumeric characters.
   */
  private static function sanitize($string, $force_lowercase = false, $anal = false) {
    $strip = array("~", "`", "!", "#", "$", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
        "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
        "â€”", "â€“", ",", "<", ".", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($string)));
    $clean = preg_replace('/\@/','%40',$clean);      // Replace @ by %40
    $clean = preg_replace('/\s[\s]+/', "-", $clean); // Strip off multiple spaces
    $clean = preg_replace('/^[\-]+/','',$clean);     // Strip off the starting hyphens
    $clean = preg_replace('/[\-]+$/','',$clean);     // Strip off the ending hyphens
    $clean = ($anal) ? preg_replace('/[\s\W]+/','-',$clean) : $clean ; // Strip off spaces and non-alpha-numeric
    return ($force_lowercase) ?
      (function_exists('mb_strtolower')) ?
        mb_strtolower($clean, 'UTF-8') :
        strtolower($clean) :
      $clean;
  }


} // class MW_Flickr_Widget

// register MW_Flickr_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "mw_flickr_widget" );' ) );

?>
