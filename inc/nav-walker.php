<?php
/**
 * Navigation Menu Walker & Primary Menu Fallback
 *
 * What: Custom nav walker that adds 'active' CSS classes, plus a fallback
 *       menu rendered when no primary nav is assigned in WP admin.
 * Who calls it: header.php — wp_nav_menu() with walker => new Peptide_Starter_Nav_Walker(),
 *               and the callback_function parameter for the fallback.
 * Dependencies: Walker_Nav_Menu (WordPress core).
 *
 * Extracted from inc/helpers.php in v2.3.0 to keep both files under 300 lines.
 *
 * @package peptide-starter
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Navigation Menu Walker — adds active classes.
 *
 * What: Renders menu items with an 'active' class for current + ancestor items.
 * Who calls it: wp_nav_menu() in header.php with walker => new instance.
 * Dependencies: Walker_Nav_Menu.
 */
class Peptide_Starter_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Render a single menu item.
	 *
	 * @param string   $output Item output accumulator (passed by reference).
	 * @param WP_Post  $item   Menu item data.
	 * @param int      $depth  Menu depth.
	 * @param stdClass $args   Menu arguments.
	 * @param int      $id     Current item ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$indent    = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		if ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current-menu-parent', $classes, true ) ) {
			$classes[] = 'active';
		}

		$args        = apply_filters( 'nav_menu_item_args', $args, $item, $depth );
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$item_id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$item_id = $item_id ? ' id="' . esc_attr( $item_id ) . '"' : '';

		$output .= $indent . '<li' . $item_id . $class_names . '>';

		$atts = array(
			'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'target' => ! empty( $item->target ) ? $item->target : '',
			'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
			'href'   => ! empty( $item->url ) ? $item->url : '',
		);

		$atts       = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
		$attributes = '';

		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$link  = $args->before;
		$link .= '<a' . $attributes . '>';
		$link .= $args->link_before . $title . $args->link_after;
		$link .= '</a>';
		$link .= $args->after;

		$output .= $link . "</li>\n";
	}
}

/**
 * Fallback menu when no primary nav is assigned in WP admin.
 *
 * Previously lived inline inside header.php; moved to inc/helpers.php in
 * v1.5.1 to prevent redeclaration, then extracted here in v2.3.0.
 *
 * @return void Echoes a complete <ul>.
 */
function peptide_starter_primary_menu_fallback() {
	?>
	<ul>
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'peptide-starter' ); ?></a></li>
		<li class="menu-item-has-children">
			<a href="#"><?php esc_html_e( 'Tools', 'peptide-starter' ); ?></a>
			<ul class="sub-menu">
				<li><a href="<?php echo esc_url( home_url( '/calculator' ) ); ?>"><?php esc_html_e( 'Calculator', 'peptide-starter' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/protocol-builder' ) ); ?>"><?php esc_html_e( 'Protocol Builder', 'peptide-starter' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/tracker' ) ); ?>"><?php esc_html_e( 'Tracker', 'peptide-starter' ); ?></a></li>
			</ul>
		</li>
		<li class="menu-item-has-children">
			<a href="#"><?php esc_html_e( 'My Data', 'peptide-starter' ); ?></a>
			<ul class="sub-menu">
				<li><a href="<?php echo esc_url( home_url( '/peptides' ) ); ?>"><?php esc_html_e( 'Peptides', 'peptide-starter' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/subject-log' ) ); ?>"><?php esc_html_e( 'Subject Log', 'peptide-starter' ); ?></a></li>
			</ul>
		</li>
		<li class="menu-item-has-children">
			<a href="#"><?php esc_html_e( 'Resources', 'peptide-starter' ); ?></a>
			<ul class="sub-menu">
				<li><a href="<?php echo esc_url( home_url( '/documentation' ) ); ?>"><?php esc_html_e( 'Documentation', 'peptide-starter' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/news' ) ); ?>"><?php esc_html_e( 'Science Feed', 'peptide-starter' ); ?></a></li>
			</ul>
		</li>
	</ul>
	<?php
}
