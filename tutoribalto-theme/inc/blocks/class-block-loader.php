<?php
/**
 * Block Loader Class
 *
 * @package Tutoribalto_Theme
 */

declare(strict_types=1);

/**
 * Class Tutoribalto_Block_Loader
 *
 * Registers and loads custom Gutenberg blocks.
 */
class Tutoribalto_Block_Loader {

	/**
	 * Blocks directory
	 *
	 * @var string
	 */
	private string $blocks_dir;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->blocks_dir = TUTORIBALTO_THEME_INC . '/blocks';

		// Register blocks.
		add_action( 'init', array( $this, 'register_blocks' ) );

		// Enqueue block editor assets.
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );

		// Add block category.
		add_filter( 'block_categories_all', array( $this, 'add_block_category' ), 10, 2 );
	}

	/**
	 * Register custom blocks
	 */
	public function register_blocks(): void {
		// Check if block editor is available.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Register Product Filter Block.
		$this->register_product_filter_block();

		// Register USP Section Block.
		$this->register_usp_section_block();

		// Register Product Grid Block.
		$this->register_product_grid_block();
	}

	/**
	 * Register Product Filter Block
	 */
	private function register_product_filter_block(): void {
		register_block_type(
			'tutoribalto/product-filter',
			array(
				'api_version'     => 2,
				'title'           => __( 'Product Filter', 'tutoribalto-theme' ),
				'description'     => __( 'Interactive product filter by body part', 'tutoribalto-theme' ),
				'category'        => 'tutoribalto',
				'icon'            => 'filter',
				'keywords'        => array( 'product', 'filter', 'category' ),
				'supports'        => array(
					'align'  => array( 'wide', 'full' ),
					'anchor' => true,
				),
				'attributes'      => array(
					'filterType' => array(
						'type'    => 'string',
						'default' => 'tabs',
					),
					'categories' => array(
						'type'    => 'array',
						'default' => array(),
					),
				),
				'render_callback' => array( $this, 'render_product_filter_block' ),
			)
		);
	}

	/**
	 * Render Product Filter Block
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render_product_filter_block( array $attributes ): string {
		$filter_type = $attributes['filterType'] ?? 'tabs';
		$categories = $attributes['categories'] ?? array();

		ob_start();
		?>
		<div class="tutoribalto-product-filter" data-filter-type="<?php echo esc_attr( $filter_type ); ?>">
			<?php if ( 'tabs' === $filter_type ) : ?>
				<ul class="products-tabs-title">
					<?php foreach ( $categories as $index => $cat_id ) : ?>
						<?php
						$category = get_term( $cat_id, 'product_cat' );
						if ( ! $category || is_wp_error( $category ) ) {
							continue;
						}
						?>
						<li data-tab="<?php echo esc_attr( $index ); ?>">
							<?php echo esc_html( $category->name ); ?>
						</li>
					<?php endforeach; ?>
				</ul>

				<div class="products-tabs-content">
					<?php foreach ( $categories as $index => $cat_id ) : ?>
						<div class="products-tab-pane" data-pane="<?php echo esc_attr( $index ); ?>">
							<?php
							// WooCommerce shortcode for category.
							echo do_shortcode( '[products category="' . esc_attr( $cat_id ) . '" limit="8"]' );
							?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<!-- Dropdown filter -->
				<select class="product-filter-dropdown">
					<?php foreach ( $categories as $cat_id ) : ?>
						<?php
						$category = get_term( $cat_id, 'product_cat' );
						if ( ! $category || is_wp_error( $category ) ) {
							continue;
						}
						?>
						<option value="<?php echo esc_attr( $cat_id ); ?>">
							<?php echo esc_html( $category->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<div class="products-dropdown-content"></div>
			<?php endif; ?>
		</div>

		<script>
		jQuery(function($) {
			// Tab switching
			$('.products-tabs-title li').on('click', function() {
				var tab = $(this).data('tab');
				$(this).addClass('active').siblings().removeClass('active');
				$('.products-tab-pane[data-pane="' + tab + '"]').addClass('active').siblings().removeClass('active');
			});
			// Activate first tab
			$('.products-tabs-title li:first').trigger('click');
		});
		</script>
		<?php
		return ob_get_clean();
	}

	/**
	 * Register USP Section Block
	 */
	private function register_usp_section_block(): void {
		register_block_type(
			'tutoribalto/usp-section',
			array(
				'api_version'     => 2,
				'title'           => __( 'USP Section', 'tutoribalto-theme' ),
				'description'     => __( 'Unique Selling Points section', 'tutoribalto-theme' ),
				'category'        => 'tutoribalto',
				'icon'            => 'star-filled',
				'keywords'        => array( 'usp', 'features', 'benefits' ),
				'supports'        => array(
					'align' => array( 'wide', 'full' ),
				),
				'attributes'      => array(
					'usps' => array(
						'type'    => 'array',
						'default' => array(
							array(
								'icon'  => 'quality',
								'title' => '100% Qualità Italiana',
								'text'  => 'Prodotti realizzati in Italia',
							),
							array(
								'icon'  => 'vet',
								'title' => 'Approccio Veterinario',
								'text'  => 'Consigliato dai veterinari',
							),
							array(
								'icon'  => 'shipping',
								'title' => 'Spedizione Gratuita',
								'text'  => 'Su tutti gli ordini',
							),
						),
					),
				),
				'render_callback' => array( $this, 'render_usp_section_block' ),
			)
		);
	}

	/**
	 * Render USP Section Block
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render_usp_section_block( array $attributes ): string {
		$usps = $attributes['usps'] ?? array();

		ob_start();
		?>
		<div class="tutoribalto-usp-section">
			<div class="usp-container">
				<?php foreach ( $usps as $usp ) : ?>
					<div class="usp-item">
						<?php if ( ! empty( $usp['icon'] ) ) : ?>
							<div class="usp-icon">
								<?php echo $this->get_usp_icon( $usp['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						<?php endif; ?>
						<h3 class="usp-title"><?php echo esc_html( $usp['title'] ?? '' ); ?></h3>
						<p class="usp-text"><?php echo esc_html( $usp['text'] ?? '' ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get USP icon SVG
	 *
	 * @param string $icon_name Icon name.
	 * @return string
	 */
	private function get_usp_icon( string $icon_name ): string {
		$icons = array(
			'quality'  => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
			'vet'      => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12h6m-3-3v6m5 5a9 9 0 1 1-8-8.94"/></svg>',
			'shipping' => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
		);

		return $icons[ $icon_name ] ?? '';
	}

	/**
	 * Register Product Grid Block
	 */
	private function register_product_grid_block(): void {
		register_block_type(
			'tutoribalto/product-grid',
			array(
				'api_version'     => 2,
				'title'           => __( 'Product Grid', 'tutoribalto-theme' ),
				'description'     => __( 'Display products in a grid layout', 'tutoribalto-theme' ),
				'category'        => 'tutoribalto',
				'icon'            => 'grid-view',
				'keywords'        => array( 'product', 'grid', 'woocommerce' ),
				'supports'        => array(
					'align' => array( 'wide', 'full' ),
				),
				'attributes'      => array(
					'title'      => array(
						'type'    => 'string',
						'default' => '',
					),
					'category'   => array(
						'type'    => 'string',
						'default' => '',
					),
					'limit'      => array(
						'type'    => 'number',
						'default' => 8,
					),
					'columns'    => array(
						'type'    => 'number',
						'default' => 4,
					),
					'orderby'    => array(
						'type'    => 'string',
						'default' => 'date',
					),
				),
				'render_callback' => array( $this, 'render_product_grid_block' ),
			)
		);
	}

	/**
	 * Render Product Grid Block
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render_product_grid_block( array $attributes ): string {
		$title = $attributes['title'] ?? '';
		$category = $attributes['category'] ?? '';
		$limit = $attributes['limit'] ?? 8;
		$columns = $attributes['columns'] ?? 4;
		$orderby = $attributes['orderby'] ?? 'date';

		ob_start();
		?>
		<div class="tutoribalto-product-grid">
			<?php if ( ! empty( $title ) ) : ?>
				<h2 class="grid-title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>

			<?php
			$shortcode_atts = array(
				'limit'   => $limit,
				'columns' => $columns,
				'orderby' => $orderby,
			);

			if ( ! empty( $category ) ) {
				$shortcode_atts['category'] = $category;
			}

			// Build shortcode.
			$shortcode = '[products';
			foreach ( $shortcode_atts as $key => $value ) {
				$shortcode .= ' ' . $key . '="' . esc_attr( $value ) . '"';
			}
			$shortcode .= ']';

			echo do_shortcode( $shortcode );
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Enqueue block editor assets
	 */
	public function enqueue_editor_assets(): void {
		wp_enqueue_style(
			'tutoribalto-blocks-editor',
			TUTORIBALTO_THEME_ASSETS . '/css/blocks-editor.css',
			array(),
			TUTORIBALTO_THEME_VERSION
		);
	}

	/**
	 * Add custom block category
	 *
	 * @param array                   $categories Block categories.
	 * @param WP_Block_Editor_Context $context Block editor context.
	 * @return array
	 */
	public function add_block_category( array $categories, $context ): array {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'tutoribalto',
					'title' => __( 'Tutoribalto', 'tutoribalto-theme' ),
					'icon'  => 'star-filled',
				),
			)
		);
	}
}
