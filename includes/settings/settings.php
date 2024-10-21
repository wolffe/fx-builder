<?php
function fxb_menu_links() {
    add_menu_page( 'FX Builder Settings', 'FX Builder', 'manage_options', 'fx_builder', 'fxb_build_admin_page', 'dashicons-admin-home', 3 );
}

add_action( 'admin_menu', 'fxb_menu_links', 10 );

function fxb_build_admin_page() {
    global $wpdb;

    $tab     = ( filter_has_var( INPUT_GET, 'tab' ) ) ? filter_input( INPUT_GET, 'tab' ) : 'dashboard';
    $section = 'admin.php?page=fx_builder&amp;tab=';

    ?>
    <div class="wrap">
        <h1>FX Builder</h1>

        <h2 class="nav-tab-wrapper nav-tab-wrapper-wppd">
            <a href="<?php echo esc_attr( $section ); ?>dashboard" class="nav-tab <?php echo $tab === 'dashboard' ? 'nav-tab-active' : ''; ?>">Dashboard</a>

            <a href="<?php echo esc_attr( $section ); ?>settings" class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>

            <a href="<?php echo esc_attr( $section ); ?>help" class="nav-tab <?php echo $tab === 'help' ? 'nav-tab-active' : ''; ?>">Help</a>
        </h2>

        <?php if ( $tab === 'dashboard' ) { ?>
            <h3 class="identityblock">FX Builder <code class="codeblock"><?php echo esc_attr( FX_BUILDER_VERSION ); ?></code></h3>
            <?php
        } elseif ( $tab === 'settings' ) {
            ?>
            <h2>Settings</h2>

            <?php
            if ( isset( $_POST['save_settings'] ) ) {
                if ( ! isset( $_POST['fxb_settings_nonce'] ) || ! check_admin_referer( 'save_fxb_settings_action', 'fxb_settings_nonce' ) ) {
                    wp_die( esc_html__( 'Nonce verification failed. Please try again.', 'fx-builder' ) );
                }

                // Get previously saved post types
                $previous_post_types = get_option( 'fx-builder_post_types', [] );

                // Sanitize the input, or set an empty array if no post types were checked
                $selected_post_types = isset( $_POST['fx-builder_post_types'] )
                ? array_map( 'sanitize_text_field', $_POST['fx-builder_post_types'] )
                : [];

                // Save the new selection
                update_option( 'fx-builder_post_types', $selected_post_types );

                // Add support for selected post types
                foreach ( $selected_post_types as $pt ) {
                    add_post_type_support( $pt, 'fx_builder' );
                }

                // Remove support for post types that were unchecked
                foreach ( $previous_post_types as $pt ) {
                    if ( ! in_array( $pt, $selected_post_types ) ) {
                        remove_post_type_support( $pt, 'fx_builder' );
                    }
                }

                update_option( 'fxb_google_fonts_api', sanitize_text_field( $_POST['fxb_google_fonts_api'] ) );

                update_option( 'fxb_google_fonts', isset( $_POST['fxb_google_fonts'] ) ? array_map( 'sanitize_text_field', $_POST['fxb_google_fonts'] ) : [] );
                update_option( 'fxb_bunny_fonts', isset( $_POST['fxb_bunny_fonts'] ) ? array_map( 'sanitize_text_field', $_POST['fxb_bunny_fonts'] ) : [] );

                delete_option( 'fxb_font_provider' );

                echo '<div class="updated notice is-dismissible"><p>' . esc_html__( 'Settings updated successfully!', 'fx-builder' ) . '</p></div>';
            }
            ?>

            <form method="post">
                <?php wp_nonce_field( 'save_fxb_settings_action', 'fxb_settings_nonce' ); ?>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label><?php echo esc_html__( 'Enable FX Builder in', 'fx-builder' ); ?></label></th>
                            <td>
                                <?php
                                $post_types = get_post_types(
                                    [
                                        'public' => true,
                                    ],
                                    'objects'
                                );

                                foreach ( $post_types as $post_type ) {
                                    // Only if post type supports "editor" (content)
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
                            <th scope="row" colspan="2">
                                <h3>Typography</h3>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row"><label>Google Fonts API</label></th>
                            <td>
                                <p>
                                    <input type="text" id="fxb_google_fonts_api" name="fxb_google_fonts_api" class="regular-text" value="<?php echo get_option( 'fxb_google_fonts_api' ); ?>">
                                    <br><small>Use your Google Fonts API key here. <a href="https://developers.google.com/fonts/docs/developer_api">Get a key</a>.</small>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label>Google Fonts</label></th>
                            <td>
                                <p>
                                    <small>Note that the following weights will be automatically loaded: 300, 400, 500 and 700.</small>
                                </p>

                                <?php if ( (string) get_option( 'fxb_google_fonts_api' ) !== '' ) { ?>
                                    <script>
                                    let currentFXBFontOption = <?php echo wp_json_encode( (array) get_option( 'fxb_google_fonts' ) ); ?>;

                                    fetch('https://www.googleapis.com/webfonts/v1/webfonts?key=<?php echo (string) get_option( 'fxb_google_fonts_api' ); ?>')
                                        .then(response => {
                                            return response.json();
                                        })
                                        .then(data => {
                                            const fontArray = Object.keys(data).map((key) => [key, data[key]]);

                                            fontArray[1][1].forEach(function (fontFamily) {
                                                var fxbFontSelector = document.getElementById('fxb-font'),
                                                    fontOption1 = document.createElement('option');

                                                fontOption1.value = fontFamily.family;
                                                fontOption1.text = fontFamily.family + ' (' + fontFamily.category + ')';

                                                fxbFontSelector.add(fontOption1);

                                                // Check if the current font family is in the selected array of fonts
                                                if (currentFXBFontOption.includes(fontFamily.family)) {
                                                    fontOption1.selected = true; // Select the option if it matches one of the saved fonts
                                                }
                                            });
                                        })
                                        .catch(err => {
                                            // error
                                        });
                                    </script>
                                <?php } else { ?>
                                    <p>You need a Google Fonts API key to view Google Fonts. <a href="<?php echo admin_url( 'admin.php?page=saturn-settings&tab=tools' ); ?>">Add one here</a>.</p>
                                <?php } ?>

                                <p>
                                    <select name="fxb_google_fonts[]" id="fxb-font" size="8" multiple>
                                        <option value="0">Select one or more fonts to be used with FX Builder...</option>
                                    </select>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label>Bunny Fonts</label></th>
                            <td>
                                <p>
                                    <small>Note that the following weights will be automatically loaded: 300, 400, 500 and 700.</small>
                                </p>

                                <script>
                                let currentFXBBunnyFontOption = <?php echo wp_json_encode( (array) get_option( 'fxb_bunny_fonts' ) ); ?>;

                                // Fetch the font data from the Bunny Fonts API
                                fetch('https://fonts.bunny.net/list')
                                    .then(response => response.json())
                                    .then(data => {
                                        // Loop through each font family in the JSON
                                        Object.entries(data).forEach(([key, fontData]) => {
                                            const fontFamily = fontData.familyName;  // Get the family name
                                            const fontCategory = fontData.category;  // Get the category
                                            const fontStyles = fontData.styles;      // Get the styles (e.g., normal, italic)
                                            const fontWeights = fontData.weights;    // Get the weights (e.g., 400, 700)

                                            // Example: Log the font family, category, styles, and weights
                                            //console.log(`Font Family: ${fontFamily}`);
                                            //console.log(`Category: ${fontCategory}`);
                                            //console.log(`Styles: ${fontStyles.join(', ')}`);
                                            //console.log(`Weights: ${fontWeights.join(', ')}`);

                                            // Example: Create an option element for each font
                                            const fxbFontSelector = document.getElementById('fxb-bunny-fonts'),
                                                fontOption = document.createElement('option');

                                            fontOption.value = fontFamily;
                                            fontOption.text = `${fontFamily} (${fontCategory})`;

                                            // Add the option to the select element
                                            fxbFontSelector.add(fontOption);

                                            // Check if the current font family is in the selected array of fonts
                                            if (currentFXBBunnyFontOption.includes(fontFamily)) {
                                                fontOption.selected = true; // Select the option if it matches one of the saved fonts
                                            }
                                        });
                                    })
                                    .catch(error => {
                                        console.error('Error fetching font data:', error);
                                    });
                                </script>

                                <p>
                                    <select name="fxb_bunny_fonts[]" id="fxb-bunny-fonts" size="8" multiple>
                                        <option value="0">Select one or more fonts to be used with FX Builder...</option>
                                    </select>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><input type="submit" name="save_settings" class="button button-primary" value="Save Changes"></th>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <?php
        }
        ?>
    </div>
    <?php
}
