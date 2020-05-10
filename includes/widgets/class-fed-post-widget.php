<?php
/**
 * Frontend Dashboard Widget lists
 *
 * @package frontend-dashboard.
 */

if ( ! class_exists( 'FED_Post_Widget' ) ) {
	/**
	 * Class FED_Post_Widget
	 */
	class FED_Post_Widget extends WP_Widget {
		/**
		 * FED_Post_Widget constructor.
		 */
		public function __construct() {
			parent::__construct(
				'fed-post-widget',
				__( 'FED Post Widget', 'frontend-dashboard' )
			);

			add_action( 'widgets_init', function () {
				register_widget( 'FED_Post_Widget' );
			} );

			add_action( 'wp_print_styles', array( $this, 'enqueue_style' ) );

		}

		/**
		 * Enqueue Style
		 */
		public function enqueue_style() {
			if ( is_active_widget( false, false, 'fed-post-widget' ) ) {
				if ( ! wp_script_is( 'fed_global_admin_style', 'enqueued' ) ) {
					wp_enqueue_style( 'fed_global_admin_style',
						plugins_url(
							'/assets/admin/css/fed_global_admin_style.css',
							BC_FED_PLUGIN
						),
						array(),
						BC_FED_PLUGIN_VERSION,
						'all'
					);
				}
			}
		}

		/**
		 * Widget.
		 *
		 * @param  array $args  Arguments.
		 * @param  array $instance  Instance.
		 */
		public function widget( $args, $instance ) {
			$post_type                  = fed_get_data( 'fed_post_type', $instance );
			$fed_post_widget_post_count = fed_get_data( 'fed_post_widget_post_count', $instance, 10 );
			$fed_post_widget_date       = fed_get_data( 'fed_post_widget_date', $instance, 'no' );
			$fed_post_widget_author     = fed_get_data( 'fed_post_widget_author', $instance, 'no' );
			$fed_taxonomy               = fed_get_data( 'fed_taxonomy', $instance );
			$fed_term                   = fed_get_data( 'fed_term', $instance );
			$instance['title']          = fed_get_data( 'fed_post_widget_title', $instance );

			$options = array(
				'post_type'   => $post_type,
				'numberposts' => $fed_post_widget_post_count,
			);

			if ( $fed_taxonomy && ! empty( $fed_taxonomy ) ) {
				$options['tax_query'] = array(
					array(
						'taxonomy' => $fed_taxonomy,
						'operator' => 'EXISTS',
					),
				);
				if ( $fed_term && ! empty( $fed_term ) ) {
					$options['tax_query'] = array(
						array(
							'taxonomy'         => $fed_taxonomy,
							'field'            => 'term_id',
							'terms'            => $fed_term,
							'include_children' => false,
						),
					);
				}
			}

			$pages = get_posts( $options );

			echo $args['before_widget'];

			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}
			?>
			<div class="textwidget">
				<div class="fed_post_widget_layout_container">
					<div class="fed_post_widget_layout_items">
						<?php
						if ( count( $pages ) ) {
							foreach ( $pages as $page ) {
								?>
								<div class="fed_post_widget_layout_item">
									<div class="fed_post_widget_layout_item_image">
										<img src="<?php echo esc_url( get_the_post_thumbnail_url( $page->ID ) ); ?>"/>
									</div>
									<div class="fed_post_widget_layout_item_content">
										<div class="fed_post_widget_layout_items_content_title">
											<a href="<?php echo esc_url( get_permalink( $page->ID ) ); ?>">
												<?php echo esc_attr( wp_trim_words( $page->post_title ) ); ?>
											</a>
										</div>
										<div class="fed_post_widget_layout_items_content_category">
											<?php echo wp_kses_post( get_the_category_list( ', ', '', $page->ID ) ); ?>
										</div>
										<div class="fed_post_widget_layout_items_content_author_date">
											<?php if ( 'yes' === $fed_post_widget_author ) { ?>
												<div class="fed_post_widget_layout_items_content_author">
													<?php
													echo esc_attr(
														get_the_author_meta(
															'display_name',
															$page->post_author )
													);
													?>
												</div>
											<?php } ?>
											<?php if ( 'yes' === $fed_post_widget_date ) { ?>
												<div class="fed_post_widget_layout_items_content_date">
													<?php echo esc_attr( date( 'M d, Y',
														strtotime( $page->post_date ) ) ); ?>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
								<?php
							}
						}
						?>

					</div>
				</div>
			</div>
			<?php
			// phpcs:ignore
			echo $args['after_widget'];

		}

		/**
		 * Form.
		 *
		 * @param  array $instance  Instance.
		 *
		 * @return string|void
		 */
		public function form( $instance ) {
			$taxonomy_value  = array();
			$term_value      = array();
			$post_types      = array( '' => 'Please Select' ) + fed_get_public_post_types();
			$post_type_value = '';
			$taxonomies      = array( '' => __( 'Please Select', 'frontend-dashboard' ) );
			$terms           = array( '0' => __( 'Please Select', 'frontend-dashboard' ) );
			$post_count      = ( isset( $instance['fed_post_widget_post_count'] ) && ! empty( $instance['fed_post_widget_post_count'] ) ) ? (int) $instance['fed_post_widget_post_count'] : 10;

			$title = ( isset( $instance['fed_post_widget_title'] ) && ! empty( $instance['fed_post_widget_title'] ) ) ? $instance['fed_post_widget_title'] : '';

			$is_date   = isset( $instance['fed_post_widget_date'] ) && 'yes' === $instance['fed_post_widget_date'] ? 'checked' : '';
			$is_author = isset( $instance['fed_post_widget_author'] ) && 'yes' === $instance['fed_post_widget_author'] ? 'checked' : '';

			if ( ! empty( $instance['fed_post_type'] ) ) {
				$post_type_value = $instance['fed_post_type'];
				$taxonomies      = get_object_taxonomies( fed_sanitize_text_field( $post_type_value ), 'object' );
				$taxonomies      = array(
					                   '' => __( 'Please Select', 'frontend-dashboard' ),
				                   ) + wp_list_pluck( $taxonomies, 'label', 'name' );
			}
			if ( ! empty( $instance['fed_taxonomy'] ) ) {
				$taxonomy_value = $instance['fed_taxonomy'];
				$terms          = get_terms( array(
					'taxonomy'   => $taxonomy_value,
					'hide_empty' => false,
					'orderby'    => 'name',
					'order'      => 'ASC',
					'parent'     => '0',
				) );
				$terms          = array( '0' => __( 'Please Select', 'frontend-dashboard' ) ) + wp_list_pluck( $terms,
						'name', 'term_id' );
			}

			if ( ! empty( $instance['fed_term'] ) ) {
				$term_value = $instance['fed_term'];
			}
			// phpcs:ignore
			echo fed_loader();
			?>
			<div class="bc_fed">
				<div class="fed_widget_container fed_widget_post">
					<div class="fed_widget_items m-y-20">
						<div class="fed_widget_item fed_widget_term_title m-b-10">
							<div class="fed_widget_item_label">
								<?php esc_attr_e( 'Title', 'frontend-dashboard' ); ?>
							</div>
							<div class="fed_widget_item_content">
								<input type="text"
										class="form-control"
										name="<?php echo esc_attr( $this->get_field_name( 'fed_post_widget_title' ) ); ?>"
										value="<?php echo $title; ?>"/>
							</div>
							<div class="fed_widget_item_message"></div>
						</div>
						<div class="fed_widget_item fed_widget_post_type_wrapper m-b-10">
							<div class="fed_widget_item_label">
								<?php esc_attr_e( 'Select Post Type' ); ?>
							</div>
							<div class="fed_widget_item_content">
								<?php
								echo fed_form_select( array(
									'input_value' => $post_types,
									'input_meta'  => $this->get_field_name( 'fed_post_type' ),
									'user_value'  => $post_type_value,
									'class_name'  => 'fed_widget_post_type',
									'extra'       => 'style="width: 100%" data-url=' . fed_get_ajax_form_action( 'fed_get_taxonomy_by_post_type&fed_nonce=' . wp_create_nonce( 'fed_nonce' ) ),
								) );
								?>
							</div>
							<div class="fed_widget_item_message"></div>
						</div>
						<div class="fed_widget_item fed_widget_taxonomy_list m-b-10">
							<div class="fed_widget_item_label">
								<?php esc_attr_e( 'Select Taxonomies' ); ?>
							</div>
							<div class="fed_widget_item_content">
								<?php
								// phpcs:ignore
								echo fed_form_select( array(
									'input_value' => $taxonomies,
									'input_meta'  => $this->get_field_name( 'fed_taxonomy' ),
									'user_value'  => $taxonomy_value,
									'extra'       => 'style="width: 100%" data-url=' . fed_get_ajax_form_action( 'fed_get_terms_by_taxonomy&fed_nonce=' . wp_create_nonce( 'fed_nonce' ) ),
									'class_name'  => 'fed_widget_taxonomy',
								) );
								?>
							</div>
							<div class="fed_widget_item_message"></div>
						</div>
						<div class="fed_widget_item fed_widget_term_list m-b-10">
							<div class="fed_widget_item_label">
								<?php esc_attr_e( 'Select Terms', 'frontend-dashboard' ); ?>
							</div>
							<div class="fed_widget_item_content">
								<?php
								echo fed_form_select( array(
									'input_value' => $terms,
									'input_meta'  => $this->get_field_name( 'fed_term' ),
									'user_value'  => $term_value,
									'extra'       => 'style="width: 100%"',
									'class_name'  => 'fed_widget_term',
								) );
								?>
							</div>
							<div class="fed_widget_item_message"></div>
						</div>
						<div class="fed_widget_item fed_widget_term_post_count m-b-10">
							<div class="fed_widget_item_label">
								<?php esc_attr_e( 'Post Count', 'frontend-dashboard' ); ?>
							</div>
							<div class="fed_widget_item_content">
								<input type="number"
										name="<?php echo esc_attr( $this->get_field_name( 'fed_post_widget_post_count' ) ); ?>"
										value="<?php echo (int) $post_count; ?>"/>
							</div>
							<div class="fed_widget_item_message"></div>
						</div>
						<div class="fed_widget_item fed_widget_term_date m-b-10">
							<div class="fed_widget_item_content">
								<input type="checkbox"
										name="<?php echo esc_attr( $this->get_field_name( 'fed_post_widget_date' ) ); ?>"
										value="yes" <?php echo esc_attr( $is_date ); ?>/>
								<?php esc_attr_e( 'Display Date', 'frontend-dashboard' ); ?>
							</div>
							<div class="fed_widget_item_message"></div>
						</div>
						<div class="fed_widget_item fed_widget_term_author m-b-10">
							<div class="fed_widget_item_content">
								<input type="checkbox"
										name="<?php echo esc_attr( $this->get_field_name( 'fed_post_widget_author' ) ); ?>"
										value="yes" <?php echo esc_attr( $is_author ); ?>/>
								<?php esc_attr_e( 'Display Author', 'frontend-dashboard' ); ?>
							</div>
							<div class="fed_widget_item_message"></div>
						</div>
					</div>
				</div>
			</div>
			<?php

		}

		/**
		 * Update.
		 *
		 * @param  array $new_instance  New Instance.
		 * @param  array $old_instance  Old Instance.
		 *
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                               = array();
			$instance['fed_taxonomy']               = ( ! empty( $new_instance['fed_taxonomy'] ) ) ? fed_sanitize_text_field( $new_instance['fed_taxonomy'] ) : '';
			$instance['fed_post_type']              = ( ! empty( $new_instance['fed_post_type'] ) ) ? fed_sanitize_text_field( $new_instance['fed_post_type'] ) : '';
			$instance['fed_term']                   = ( ! empty( $new_instance['fed_term'] ) ) ? fed_sanitize_text_field( $new_instance['fed_term'] ) : '';
			$instance['fed_post_widget_post_count'] = ( ! empty( $new_instance['fed_post_widget_post_count'] ) ) ? fed_sanitize_text_field( $new_instance['fed_post_widget_post_count'] ) : '';
			$instance['fed_post_widget_title']      = ( ! empty( $new_instance['fed_post_widget_title'] ) ) ? fed_sanitize_text_field( $new_instance['fed_post_widget_title'] ) : '';

			$instance['fed_post_widget_date']   = ( ! empty( $new_instance['fed_post_widget_date'] ) ) ? fed_sanitize_text_field( $new_instance['fed_post_widget_date'] ) : '';
			$instance['fed_post_widget_author'] = ( ! empty( $new_instance['fed_post_widget_author'] ) ) ? fed_sanitize_text_field( $new_instance['fed_post_widget_author'] ) : '';

			return $instance;
		}
	}

	new FED_Post_Widget();

}
