<?php

/** @var array|WP_Error $compatible_plugins */
$compatible_plugins = Akismet_Compatible_Plugins::get_installed_compatible_plugins();
if ( is_array( $compatible_plugins ) ) :

	$compatible_plugin_count = count( $compatible_plugins );
	?>
	<div class="akismet-card akismet-compatible-plugins">
		<div class="akismet-section-header">
			<h2 class="akismet-section-header__label  akismet-compatible-plugins__section-header-label" aria-label="<?php esc_attr_e( 'Compatible plugins (new feature)', 'akismet' ); ?>">
				<span class="akismet-compatible-plugins__section-header-label-text"><?php esc_html_e( 'Compatible plugins', 'akismet' ); ?></span>
				<span class="akismet-new-feature"><?php esc_html_e( 'New', 'akismet' ); ?></span>
			</h2>
		</div>

		<div class="akismet-compatible-plugins__content">
			<p class="akismet-card__intro">
				<?php
				if ( 0 === $compatible_plugin_count ) {
					esc_html_e( 'Akismet works with other plugins to keep spam away.', 'akismet' );

					/* translators: %s is a URL */
					printf(
						'<p><a class="akismet-settings__external-link" href="%s">%s</a></p>',
						esc_url( 'https://akismet.com/developers/plugins-and-libraries/' ),
						esc_html__( 'See supported integrations', 'akismet' )
					);
				} else {
					printf(
						'<p>%s</p>',
						esc_html__( 'Akismet works with other plugins to keep spam away.', 'akismet' )
					);

					printf(
						'%s. %s.',
						1 === $compatible_plugin_count
							? esc_html__( 'The plugin you\'ve installed is compatible', 'akismet' )
							: esc_html__( 'The plugins you\'ve installed are compatible', 'akismet' ),
						1 === $compatible_plugin_count
							? esc_html__( 'Follow the documentation link to get started', 'akismet' )
							: esc_html__( 'Follow the documentation links to get started', 'akismet' )
					);
				}
				?>
			</p>

			<?php if ( ! empty( $compatible_plugins ) ) : ?>
				<ul class="akismet-compatible-plugins__list" id="akismet-compatible-plugins__list">
					<?php
					foreach ( $compatible_plugins as $compatible_plugin ) :
						if ( empty( $compatible_plugin['help_url'] ) ) {
							continue;
						}
						?>
						<li class="akismet-compatible-plugins__card">
							<?php if ( strlen( $compatible_plugin['logo'] ) > 0 ) : ?>
								<?php
									$logo_alt = sprintf(
										/* translators: The placeholder is the name of a plugin, like "Jetpack" . */
										__( '%s logo', 'akismet' ),
										$compatible_plugin['name']
									);
								?>
								<img
									src="<?php echo esc_url( $compatible_plugin['logo'] ); ?>"
									alt="<?php echo esc_attr( $logo_alt ); ?>"
									class="akismet-compatible-plugins__card-logo"
									width="55"
									height="55"
								/>
							<?php endif ?>
							<div class="akismet-compatible-plugins__card-detail">
								<h3 class="akismet-compatible-plugins__card-title"><?php echo esc_html( $compatible_plugin['name'] ); ?></h3>
								<div class="akismet-compatible-plugins__docs">
									<a class="akismet-settings__external-link" href="<?php echo esc_url( $compatible_plugin['help_url'] ); ?>" aria-label="
									<?php
									echo esc_html(
										sprintf(
											/* translators: The placeholder is the name of a plugin, like "Jetpack" . */
											__( 'Documentation for %s', 'akismet' ),
											$compatible_plugin['name']
										)
									);
									?>
									"><?php esc_html_e( 'View documentation', 'akismet' ); ?></a>
								</div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ( $compatible_plugin_count > Akismet_Compatible_Plugins::DEFAULT_VISIBLE_PLUGIN_COUNT ) : ?>
					<button class="akismet-compatible-plugins__show-more" 
						aria-expanded="false" 
						aria-controls="akismet-compatible-plugins__list"
						data-label-closed="
						<?php
							/* translators: %d: number of compatible plugins */
							printf( esc_html__( 'Show all %d plugins', 'akismet' ), esc_attr( $compatible_plugin_count ) );
						?>
						"  
						data-label-open="<?php esc_html_e( 'Show less', 'akismet' ); ?>">
						<?php
							/* translators: %d: number of compatible plugins */
							printf( esc_html__( 'Show all %d plugins', 'akismet' ), esc_html( $compatible_plugin_count ) );
						?>
					</button>
				<?php endif; ?>

			<?php endif; ?>
		</div>
	</div>
	<?php
	endif;
