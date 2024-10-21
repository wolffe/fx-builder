<?php
namespace fx_builder\settings;

use fx_builder\Functions as Fs;

if ( ! defined( 'WPINC' ) ) {
    die;
}
Settings::get_instance();

/**
 * Settings
 * @since 1.0.0
 */
class Settings {
    /**
     * The instance of the class.
     *
     * @var Settings|null
     */
    private static $instance = null;

    /**
     * The slug for the settings.
     *
     * @var string
     */
    //private $settings_slug;

    /**
     * The suffix for the hook.
     *
     * @var string
     */
    //private $hook_suffix;

    /**
     * The options group.
     *
     * @var string
     */
    //private $options_group;

    /**
     * Returns the instance.
     *
     * @return Settings
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        /* Vars */
        //$this->settings_slug = 'fx-builder';
        //$this->hook_suffix   = '';
        //$this->options_group = 'fx-base';

        /* Create Settings Page */
        //add_action( 'admin_menu', [ $this, 'create_settings_page' ] );

        /* Register Settings and Fields */
        //add_action( 'admin_init', [ $this, 'register_settings' ], 1 );

        /* Add Post Type Support */
        add_action( 'init', [ $this, 'add_builder_support' ] );
    }

    /**
     * Create Settings Page
     * @since 1.0.0
     */
    /*
    public function create_settings_page() {
        if ( false === apply_filters( 'fx_builder_settings', true ) ) {
            return false;
        }

        $this->hook_suffix = add_options_page(
            __( 'FX Builder Settings', 'fx-builder' ),
            __( 'FX Builder', 'fx-builder' ),
            'manage_options',
            $this->settings_slug,
            [ $this, 'settings_page' ],
        );
    }
    /**/

    /**
     * Settings Page Output
     * @since 1.0.0
     */
    /*
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_attr_e( 'FX Builder Settings', 'fx-builder' ); ?></h1>
            <form method="post" action="options.php">
                <?php do_settings_sections( $this->settings_slug ); ?>
                <?php settings_fields( $this->options_group ); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    /**/

    /**
     * Register Settings
     * @since 0.1.0
     */
    /*
    public function register_settings() {
        if ( false === apply_filters( 'fx_builder_settings', true ) ) {
            return false;
        }

        register_setting(
            $this->options_group,
            'fx-builder_post_types',
            function ( $data ) {
                return $this->check_post_types_exists( $data );
            }
        );

        add_settings_section(
            'fxb_settings',
            '',
            '__return_false',
            $this->settings_slug
        );

        add_settings_field(
            'fxb_post_types_field',
            __( 'Enable FX Builder in', 'fx-builder' ),
            function () {
                $post_types = get_post_types( [ 'public' => true ], 'objects' );

                foreach ( $post_types as $post_type ) {
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
            },
            $this->settings_slug,
            'fxb_settings'
        );
    }
    /**/

    /**
     * Enable FX Builder to Post Type
     */
    public function add_builder_support() {
        /* Hook to disable settings */
        if ( false === apply_filters( 'fx_builder_settings', true ) ) {
            return false;
        }

        /* If not set, default to page. */
        $post_types = get_option( 'fx-builder_post_types' );
        if ( ! $post_types && ! is_array( $post_types ) ) {
            $post_types = [ 'page' ];
        } else {
            $post_types = $this->check_post_types_exists( $post_types );
        }
        foreach ( $post_types as $pt ) {
            add_post_type_support( $pt, 'fx_builder' );
        }
    }

    /**
     * Sanitize Post Types
     * @param $input array
     * @return array
     * @since 1.0.0
     */
    public function check_post_types_exists( $input ) {
        $input      = is_array( $input ) ? $input : [];
        $post_types = [];

        foreach ( $input as $post_type ) {
            if ( post_type_exists( $post_type ) ) {
                $post_types[] = $post_type;
            }
        }

        return $post_types;
    }
}
