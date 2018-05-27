<?php

/******************************************************************************
 *
 *   Adds Post Templates to individual posts. These behave like Page Templates. 
 *   The Templates themselves are created separately and kept in the 
 *   page-templates/ folder.
 *
 *   Thanks to Nathan Rice and Single Post Template plugin for the code: 
 *   https://wordpress.org/plugins/single-post-template/
 *
 ******************************************************************************/

class Imagely_Post_Templates {

	function __construct() {

		add_action( 'admin_menu', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'metabox_save' ), 1, 2 );
		add_filter( 'single_template', array( $this, 'get_post_template' ) );

	}

	function get_post_template( $template ) {

		global $post;

		$custom_field = get_post_meta( $post->ID, '_wp_post_template', true );

		if( ! $custom_field )
			return $template;

		/* Prevent directory traversal */
		$custom_field = str_replace( '..', '', $custom_field );

		if( file_exists( get_stylesheet_directory()  . DIRECTORY_SEPARATOR . "{$custom_field}" ) )
			$template = get_stylesheet_directory()  . DIRECTORY_SEPARATOR . "{$custom_field}";
		elseif( file_exists( get_template_directory()  . DIRECTORY_SEPARATOR . "{$custom_field}" ) )
			$template = get_template_directory()  . DIRECTORY_SEPARATOR . "{$custom_field}";

		return $template;

	}

	function get_post_templates() {

		$templates = wp_get_theme()->get_files( 'php', 1 );
		$post_templates = array();

		$base = array( trailingslashit( get_template_directory() ), trailingslashit( get_stylesheet_directory() ) );

		foreach ( (array) $templates as $file => $full_path ) {

			if ( $full_path == __FILE__ ) 
				continue;

			if ( ! preg_match( '|Single Post Template:(.*)$|mi', file_get_contents( $full_path ), $header ) )
				continue; 

			$post_templates[ $file ] = _cleanup_header_comment( $header[1] );

		}

		return $post_templates;

	}

	function post_templates_dropdown() {

		global $post;

		$post_templates = $this->get_post_templates();

		/* Loop through templates, make them options */
		foreach ( (array) $post_templates as $template_file => $template_name ) {
			$selected = ( $template_file == get_post_meta( $post->ID, '_wp_post_template', true ) ) ? ' selected="selected"' : '';
			$opt = '<option value="' . esc_attr( $template_file ) . '"' . $selected . '>' . esc_html( $template_name ) . '</option>';
			echo $opt;
		}

	}

	function add_metabox() {

		if ( $this->get_post_templates() )
			add_meta_box( 'imagely_post_templates', __( 'Single Post Template', 'imagely-rebel' ), array( $this, 'metabox' ), 'post', 'side', 'low' );

	}

	function metabox( $post ) {

		?>
		<input type="hidden" name="imagely_noncename" id="imagely_noncename" value="<?php echo wp_create_nonce( 'post_template-' . $post->ID ); ?>" />

		<label class="hidden" for="post_template"><?php  _e( 'Post Template', 'imagely-rebel' ); ?></label><br />
		<select name="_wp_post_template" id="post_template" class="dropdown">
			<option value=""><?php _e( 'Default', 'imagely-rebel' ); ?></option>
			<?php $this->post_templates_dropdown(); ?>
		</select><br /><br />
		<p><?php _e( 'Choose a template for your post layout.', 'imagely-rebel' ); ?></p>
		<?php

	}

	function metabox_save( $post_id, $post ) {

		/**
		 * Verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times
		 **/
		
		if ( ! isset( $_POST['imagely_noncename'] ) )
			return $post->ID;

		if ( ! wp_verify_nonce( $_POST['imagely_noncename'], 'post_template-' . $post->ID ) )
			return $post->ID;

		/* Is the user allowed to edit the post or page? */
		if ( !empty($_POST['post_type']) && 'page' == $_POST['post_type'] )
			if ( ! current_user_can( 'edit_page', $post->ID ) )
				return $post->ID;
		else
			if ( ! current_user_can( 'edit_post', $post->ID ) )
				return $post->ID;

		/* OK, we're authenticated: we need to find and save the data */

		/* If _wp_post_template hasn't been set, just return */
		if ( ! isset( $_POST['_wp_post_template'] ) )
			return $post->ID;

		/* Otherwise put the data into an array to make it easier to loop though and save */
		$mydata['_wp_post_template'] = $_POST['_wp_post_template'];

		/* Add values of $mydata as custom fields */
		foreach ( $mydata as $key => $value ) {

			/* Don't store custom data twice */
			if( 'revision' == $post->post_type )
				return;

			/* If $value is an array, make it a CSV (unlikely) */
			$value = implode( ',', (array) $value );

			/* Update the data if it exists, or add it if it doesn't */
			if( get_post_meta( $post->ID, $key, false ) )
				update_post_meta( $post->ID, $key, $value );
			else
				add_post_meta( $post->ID, $key, $value );

			/* Delete if blank */
			if( ! $value )
				delete_post_meta( $post->ID, $key );
		}
	}

}

/* Instantiate the class after theme has been set up. */
add_action( 'after_setup_theme', 'imagely_post_templates_class_init' );
function imagely_post_templates_class_init() {
    if (!class_exists('Single_Post_Template_Plugin')) new Imagely_Post_Templates;
}