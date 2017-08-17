<?php
function fed_plugin_pages() {
	$plugins = array(
		'plugins' => array(
			'fed_extra'   => array(
				'id'          => 'BC_FED_EXTRA_PLUGIN',
				'title'       => 'Frontend Dashboard Extra',
				'description' => 'Frontend Dashboard extra fields added',
				'thumbnail'   => 'Frontend Dashboard extra fields added',
				'url'         => 'http://buffercode.com',
				'pricing'     => array(
					'type'          => 'Free',
					'amount'        => '0',
					'currency'      => '$',
					'currency_code' => 'USD',
					'purchase_url'  => ''
				)
			),
			'fed_captcha' => array(
				'id'          => 'BC_FED_CAPTCHA_PLUGIN',
				'title'       => 'Frontend Dashboard Extra',
				'description' => 'Frontend Dashboard extra fields added',
				'thumbnail'   => 'Frontend Dashboard extra fields added',
				'url'         => 'http://buffercode.com',
				'pricing'     => array(
					'type'          => 'Pro',
					'amount'        => '10',
					'currency'      => '$',
					'currency_code' => 'USD',
					'purchase_url'  => 'https://buffercode.com'
				),
			)
		),
		'date'    => date( 'Y-m-d H:i:s' )
	);
	?>
	<div class="bc_fed container fed_plugins">
		<h3 class="fed_header_font_color">Plugin List</h3>
		<div class="row">
			<?php foreach ( $plugins['plugins'] as $single ) { ?>
				<div class="col-sm-6 col-md-3 col-xs-12">
					<div class="thumbnail">
						<img src="<?php echo $single['thumbnail']; ?>" alt="">
						<div class="caption">
							<h3 class="text-center"><?php echo $single['title']; ?></h3>
							<p><?php echo $single['description']; ?></p>
							<div class="text-center">
								<a href="<?php $single['url'] ?>">
									<button class="btn btn-warning">
										<i class="fa fa-eye" aria-hidden="true"></i>
										View
									</button>
								</a>
								<?php
								if ( defined( $single['id'] ) ) {
									?>
									<button class="btn btn-info">
										<i class="fa fa-check" aria-hidden="true"></i>
										Installed
									</button>
									<?php
								} else {
									if ( $single['pricing']['type'] === 'Free' ) { ?>
										<a href="#" class="btn btn-primary" role="button">
											<i class="fa fa-download" aria-hidden="true"></i>
											<?php _e( 'Download', 'fed' ) ?>
										</a>
									<?php }
									if ( $single['pricing']['type'] === 'Pro' ) {
										?>
										<a href="#" class="btn btn-primary" role="button">
											<i class="fa fa-shopping-cart" aria-hidden="true"></i>
											<?php echo $single['pricing']['currency'] . $single['pricing']['amount']; ?>
										</a>
										<?php
									}
								}
								?>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php
}