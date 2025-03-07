<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'jde_kiosques_widget',
            __( 'JDE Kiosques', 'jde-kiosques' ),
            array( 'description' => __( 'Widget interactif pour la gestion et la réservation des kiosques.', 'jde-kiosques' ) )
        );
    }
    
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        
        $title = ! empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : __( 'Nos kiosques', 'jde-kiosques' );
        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }
        
        echo do_shortcode( '[jde_kiosques]' );
        
        echo $args['after_widget'];
    }
    
    public function form( $instance ) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                <?php esc_html_e( 'Titre:', 'jde-kiosques' ); ?>
            </label>
            <input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }
    
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
        return $instance;
    }
}

function register_jde_kiosques_widget() {
    register_widget( 'JDE_Kiosques_Widget' );
}
add_action( 'widgets_init', 'register_jde_kiosques_widget' );
