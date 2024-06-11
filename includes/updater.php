<?php
// Take over the update check
add_filter( 'pre_set_site_transient_update_plugins', 'fxb_check_for_plugin_update' );

function fxb_check_for_plugin_update( $checked_data ) {
    $api_url     = 'https://getbutterfly.com/web/wp/update/';
    $plugin_slug = 'fx-builder';

    if ( empty( $checked_data->checked ) ) {
        return $checked_data;
    }

    $request_args = [
        'slug'    => $plugin_slug,
        'version' => $checked_data->checked[ $plugin_slug . '/' . $plugin_slug . '.php' ],
    ];

    $request_string = fxb_prepare_request( 'basic_check', $request_args );

    // Start checking for an update
    $raw_response = wp_remote_post( $api_url, $request_string );

    if ( ! is_wp_error( $raw_response ) && ( (int) $raw_response['response']['code'] === 200 ) ) {
        $response = unserialize( $raw_response['body'] );
    }

    if ( is_object( $response ) && ! empty( $response ) ) { // Feed the update data into WP updater
        $checked_data->response[ $plugin_slug . '/' . $plugin_slug . '.php' ] = $response;
    }

    return $checked_data;
}

// Take over the Plugin info screen
add_filter( 'plugins_api', 'fxb_plugin_api_call', 10, 3 );

function fxb_plugin_api_call( $def, $action, $args ) {
    $api_url     = 'https://getbutterfly.com/web/wp/update/';
    $plugin_slug = 'fx-builder';

    // Do nothing if this is not about getting plugin information
    if ( $action !== 'plugin_information' ) {
        return false;
    }

    if ( (string) $args->slug !== (string) $plugin_slug ) {
        // Conserve the value of previous filter of plugins list in alternate API
        return $def;
    }

    // Get the current version
    $plugin_info     = get_site_transient( 'update_plugins' );
    $current_version = $plugin_info->checked[ $plugin_slug . '/' . $plugin_slug . '.php' ];
    $args->version   = $current_version;

    $request_string = fxb_prepare_request( $action, $args );

    $request = wp_remote_post( $api_url, $request_string );

    if ( is_wp_error( $request ) ) {
        $res = new WP_Error( 'plugins_api_failed', __( 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>' ), $request->get_error_message() );
    } else {
        $res = unserialize( $request['body'] );

        if ( $res === false ) {
            $res = new WP_Error( 'plugins_api_failed', __( 'An unknown error occurred' ), $request['body'] );
        }
    }

    return $res;
}

function fxb_prepare_request( $action, $args ) {
    global $wp_version;

    return [
        'body'       => [
            'action'  => $action,
            'request' => serialize( $args ),
            'api-key' => md5( get_bloginfo( 'url' ) ),
        ],
        'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
    ];
}
