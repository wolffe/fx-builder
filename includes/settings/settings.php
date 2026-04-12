<?php
function fxb_menu_links() {
	add_menu_page( 'FX Builder Settings', 'FX Builder', 'manage_options', 'fx_builder', 'fxb_build_admin_page', 'dashicons-block-default', 99 );
}

add_action( 'admin_menu', 'fxb_menu_links', 10 );

function fxb_build_admin_page() {
	$tab     = ( filter_has_var( INPUT_GET, 'tab' ) ) ? filter_input( INPUT_GET, 'tab' ) : 'dashboard';
	$section = 'admin.php?page=fx_builder&amp;tab=';

	?>
	<div class="wrap">
		<h1>FX Builder</h1>

		<h2 class="nav-tab-wrapper nav-tab-wrapper-fxb">
			<a href="<?php echo esc_attr( $section ); ?>dashboard" class="nav-tab <?php echo $tab === 'dashboard' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Dashboard', 'fx-builder' ); ?></a>
			<a href="<?php echo esc_attr( $section ); ?>settings" class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Settings', 'fx-builder' ); ?></a>
			<a href="<?php echo esc_attr( $section ); ?>design" class="nav-tab <?php echo $tab === 'design' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Design', 'fx-builder' ); ?></a>
		</h2>

		<?php if ( $tab === 'dashboard' ) { ?>
			<h3 class="identityblock">
				<img src="<?php echo esc_url( FX_BUILDER_URI ); ?>includes/builder/assets/layout-images/fx-builder-logo.svg" width="48" height="48" alt="FX Builder">
				FX Builder <code class="codeblock"><?php echo esc_attr( FX_BUILDER_VERSION ); ?></code>
			</h3>
			<h2 class="titleblock">Reduce your technology overhead, improve site performance, and empower your digital teams with FX Builder.</h2>

			<div class="fxb-ad">
				<h3>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 68 68">
						<defs/>
						<rect width="100%" height="100%" fill="none"/>
						<g class="currentLayer">
							<path fill="#87e64b" d="M34.76 33C22.85 21.1 20.1 13.33 28.23 5.2 36.37-2.95 46.74.01 50.53 3.8c3.8 3.8 5.14 17.94-5.04 28.12-2.95 2.95-5.97 5.84-5.97 5.84L34.76 33"/>
							<path fill="#87e64b" d="M43.98 42.21c5.54 5.55 14.59 11.06 20.35 5.3 5.76-5.77 3.67-13.1.98-15.79-2.68-2.68-10.87-5.25-18.07 1.96-2.95 2.95-5.96 5.84-5.96 5.84l2.7 2.7m-1.76 1.75c5.55 5.54 11.06 14.59 5.3 20.35-5.77 5.76-13.1 3.67-15.79.98-2.69-2.68-5.25-10.87 1.95-18.07 2.85-2.84 5.84-5.96 5.84-5.96l2.7 2.7"/>
							<path fill="#87e64b" d="M33 34.75c-11.9-11.9-19.67-14.67-27.8-6.52-8.15 8.14-5.2 18.5-1.4 22.3 3.8 3.79 17.95 5.13 28.13-5.05 3.1-3.11 5.84-5.97 5.84-5.97L33 34.75"/>
						</g>
					</svg> Thank you for using FX Builder!</h3>
				<p>Create pixel-perfect columns with Flex CSS and native responsive styles. Use design tools and options, straight into your TinyMCE editor — customizable font size, font weight, line height, alignment, gaps and more. FX Builder approaches every project with a simplicity and native features in mind, without vendor lock-in, consistently delivering awesome designs.</p>
				<p>If you enjoy this plugin, do not forget to <a href="https://getbutterfly.com/classicpress-plugins/fx-builder/" rel="external">rate it</a>! We work hard to update it, fix bugs, add new features and make it compatible with the latest web technologies.</p>
				<p style="font-size:14px">
					🔥 Have you tried our other <a href="https://getbutterfly.com/classicpress-plugins/">ClassicPress plugins</a>?
				</p>
			</div>

			<hr>
			<p>&copy;<?php echo esc_attr( gmdate( 'Y' ) ); ?> <a href="https://getbutterfly.com/" rel="external"><strong>getButterfly</strong>.com</a> &middot; <small>Code wrangling since 2005</small></p>
		<?php } elseif ( $tab === 'design' ) {
			$small_val  = max( 320, min( 1920, absint( get_option( 'fxb_breakpoint_small', 480 ) ) ) );
			$medium_val = max( 320, min( 1920, absint( get_option( 'fxb_breakpoint_medium', 768 ) ) ) );
			?>
			<h2><?php esc_html_e( 'Design', 'fx-builder' ); ?></h2>

			<?php
			if ( isset( $_POST['save_design'] ) ) {
				if ( ! isset( $_POST['fxb_design_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fxb_design_nonce'] ) ), 'save_fxb_design_action' ) ) {
					wp_die( esc_html__( 'Nonce verification failed. Please try again.', 'fx-builder' ) );
				}
				$small  = isset( $_POST['fxb_breakpoint_small'] ) ? absint( $_POST['fxb_breakpoint_small'] ) : 480;
				$medium = isset( $_POST['fxb_breakpoint_medium'] ) ? absint( $_POST['fxb_breakpoint_medium'] ) : 768;
				$small  = max( 320, min( 1920, $small ) );
				$medium = max( 320, min( 1920, $medium ) );
				update_option( 'fxb_breakpoint_small', $small );
				update_option( 'fxb_breakpoint_medium', $medium );
				echo '<div class="updated notice is-dismissible"><p>' . esc_html__( 'Design settings updated successfully!', 'fx-builder' ) . '</p></div>';
				$small_val  = $small;
				$medium_val = $medium;
			}
			?>
			<form method="post">
				<?php wp_nonce_field( 'save_fxb_design_action', 'fxb_design_nonce' ); ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row" colspan="2">
								<h3><?php esc_html_e( 'Responsive Breakpoints', 'fx-builder' ); ?></h3>
							</th>
						</tr>
						<tr>
							<th scope="row"><label for="fxb_breakpoint_small"><?php esc_html_e( 'Small Screen', 'fx-builder' ); ?></label></th>
							<td>
								<p>
									<input type="range" id="fxb_breakpoint_small_range" min="320" max="1920" step="1" value="<?php echo esc_attr( (string) $small_val ); ?>" aria-describedby="fxb_breakpoint_small_desc">
									<input type="number" id="fxb_breakpoint_small" name="fxb_breakpoint_small" value="<?php echo esc_attr( (string) $small_val ); ?>" min="320" max="1920" step="1" class="small-text"> px
								</p>
								<p class="description" id="fxb_breakpoint_small_desc"><?php esc_html_e( 'Controls when the small screen options and visibility should take effect. In pixels. Default is 480.', 'fx-builder' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="fxb_breakpoint_medium"><?php esc_html_e( 'Medium Screen', 'fx-builder' ); ?></label></th>
							<td>
								<p>
									<input type="range" id="fxb_breakpoint_medium_range" min="320" max="1920" step="1" value="<?php echo esc_attr( (string) $medium_val ); ?>" aria-describedby="fxb_breakpoint_medium_desc">
									<input type="number" id="fxb_breakpoint_medium" name="fxb_breakpoint_medium" value="<?php echo esc_attr( (string) $medium_val ); ?>" min="320" max="1920" step="1" class="small-text"> px
								</p>
								<p class="description" id="fxb_breakpoint_medium_desc"><?php esc_html_e( 'Controls when the medium screen options and visibility should take effect. In pixels. Default is 768.', 'fx-builder' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="fxb_breakpoint_large_display"><?php esc_html_e( 'Large Screen', 'fx-builder' ); ?></label></th>
							<td>
								<input type="text" id="fxb_breakpoint_large_display" value="<?php echo esc_attr( (string) ( $medium_val + 1 ) ); ?>" class="small-text" readonly> px
								<p class="description"><?php esc_html_e( 'Any screen larger than that which is defined as the medium screen will be counted as a large screen.', 'fx-builder' ); ?></p>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<p class="description" style="margin-top:0;"><strong><?php esc_html_e( 'Frequently used widths (for reference)', 'fx-builder' ); ?></strong></p>
								<ul class="description" style="margin:0.5em 0 0 1.5em;list-style:disc;">
									<li><strong>480 px</strong> — <?php esc_html_e( 'Large phones, small phablets (default Small)', 'fx-builder' ); ?></li>
									<li><strong>768 px</strong> — <?php esc_html_e( 'Tablets portrait, large phones landscape (default Medium)', 'fx-builder' ); ?></li>
									<li><strong>1024 px</strong> — <?php esc_html_e( 'Tablets landscape, small laptops', 'fx-builder' ); ?></li>
									<li><strong>1280 px</strong> — <?php esc_html_e( 'Laptops, small desktops', 'fx-builder' ); ?></li>
									<li><strong>1920 px</strong> — <?php esc_html_e( 'Full HD desktops', 'fx-builder' ); ?></li>
								</ul>
							</td>
						</tr>
						<tr>
							<th scope="row"><input type="submit" name="save_design" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'fx-builder' ); ?>"></th>
							<td></td>
						</tr>
					</tbody>
				</table>
			</form>
			<script>
			(function(){
				var minBp = 320, maxBp = 1920;
				var smallNum   = document.getElementById('fxb_breakpoint_small');
				var smallRange = document.getElementById('fxb_breakpoint_small_range');
				var medNum     = document.getElementById('fxb_breakpoint_medium');
				var medRange   = document.getElementById('fxb_breakpoint_medium_range');
				var largeDisplay = document.getElementById('fxb_breakpoint_large_display');

				function clamp(v) { return Math.max(minBp, Math.min(maxBp, parseInt(v, 10) || 0)); }
				function getSmall() { return smallNum ? clamp(smallNum.value) : minBp; }
				function getMed()   { return medNum   ? clamp(medNum.value)   : maxBp; }

				function updateLarge() {
					if (largeDisplay && medNum) { largeDisplay.value = getMed() + 1; }
				}
				function setMedium(val) {
					val = clamp(val);
					if (medNum)   medNum.value   = val;
					if (medRange) medRange.value = val;
					updateLarge();
				}
				function setSmall(val) {
					val = clamp(val);
					if (smallNum)   smallNum.value   = val;
					if (smallRange) smallRange.value = val;
				}

				if (smallNum && smallRange) {
					smallRange.addEventListener('input', function() {
						smallNum.value = smallRange.value;
						var s = getSmall(), m = getMed();
						if (s >= m) setMedium(s + 1);
					});
					smallNum.addEventListener('change', function() {
						smallRange.value = clamp(smallNum.value);
						smallNum.value   = smallRange.value;
						var s = getSmall(), m = getMed();
						if (s >= m) setMedium(s + 1);
					});
				}

				if (medNum && medRange) {
					medRange.addEventListener('input', function() {
						medNum.value = medRange.value;
						var s = getSmall(), m = getMed();
						if (m <= s) setSmall(m - 1);
						updateLarge();
					});
					medNum.addEventListener('change', function() {
						medRange.value = clamp(medNum.value);
						medNum.value   = medRange.value;
						var s = getSmall(), m = getMed();
						if (m <= s) setSmall(m - 1);
						updateLarge();
					});
				}
			})();
			</script>
		<?php } elseif ( $tab === 'settings' ) { ?>
			<h2><?php esc_html_e( 'Settings', 'fx-builder' ); ?></h2>

			<?php
			if ( isset( $_POST['save_settings'] ) ) {
				if ( ! isset( $_POST['fxb_settings_nonce'] ) || ! check_admin_referer( 'save_fxb_settings_action', 'fxb_settings_nonce' ) ) {
					wp_die( esc_html__( 'Nonce verification failed. Please try again.', 'fx-builder' ) );
				}

				// Get previously saved post types.
				$previous_post_types = get_option( 'fx-builder_post_types', [] );

				// Sanitize the input, or set an empty array if no post types were checked.
				$selected_post_types = isset( $_POST['fx-builder_post_types'] )
					? array_map( 'sanitize_text_field', wp_unslash( $_POST['fx-builder_post_types'] ) )
					: [];

				// Save the new selection.
				update_option( 'fx-builder_post_types', $selected_post_types );

				// Add support for selected post types.
				foreach ( $selected_post_types as $pt ) {
					add_post_type_support( $pt, 'fx_builder' );
				}

				// Remove support for post types that were unchecked.
				foreach ( $previous_post_types as $pt ) {
					if ( ! in_array( $pt, $selected_post_types ) ) {
						remove_post_type_support( $pt, 'fx_builder' );
					}
				}

				update_option( 'fxb_google_fonts_api', sanitize_text_field( wp_unslash( $_POST['fxb_google_fonts_api'] ?? '' ) ) );

				update_option( 'fxb_google_fonts', isset( $_POST['fxb_google_fonts'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['fxb_google_fonts'] ) ) : [] );
				update_option( 'fxb_bunny_fonts',  isset( $_POST['fxb_bunny_fonts'] )  ? array_map( 'sanitize_text_field', wp_unslash( $_POST['fxb_bunny_fonts'] ) )  : [] );

				delete_option( 'fxb_font_provider' );

				echo '<div class="updated notice is-dismissible"><p>' . esc_html__( 'Settings updated successfully!', 'fx-builder' ) . '</p></div>';
			}
			?>

			<form method="post">
				<?php wp_nonce_field( 'save_fxb_settings_action', 'fxb_settings_nonce' ); ?>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label><?php esc_html_e( 'Enable FX Builder in', 'fx-builder' ); ?></label></th>
							<td>
								<?php
								$post_types = get_post_types( [ 'public' => true ], 'objects' );

								foreach ( $post_types as $post_type ) {
									// Only show post types that support the content editor.
									if ( post_type_supports( $post_type->name, 'editor' ) ) {
										?>
										<p>
											<label>
												<input type="checkbox" value="<?php echo esc_attr( $post_type->name ); ?>" name="fx-builder_post_types[]" <?php checked( post_type_supports( esc_attr( $post_type->name ), 'fx_builder' ) ); ?>> <?php echo esc_attr( $post_type->label ); ?>
											</label>
										</p>
										<?php
									}
								}
								?>
							</td>
						</tr>
						<tr>
							<th scope="row"><label><?php esc_html_e( 'FX Builder Theme', 'fx-builder' ); ?></label></th>
							<td>
								<p>
									<select name="fxb_theme" id="fxb-theme">
										<option value="light" selected><?php esc_html_e( 'Light', 'fx-builder' ); ?></option>
									</select>
								</p>
							</td>
						</tr>

						<!-- ============================================================
						     TYPOGRAPHY
						     ============================================================ -->
						<tr>
							<th scope="row" colspan="2">
								<h3><?php esc_html_e( 'Typography', 'fx-builder' ); ?></h3>
							</th>
						</tr>

						<!-- Google Fonts vs. Bunny Fonts explanation -->
						<tr>
							<td colspan="2">
								<div class="notice notice-info inline" style="margin:0 0 0.5em;padding:10px 14px;">
									<p>
										<strong><?php esc_html_e( 'Google Fonts vs. Bunny Fonts', 'fx-builder' ); ?></strong><br>
										<?php
										esc_html_e(
											'Both services offer the same large catalogue of web fonts and can be used simultaneously. ' .
											'Google Fonts loads assets from Google\'s servers: this requires an API key and may raise GDPR compliance concerns for EU-based sites, ' .
											'because visitor IP addresses are transmitted to Google\'s infrastructure. ' .
											'Bunny Fonts is a fully GDPR-compliant, privacy-friendly drop-in replacement: ' .
											'it serves the identical font catalogue from EU-based servers with no tracking, no data sharing, and no API key required.',
											'fx-builder'
										);
										?>
									</p>
								</div>
							</td>
						</tr>

						<!-- Google Fonts API key -->
						<tr>
							<th scope="row"><label for="fxb_google_fonts_api"><?php esc_html_e( 'Google Fonts API Key', 'fx-builder' ); ?></label></th>
							<td>
								<p>
									<input type="text" id="fxb_google_fonts_api" name="fxb_google_fonts_api" class="regular-text" value="<?php echo esc_attr( get_option( 'fxb_google_fonts_api' ) ); ?>">
									<br><small><?php esc_html_e( 'Required only for Google Fonts. Leave blank if you are using Bunny Fonts exclusively.', 'fx-builder' ); ?> <a href="https://developers.google.com/fonts/docs/developer_api" rel="external"><?php esc_html_e( 'How to get a key.', 'fx-builder' ); ?></a></small>
								</p>
							</td>
						</tr>

						<!-- Google Fonts selector -->
						<tr>
							<th scope="row"><label for="fxb-font"><?php esc_html_e( 'Google Fonts', 'fx-builder' ); ?></label></th>
							<td>
								<?php if ( (string) get_option( 'fxb_google_fonts_api' ) !== '' ) : ?>
									<!-- API key present: fetch font list and initialise Tom Select. -->
									<p class="description">
										<?php esc_html_e( 'Select up to 20 Google Fonts to make available in the editor. Weights 300, 400, 500 and 700 are loaded automatically for every selected font.', 'fx-builder' ); ?>
									</p>
									<p>
										<select name="fxb_google_fonts[]" id="fxb-font" multiple></select>
									</p>
									<p>
										<button type="button" id="fxb-clear-google-fonts" class="button">
											<?php esc_html_e( 'Clear all selected Google Fonts', 'fx-builder' ); ?>
										</button>
									</p>
									<script>
									(function () {
										var currentGoogleFonts = <?php echo wp_json_encode( (array) get_option( 'fxb_google_fonts' ) ); ?>;
										var fxbGoogleTs = null;

										fetch('https://www.googleapis.com/webfonts/v1/webfonts?key=<?php echo esc_attr( get_option( 'fxb_google_fonts_api' ) ); ?>')
											.then(function (response) { return response.json(); })
											.then(function (data) {
												var selector = document.getElementById('fxb-font');
												var fontList = Object.keys(data).map(function (k) { return [k, data[k]]; });

												fontList[1][1].forEach(function (font) {
													var option   = document.createElement('option');
													option.value = font.family;
													option.text  = font.family + ' (' + font.category + ')';
													if (currentGoogleFonts.includes(font.family)) {
														option.selected = true;
													}
													selector.add(option);
												});

												if (typeof TomSelect !== 'undefined') {
													fxbGoogleTs = new TomSelect('#fxb-font', {
														maxItems: 20,
														plugins: {
															remove_button: {},
															clear_button: {
																title: '<?php echo esc_js( __( 'Remove all selected fonts', 'fx-builder' ) ); ?>'
															}
														},
														placeholder: '<?php echo esc_js( __( 'Search Google Fonts\u2026', 'fx-builder' ) ); ?>'
													});
												}
											})
											.catch(function (err) {
												console.error('FX Builder: error fetching Google Fonts:', err);
												if (typeof TomSelect !== 'undefined') {
													fxbGoogleTs = new TomSelect('#fxb-font', {
														placeholder: '<?php echo esc_js( __( 'Error loading fonts — please check your API key.', 'fx-builder' ) ); ?>'
													});
													fxbGoogleTs.disable();
												}
											});

										document.getElementById('fxb-clear-google-fonts').addEventListener('click', function () {
											if (fxbGoogleTs) { fxbGoogleTs.clear(); }
										});
									})();
									</script>
								<?php else : ?>
									<!-- No API key: show instructional notice and a disabled selector. -->
									<p class="description">
										<?php esc_html_e( 'The Google Fonts selector is inactive. To enable it: enter your Google Fonts API key in the field above and click Save Changes. The font list will be available on the next page load.', 'fx-builder' ); ?>
										<a href="https://developers.google.com/fonts/docs/developer_api" rel="external"><?php esc_html_e( 'How to get a key.', 'fx-builder' ); ?></a>
									</p>
									<p>
										<select id="fxb-font-disabled" multiple disabled></select>
									</p>
									<script>
									document.addEventListener('DOMContentLoaded', function () {
										if (typeof TomSelect !== 'undefined') {
											var ts = new TomSelect('#fxb-font-disabled', {
												placeholder: '<?php echo esc_js( __( 'Enter an API key above and save to unlock Google Fonts\u2026', 'fx-builder' ) ); ?>'
											});
											ts.disable();
										}
									});
									</script>
								<?php endif; ?>
							</td>
						</tr>

						<!-- Bunny Fonts selector -->
						<tr>
							<th scope="row"><label for="fxb-bunny-fonts"><?php esc_html_e( 'Bunny Fonts', 'fx-builder' ); ?></label></th>
							<td>
								<p class="description">
									<?php esc_html_e( 'Select up to 20 Bunny Fonts to make available in the editor. Weights 300, 400, 500 and 700 are loaded automatically for every selected font. No API key is required — this selector is always active.', 'fx-builder' ); ?>
								</p>
								<p>
									<select name="fxb_bunny_fonts[]" id="fxb-bunny-fonts" multiple></select>
								</p>
								<p>
									<button type="button" id="fxb-clear-bunny-fonts" class="button">
										<?php esc_html_e( 'Clear all selected Bunny Fonts', 'fx-builder' ); ?>
									</button>
								</p>
								<script>
								(function () {
									var currentBunnyFonts = <?php echo wp_json_encode( (array) get_option( 'fxb_bunny_fonts' ) ); ?>;
									var fxbBunnyTs = null;

									fetch('https://fonts.bunny.net/list')
										.then(function (response) { return response.json(); })
										.then(function (data) {
											var selector = document.getElementById('fxb-bunny-fonts');

											Object.entries(data).forEach(function (entry) {
												var fontFamily   = entry[1].familyName;
												var fontCategory = entry[1].category;
												var option       = document.createElement('option');
												option.value     = fontFamily;
												option.text      = fontFamily + ' (' + fontCategory + ')';
												if (currentBunnyFonts.includes(fontFamily)) {
													option.selected = true;
												}
												selector.add(option);
											});

											if (typeof TomSelect !== 'undefined') {
												fxbBunnyTs = new TomSelect('#fxb-bunny-fonts', {
													maxItems: 20,
													plugins: {
														remove_button: {},
														clear_button: {
															title: '<?php echo esc_js( __( 'Remove all selected fonts', 'fx-builder' ) ); ?>'
														}
													},
													placeholder: '<?php echo esc_js( __( 'Search Bunny Fonts\u2026', 'fx-builder' ) ); ?>'
												});
											}
										})
										.catch(function (err) {
											console.error('FX Builder: error fetching Bunny Fonts:', err);
										});

									document.getElementById('fxb-clear-bunny-fonts').addEventListener('click', function () {
										if (fxbBunnyTs) { fxbBunnyTs.clear(); }
									});
								})();
								</script>
							</td>
						</tr>
						<!-- ============================================================ -->

						<tr>
							<th scope="row"><input type="submit" name="save_settings" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'fx-builder' ); ?>"></th>
							<td></td>
						</tr>
					</tbody>
				</table>
			</form>
		<?php } ?>
	</div>
	<?php
}