<?php
function unte_enqueue_styles() {

    $parent_style = 'parent-style'; 

    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ), wp_get_theme()->get('Version')
    );
}

add_action( 'wp_enqueue_scripts', 'unte_enqueue_styles' );


/**
 * Register a custom post type called "Film".
 *
 */
function unte_register_film_type() {
	$labels = array(
        'name'                  => _x( 'Films', 'Post type general name', 'minimal-film' ),
        'singular_name'         => _x( 'Film', 'Post type singular name', 'minimal-film' ),
        'menu_name'             => _x( 'Films', 'Admin Menu text', 'minimal-film' ),
        'name_admin_bar'        => _x( 'Film', 'Add New on Toolbar', 'minimal-film' ),
        'add_new'               => __( 'Add New', 'minimal-film' ),
        'add_new_item'          => __( 'Add New film', 'minimal-film' ),
        'new_item'              => __( 'New Film', 'minimal-film' ),
        'edit_item'             => __( 'Edit Film', 'minimal-film' ),
        'view_item'             => __( 'View Film', 'minimal-film' ),
        'all_items'             => __( 'All Films', 'minimal-film' ),
        'search_items'          => __( 'Search Films', 'minimal-film' ),
        'parent_item_colon'     => __( 'Parent Films:', 'minimal-film' ),
        'not_found'             => __( 'No Films found.', 'minimal-film' ),
        'not_found_in_trash'    => __( 'No Films found in Trash.', 'minimal-film' ),
  
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'film' ),
        'capability_type'    => 'post',
        'taxonomies'         => array(),
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail' ),
    );
 
    register_post_type( 'film', $args );

}

add_action( 'init', 'unte_register_film_type' );


/**
 * Register Multiple Taxonomies
 *
 */
function unte_register_taxonomies() {
	$taxonomies = array(
		array(
			'slug'         => 'genre',
			'single_name'  => 'Genre',
			'plural_name'  => 'Genres',
			'post_type'    => 'film',
			'rewrite'      => array( 'slug' => 'gennre' ),
		),
		array(
			'slug'         => 'country',
			'single_name'  => 'Country',
			'plural_name'  => 'Countries',
			'post_type'    => 'film',
			'rewrite'      => array( 'slug' => 'country' ),
		),
		array(
			'slug'         => 'year',
			'single_name'  => 'Year',
			'plural_name'  => 'Years',
			'post_type'    => 'film',
			'rewrite'      => array( 'slug' => 'year' ),
		),
		array(
			'slug'         => 'actor',
			'single_name'  => 'Actor',
			'plural_name'  => 'Actors',
			'post_type'    => 'film',
			'rewrite'      => array( 'slug' => 'actor' ),
		),
	);
	foreach( $taxonomies as $taxonomy ) {
		$labels = array(
			'name' => $taxonomy['plural_name'],
			'singular_name' => $taxonomy['single_name'],
			'search_items' =>  'Search ' . $taxonomy['plural_name'],
			'all_items' => 'All ' . $taxonomy['plural_name'],
			'parent_item' => 'Parent ' . $taxonomy['single_name'],
			'parent_item_colon' => 'Parent ' . $taxonomy['single_name'] . ':',
			'edit_item' => 'Edit ' . $taxonomy['single_name'],
			'update_item' => 'Update ' . $taxonomy['single_name'],
			'add_new_item' => 'Add New ' . $taxonomy['single_name'],
			'new_item_name' => 'New ' . $taxonomy['single_name'] . ' Name',
			'menu_name' => $taxonomy['plural_name']
		);
		
		$rewrite = isset( $taxonomy['rewrite'] ) ? $taxonomy['rewrite'] : array( 'slug' => $taxonomy['slug'] );
		$hierarchical = isset( $taxonomy['hierarchical'] ) ? $taxonomy['hierarchical'] : true;
	
		register_taxonomy( $taxonomy['slug'], $taxonomy['post_type'], array(
			'hierarchical' => $hierarchical,
			'labels' => $labels,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => $rewrite,
		));
	}
	
}
add_action( 'init', 'unte_register_taxonomies' );

/** 
* Add Custom Metabox
*
*/

function unte_meta_boxes() {
	add_meta_box( 'ticket-price', 'Ticket Price', 'add_meta_price_field', 'film', 'advanced', 'default' );
	add_meta_box( 'release-date', 'Release Date', 'add_meta_release_field', 'film', 'advanced', 'default' );
}

