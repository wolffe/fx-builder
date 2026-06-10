<?php
namespace fx_builder\builder;
if ( ! defined( 'WPINC' ) ) {
    die;
}
new Switcher();

/**
 * Switcher: Toggle between FX Builder / wp editor.
 * Since v.1.0.1 Switcher save method is added in builder.php
 * @since 1.0.0
 */
class Switcher {

    public function __construct() {

        /* Add HTML Class */
        add_action( 'admin_head', [ $this, 'html_class_script' ] );

        /* Add Editor/FX Builder Tab */
        add_action( 'edit_form_after_title', [ $this, 'editor_toggle' ] );

        /* Scripts */
        add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ], 99 );
    }


    /**
     * Add Builder Active HTML Class
     * This is added using this method, so we can hide/show builder properly without waiting all page loaded.
     */
    public function html_class_script() {
        global $pagenow, $post_type;
        if ( ! in_array( $pagenow, [ 'post.php', 'post-new.php' ] ) ) {
            return;
        }

        if ( post_type_supports( $post_type, 'editor' ) && post_type_supports( $post_type, 'fx_builder' ) ) {

            /* If builder selected */
            if ( $active = get_post_meta( get_the_ID(), '_fxb_active', true ) ) {
                ?>
                <script type="text/javascript">
                /* <![CDATA[ */
                document.documentElement.classList.add( "fx_builder_active" );
                /* ]]> */
                </script>
                <?php
            }
        }
    }

    /**
     * Editor Toggle
     */
    public function editor_toggle( $post ) {
        $post_id = $post->ID;
        if ( post_type_supports( $post->post_type, 'editor' ) && post_type_supports( $post->post_type, 'fx_builder' ) ) {
            $active        = get_post_meta( $post_id, '_fxb_active', true );
            $active        = $active ? 1 : 0;
            $editor_class  = $active ? 'nav-tab' : 'nav-tab nav-tab-active';
            $builder_class = $active ? 'nav-tab nav-tab-active' : 'nav-tab';
            ?>
            <h1 id="fxb-switcher" class="nav-tab-wrapper wp-clearfix">
                <a data-fxb-switcher="editor" data-confirm="<?php esc_attr_e( 'Switch to the standard editor? Your FX Builder layout will be hidden but kept, and restored if you switch back.', 'fx-builder' ); ?>" class="<?php echo esc_attr( $editor_class ); ?>" href="#"><?php esc_attr_e( 'Editor', 'fx-builder' ); ?></a>
                <a data-fxb-switcher="builder" data-confirm="<?php esc_attr_e( 'Would you like to clear your editor existing content the next time you update this post and use FX Builder?', 'fx-builder' ); ?>" class="<?php echo esc_attr( $builder_class ); ?>" href="#"><?php esc_attr_e( 'FX Builder', 'fx-builder' ); ?></a>
                <input type="hidden" name="_fxb_active" value="<?php echo esc_attr( $active ); ?>">
                <?php do_action( 'fxb_switcher_nav', $post ); ?>
            </h1>
            <?php
        }
    }


    /**
     * Admin Scripts
     * @since 1.0.0
     */
    public function scripts( $hook_suffix ) {
        global $post_type;
        if ( ! in_array( $hook_suffix, [ 'post.php', 'post-new.php' ] ) ) {
            return;
        }
        if ( post_type_supports( $post_type, 'editor' ) && post_type_supports( $post_type, 'fx_builder' ) ) {

            /* CSS */
            wp_enqueue_style( 'fx-builder-switcher', URI . 'assets/switcher.css', [], VERSION );

            /* JS */
            wp_enqueue_script( 'fx-builder-switcher', URI . 'assets/switcher.js', [ 'fx-builder-core', 'fx-builder-item' ], VERSION, true );
        }
    }
}
