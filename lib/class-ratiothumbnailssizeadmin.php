<?php
/**
 * Ratio Thumbnails Size
 *
 * @package    Ratio Thumbnails Size
 * @subpackage RatioThumbnailsSizeAdmin Management screen
	Copyright (c) 2019- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$ratiothumbnailssizeadmin = new RatioThumbnailsSizeAdmin();

/** ==================================================
 * Management screen
 */
class RatioThumbnailsSizeAdmin {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 10, 2 );
	}

	/** ==================================================
	 * Add a "Settings" link to the plugins page
	 *
	 * @param  array  $links  links array.
	 * @param  string $file   file.
	 * @return array  $links  links array.
	 * @since 1.00
	 */
	public function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty( $this_plugin ) ) {
			$this_plugin = 'ratio-thumbnails-size/ratiothumbnailssize.php';
		}
		if ( $file === $this_plugin ) {
			$links[] = '<a href="' . admin_url( 'options-general.php?page=ratiothumbnailssize' ) . '">' . __( 'Settings' ) . '</a>';
		}
			return $links;
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function plugin_menu() {
		add_options_page( 'Ratio Thumbnails Size Options', 'Ratio Thumbnails Size', 'manage_options', 'ratiothumbnailssize', array( $this, 'plugin_options' ) );
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function plugin_options() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		$this->options_updated();

		$scriptname = admin_url( 'options-general.php?page=ratiothumbnailssize' );
		$ratiothumbnailssize_settings = get_option( 'ratiothumbnailssize', 'digicame' );
		$ratiothumbnailssize_disable_settings = get_option( 'ratiothumbnailssize_disable', array() );

		?>

		<div class="wrap">
		<h2>Ratio Thumbnails Size</h2>

			<details>
			<summary><strong><?php esc_html_e( 'Various links of this plugin', 'ratio-thumbnails-size' ); ?></strong></summary>
			<?php $this->credit(); ?>
			</details>

			<div class="wrap">
				<h3><?php esc_html_e( 'Settings' ); ?></h3>	

				<h3><?php esc_html_e( 'Ratio at the time of thumbnail generation', 'ratio-thumbnails-size' ); ?></h3>
				<form method="post" action="<?php echo esc_url( $scriptname ); ?>">
				<?php wp_nonce_field( 'rts_set', 'ratiothumbnailssize_set' ); ?>

				<div style="display: block;padding:5px 5px">
				<input type="radio" name="ratio" value="silver" 
				<?php
				if ( 'silver' === $ratiothumbnailssize_settings ) {
					echo 'checked';
				}
				?>
				>1.414 : 1
				<?php esc_html_e( 'Silver ratio', 'ratio-thumbnails-size' ); ?>
				</div>

				<div style="display: block;padding:5px 5px">
				<input type="radio" name="ratio" value="golden" 
				<?php
				if ( 'golden' === $ratiothumbnailssize_settings ) {
					echo 'checked';
				}
				?>
				>1.618 : 1
				<?php esc_html_e( 'Golden ratio', 'ratio-thumbnails-size' ); ?>
				</div>

				<div style="display: block;padding:5px 5px">
				<input type="radio" name="ratio" value="ogp_fb_tw" 
				<?php
				if ( 'ogp_fb_tw' === $ratiothumbnailssize_settings ) {
					echo 'checked';
				}
				?>
				>1.91 : 1
				<?php esc_html_e( 'OGP image ratio for Facebook and Twitter', 'ratio-thumbnails-size' ); ?>
				</div>

				<div style="display: block;padding:5px 5px">
				<input type="radio" name="ratio" value="film" 
				<?php
				if ( 'film' === $ratiothumbnailssize_settings ) {
					echo 'checked';
				}
				?>
				>3 : 2
				<?php esc_html_e( 'Film camera', 'ratio-thumbnails-size' ); ?>
				</div>

				<div style="display: block;padding:5px 5px">
				<input type="radio" name="ratio" value="digicame" 
				<?php
				if ( 'digicame' === $ratiothumbnailssize_settings ) {
					echo 'checked';
				}
				?>
				>4 : 3
				<?php esc_html_e( 'Digital camera', 'ratio-thumbnails-size' ); ?>
				</div>

				<div style="display: block;padding:5px 5px">
				<input type="radio" name="ratio" value="hd" 
				<?php
				if ( 'hd' === $ratiothumbnailssize_settings ) {
					echo 'checked';
				}
				?>
				>16 : 9
				<?php esc_html_e( 'HDTV', 'ratio-thumbnails-size' ); ?>
				</div>

				<div style="display: block;padding:5px 5px">
				<input type="radio" name="ratio" value="org" 
				<?php
				if ( 'org' === $ratiothumbnailssize_settings ) {
					echo 'checked';
				}
				?>
				>
				<?php esc_html_e( 'Same ratio as original image', 'ratio-thumbnails-size' ); ?>
				</div>

				<hr>

				<h3><?php esc_html_e( 'Override original settings', 'ratio-thumbnails-size' ); ?></h3>

				<?php
				$list_thumbnails = get_intermediate_image_sizes();
				foreach ( $list_thumbnails as $value ) {
					if ( in_array( $value, $ratiothumbnailssize_disable_settings ) ) {
						$check = 1;
					} else {
						$check = 0;
					}
					?>
					<div style="display: block;padding:5px 5px"><input type="checkbox" name="disable_ratios[<?php echo esc_attr( $value ); ?>]" value="1" <?php checked( '1', $check ); ?> /><?php echo esc_html( $value ); ?></div>
					<?php
				}

				submit_button( __( 'Save Changes' ), 'large', 'Manageset', true );

				?>
				</form>

			</div>

		</div>
		<?php
	}

	/** ==================================================
	 * Credit
	 *
	 * @since 1.00
	 */
	private function credit() {

		$plugin_name    = null;
		$plugin_ver_num = null;
		$plugin_path    = plugin_dir_path( __DIR__ );
		$plugin_dir     = untrailingslashit( wp_normalize_path( $plugin_path ) );
		$slugs          = explode( '/', $plugin_dir );
		$slug           = end( $slugs );
		$files          = scandir( $plugin_dir );
		foreach ( $files as $file ) {
			if ( '.' === $file || '..' === $file || is_dir( $plugin_path . $file ) ) {
				continue;
			} else {
				$exts = explode( '.', $file );
				$ext  = strtolower( end( $exts ) );
				if ( 'php' === $ext ) {
					$plugin_datas = get_file_data(
						$plugin_path . $file,
						array(
							'name'    => 'Plugin Name',
							'version' => 'Version',
						)
					);
					if ( array_key_exists( 'name', $plugin_datas ) && ! empty( $plugin_datas['name'] ) && array_key_exists( 'version', $plugin_datas ) && ! empty( $plugin_datas['version'] ) ) {
						$plugin_name    = $plugin_datas['name'];
						$plugin_ver_num = $plugin_datas['version'];
						break;
					}
				}
			}
		}
		$plugin_version = __( 'Version:' ) . ' ' . $plugin_ver_num;
		/* translators: FAQ Link & Slug */
		$faq       = sprintf( __( 'https://wordpress.org/plugins/%s/faq', 'ratio-thumbnails-size' ), $slug );
		$support   = 'https://wordpress.org/support/plugin/' . $slug;
		$review    = 'https://wordpress.org/support/view/plugin-reviews/' . $slug;
		$translate = 'https://translate.wordpress.org/projects/wp-plugins/' . $slug;
		$facebook  = 'https://www.facebook.com/katsushikawamori/';
		$twitter   = 'https://twitter.com/dodesyo312';
		$youtube   = 'https://www.youtube.com/channel/UC5zTLeyROkvZm86OgNRcb_w';
		$donate    = __( 'https://shop.riverforest-wp.info/donate/', 'ratio-thumbnails-size' );

		?>
		<span style="font-weight: bold;">
		<div>
		<?php echo esc_html( $plugin_version ); ?> | 
		<a style="text-decoration: none;" href="<?php echo esc_url( $faq ); ?>" target="_blank" rel="noopener noreferrer">FAQ</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $support ); ?>" target="_blank" rel="noopener noreferrer">Support Forums</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $review ); ?>" target="_blank" rel="noopener noreferrer">Reviews</a>
		</div>
		<div>
		<a style="text-decoration: none;" href="<?php echo esc_url( $translate ); ?>" target="_blank" rel="noopener noreferrer">
		<?php
		/* translators: Plugin translation link */
		echo esc_html( sprintf( __( 'Translations for %s' ), $plugin_name ) );
		?>
		</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-facebook"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-twitter"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-video-alt3"></span></a>
		</div>
		</span>

		<div style="width: 250px; height: 180px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
		<h3><?php esc_html_e( 'Please make a donation if you like my work or would like to further the development of this plugin.', 'ratio-thumbnails-size' ); ?></h3>
		<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
		<button type="button" style="margin: 5px; padding: 5px;" onclick="window.open('<?php echo esc_url( $donate ); ?>')"><?php esc_html_e( 'Donate to this plugin &#187;' ); ?></button>
		</div>

		<?php
	}

	/** ==================================================
	 * Update wp_options table.
	 *
	 * @since 1.00
	 */
	private function options_updated() {

		if ( isset( $_POST['Manageset'] ) && ! empty( $_POST['Manageset'] ) ) {
			if ( check_admin_referer( 'rts_set', 'ratiothumbnailssize_set' ) ) {
				if ( isset( $_POST['ratio'] ) && ! empty( $_POST['ratio'] ) ) {
					$ratiothumbnailssize_settings = sanitize_text_field( wp_unslash( $_POST['ratio'] ) );
				}
				update_option( 'ratiothumbnailssize', $ratiothumbnailssize_settings );
				$disable_ratios = array();
				if ( isset( $_POST['disable_ratios'] ) && ! empty( $_POST['disable_ratios'] ) ) {
					$tmps = filter_var(
						wp_unslash( $_POST['disable_ratios'] ),
						FILTER_CALLBACK,
						array(
							'options' => function ( $value ) {
								return sanitize_text_field( $value );
							},
						)
					);
					foreach ( $tmps as $key => $value ) {
						$disable_ratios[] = $key;
					}
				}
				update_option( 'ratiothumbnailssize_disable', $disable_ratios );
				echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html__( 'Settings' ) . ' --> ' . esc_html__( 'Settings saved.' ) . '</li></ul></div>';
			}
		}
	}
}


