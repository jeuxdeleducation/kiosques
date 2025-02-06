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
        
        // Récupération de l'image du plan, du nombre total de kiosques et des positions enregistrées
        $plan_image = get_option( 'jde_kiosques_plan_image', '' );
        $total = intval( get_option( 'jde_kiosques_total', 20 ) );
        $positions = get_option( 'jde_kiosques_positions', array() );
        ?>
        <div id="jde-kiosques-plan" style="position: relative; width: 100%; max-width: 800px; margin: 0 auto;">
            <?php if ( ! empty( $plan_image ) ) : ?>
                <img src="<?php echo esc_url( $plan_image ); ?>" alt="<?php _e( 'Plan des kiosques', 'jde-kiosques' ); ?>" style="width: 100%; display: block;">
            <?php endif; ?>
            <?php
            $db = new JDE_Kiosques_Database();
            for ( $i = 1; $i <= $total; $i++ ) {
                $pos_x = isset( $positions[$i]['x'] ) ? floatval( $positions[$i]['x'] ) : 0;
                $pos_y = isset( $positions[$i]['y'] ) ? floatval( $positions[$i]['y'] ) : 0;
                $reservation = $db->get_reservation_by_kiosk( $i );
                $status = $reservation ? $reservation['status'] : 'disponible';
                ?>
                <div class="kiosque-hotspot" data-kiosk="<?php echo esc_attr( $i ); ?>" data-status="<?php echo esc_attr( $status ); ?>" 
                     style="position: absolute; left: <?php echo esc_attr( $pos_x ); ?>%; top: <?php echo esc_attr( $pos_y ); ?>%;">
                    <span><?php echo $i; ?></span>
                </div>
                <?php
            }
            ?>
        </div>
        <script>
        jQuery(document).ready(function($) {
            // Au clic sur un hotspot, si disponible, demander les informations pour réserver
            $('.kiosque-hotspot').on('click', function() {
                var kiosk = $(this).data('kiosk');
                var status = $(this).data('status');
                if ( status !== 'disponible' ) {
                    alert('Ce kiosque n\'est pas disponible.');
                    return;
                }
                var companyName = prompt('Entrez le nom de l\'entreprise pour réserver le kiosque ' + kiosk + ':');
                if ( companyName ) {
                    var accessCode = prompt('Entrez votre code d\'accès:');
                    if ( accessCode ) {
                        $.ajax({
                            url: jdeKiosquesAjax.ajax_url,
                            method: 'POST',
                            data: {
                                action: 'jde_kiosques_reserve',
                                nonce: jdeKiosquesAjax.nonce,
                                kiosk_number: kiosk,
                                company_name: companyName,
                                access_code: accessCode
                            },
                            success: function(response) {
                                if ( response.success ) {
                                    alert(response.data);
                                    location.reload();
                                } else {
                                    alert(response.data);
                                }
                            },
                            error: function() {
                                alert('Erreur lors de la requête.');
                            }
                        });
                    }
                }
            });
        });
        </script>
        <?php
        echo $args['after_widget'];
    }
    
    public function form( $instance ) {
        ?>
        <p><?php _e( 'Ce widget n’a pas d’options configurables.', 'jde-kiosques' ); ?></p>
        <?php
    }
    
    public function update( $new_instance, $old_instance ) {
        return $new_instance;
    }
}