add_action( 'add_meta_boxes', 'unte_meta_boxes' );

function add_meta_price_field($post) { 
	wp_nonce_field( 'unte_nonce_action', 'unte_nonce' ); ?>
    <div class="form-group">
        <p class="meta-title">Ticket Price</p>
        <input class="unte-text-input" id="ticket-price" type="text" name="unte_meta_options_ticket" value="<?php echo get_post_meta( $post->ID, '_unte_meta_key_ticket', true ); ?>" />
        <p class="meta-description">This is the price of the Ticket</p>
    </div>
<?php
}

function add_meta_release_field($post) { 
	wp_nonce_field( 'unte_nonce_action', 'unte_nonce' ); ?>
    <div class="form-group">
        <p class="meta-title">Release Date</p>
        <input class="unte-text-input" id="release-date" type="text" name="unte_meta_options_release" value="<?php echo get_post_meta( $post->ID, '_unte_meta_key_release', true ); ?>" placeholder="30/12/2018 put it this format" />
        <p class="meta-description">This is Release Date</p>
    </div>
<?php
}


/**
 * Handles saving the meta box.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return null
 */

function unte_save_metabox( $post_id ) {
    
    // Add nonce for security and authentication.
    $nonce_name   = isset( $_POST['unte_nonce'] ) ? $_POST['unte_nonce'] : '';
    $nonce_action = 'unte_nonce_action';

    // Check if nonce is set.
    if ( ! isset( $nonce_name ) ) {
        return;
    }

    // Check if nonce is valid.
    if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
        return;
    }

    // Check if user has permissions to save data.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Check if not an autosave.
    if ( wp_is_post_autosave( $post_id ) ) {
        return;
    }

    // Check if not a revision.
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }

    
    $post_meta_array = array();
  	
    $post_meta_array['_unte_meta_key_ticket'] = isset( $_POST['unte_meta_options_ticket'] ) ? $_POST['unte_meta_options_ticket'] : '';
    $post_meta_array['_unte_meta_key_release'] = isset( $_POST['unte_meta_options_release'] ) ? $_POST['unte_meta_options_release'] : '';

    foreach ($post_meta_array as $meta_key => $meta_value) {
       update_post_meta( $post_id, $meta_key, $meta_value );
    }   
    
}

add_action( 'save_post', 'unte_save_metabox' );


// Adding action to Archive page
function archive_ticket_meta_value() {
	global $post;
	echo '<p><strong>Ticket Price: </strong>' . get_post_meta( $post->ID, '_unte_meta_key_ticket', true ) . '</p>';
}
add_action( 'archive_ticket_price', 'archive_ticket_meta_value' );

function archive_release_meta_value() {
	global $post;
	echo '<p><strong>Release Date: </strong>' . get_post_meta( $post->ID, '_unte_meta_key_release', true ) . '</p>';
}
add_action( 'archive_release_date', 'archive_release_meta_value' );

function archive_country_term() { 
	global $post;
	?>

	<p>
		<strong>Country: </strong>
		<ul>	
			<?php
			$country = get_the_terms( $post->ID, 'country');
			
			foreach ($country as $term) {
				echo '<li>' . $term->name . '</li>';
			}
			?>
		</ul>
	</p>

<?php 
}
add_action( 'archive_country', 'archive_country_term' );


function archive_genre_term() { 
	global $post;
	?>

	<p>
		<strong>Genre: </strong>
		<ul>	
			<?php
			$genre = get_the_terms( $post->ID, 'genre');
			
			foreach ($genre as $term) {
				echo '<li>' . $term->name . '</li>';
			}
			?>
		</ul>
	</p>

<?php 
}

add_action( 'archive_genre', 'archive_genre_term' );


function unte_film_shortcode( $atts ) {
		
	$args = array( 'post_type' => 'film',
				   'posts_per_page' => 5,
				 );

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) : 

	$output = '';

		$output .= '<div class="container-fluid"><div class="row"><div class="col-md-12">';
					while ( $query->have_posts() ) : $query->the_post();

						$output .= '<div class="film-widget-wrapper">
									<h3 class="film-widget-title">
									<a href="'. get_the_permalink() . '">' . get_the_title() . '</a></h3>
						    <p class="film-widget-text">' . get_the_content() . '</p>
						</div>';
					
					 endwhile;
					wp_reset_postdata();
		
		$output .= '</div></div></div>';


	endif;

	return $output;
}

add_shortcode( 'film', 'unte_film_shortcode' );	
		