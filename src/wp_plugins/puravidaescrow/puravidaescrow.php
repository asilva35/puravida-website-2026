<?php
/*
Plugin Name: Pura Vida Escrow
Description: Pura Vida Escrow Plugin with function for website.
*/
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// add_action( 'wp_print_scripts', function() {
//     global $wp_styles;
//     foreach( $wp_styles->queue as $handle ) {
//         echo "<!--HANDLE:". $handle . '-->';
//     }
// });

add_action( 'wp_enqueue_scripts', 'puravidaescrow_dequeue_scripts', 9999 );

function puravidaescrow_dequeue_scripts() {
    // Obtenemos el ID del post actual
    $post_id = get_the_ID();
    
    // Obtenemos las metas para la validación
    $faisanbuilder_template_id = get_post_meta( $post_id, '_faisan_page_template', true );
    $is_standalone = get_post_meta( $post_id, '_faisan_is_standalone', true );

    // Condición lógica: Si hay un template asignado o es una página independiente (standalone)
    if ( ! empty( $faisanbuilder_template_id ) || $is_standalone ) {
        
        // --- 1. DG Divi Carousel ---
        // Corresponde a: swiper.min.css, light-box-styles.css y style.min.css
        wp_dequeue_style( 'swipe-style' );
        wp_dequeue_style( 'dica-lightbox-styles' );
        wp_dequeue_style( 'divi-carousel-styles' );
        wp_dequeue_style( 'divi-style' );
        wp_dequeue_style( 'divi-style-inline' );
        wp_dequeue_style( 'divi-dynamic-critical' );

        // --- 2. Divi Contact Form Helper ---
        // Corresponde a: app.min.css
        wp_dequeue_style( 'divi-contact-form-helper' );
        
        // 3. Google Fonts de Divi (El handle que encontraste)
        wp_dequeue_style( 'et-builder-googlefonts-cached' );
        wp_deregister_style( 'et-builder-googlefonts-cached' );

        remove_action( 'wp_head', 'et_builder_enqueue_inline_style', 10 );
        remove_action( 'wp_head', 'et_divi_add_customizer_css', 20 );

        add_action( 'wp_print_styles', function() {
            global $wp_styles;
            if ( isset( $wp_styles->registered['divi-style-inline'] ) ) {
                unset( $wp_styles->registered['divi-style-inline'] );
            }
            // Esto lo quita de la lista de pendientes por imprimir
            $wp_styles->queue = array_diff( $wp_styles->queue, array( 'divi-style-inline' ) );
        }, 9999 );

        
        wp_dequeue_style( 'et-divi-customizer-global-cached-inline' );
        wp_dequeue_style( 'et-core-unified' );
        
        ob_start( 'puravidaescrow_final_output_cleaner' );

        //DEQUE JQUERY AND JQUERY-migrate
        wp_dequeue_script( 'jquery' );
        wp_dequeue_script( 'jquery-migrate' );
        wp_deregister_script( 'jquery' );
        wp_deregister_script( 'jquery-migrate' );

        // --- Opcional: Eliminar el registro por completo para evitar que se carguen inline ---
        wp_deregister_style( 'swipe-style' );
        wp_deregister_style( 'dica-lightbox-styles' );
        wp_deregister_style( 'divi-carousel-styles' );
        wp_deregister_style( 'divi-contact-form-helper' );
        wp_deregister_style( 'divi-style' );
        wp_deregister_style( 'divi-style-inline' );
        wp_deregister_style( 'divi-dynamic-critical' );
    }
}

// function puravidaescrow_final_output_cleaner( $html ) {
//     // Buscamos la etiqueta link que apunte a et-divi-customizer-global.min.css y la eliminamos
//     $pattern = '/<link [^>]*href=["\'][^"\']*et-divi-customizer-global\.min\.css[^"\']*["\'][^>]*>/i';
//     return preg_replace( $pattern, '', $html );
// }

function puravidaescrow_final_output_cleaner( $html ) {
    // Elimina cualquier bloque de estilo que contenga el ID 'divi-style-inline-css'
    // El modificador 's' permite que el '.' incluya saltos de línea (multilinea)
    $html = preg_replace( '/<style id=[\'"]divi-style-inline-css[\'"]>.*?<\/style>/is', '', $html );
    
    // Elimina el CSS del Customizer por si acaso
    $html = preg_replace( '/<link [^>]*href=["\'][^"\']*et-divi-customizer-global\.min\.css[^"\']*["\'][^>]*>/i', '', $html );
    
    // Elimina bloques de estilos de Divi que no tienen ID pero usan comentarios identificadores
    $html = preg_replace( '/\/\* Divi Customizer CSS \*\/.*?<\/style>/is', '</style>', $html );

    return $html;
}

//add_action('wp_footer','puravidaescrow_add_booking_calendar');

function puravidaescrow_add_booking_calendar(){
	//if (is_page('test2')) { 
		echo '<link href="https://calendar.google.com/calendar/scheduling-button-script.css" rel="stylesheet">
	<script src="https://calendar.google.com/calendar/scheduling-button-script.js" async></script>
	<script>
	(function() {
	  var target = document.currentScript;
	  window.addEventListener(\'load\', function() {
		calendar.schedulingButton.load({
		  url: \'https://calendar.google.com/calendar/appointments/schedules/AcZssZ20_K9iyJr6rFJq-wloC0UCxp9VxU7v5vMsOlbsEI3RhavYM_GT_Dl3JJ5yrwS893yXiu9NOJYX?gv=true\',
		  color: \'#039BE5\',
		  label: \'Book an appointment\',
		  target,
		});
	  });
	})();
	</script>
	<style>.qxCTlb{position:fixed;bottom:80px; right:20px;z-index:99;}.hur54b{z-index:999999;}</style>
	';
	//}
}

add_action('wp_footer','puravidaescrow_add_whatsapp_button');

function puravidaescrow_add_whatsapp_button(){
	echo '<style>
		.whatsapp-button {
			position: fixed;
            display: flex;
            align-items: center;
            gap: 10px;
			bottom: 20px;
			right: 20px;
			z-index: 999999;
            background-color: #25d366;
            color: #fff;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
		}
	</style>';
	echo '<a href="https://wa.me/50670228103" class="whatsapp-button" target="_blank">
		<img src="https://puravidaescrowandtrust.com/wp-content/uploads/2026/04/icon-whatsapp.webp" alt="WhatsApp" width="24" height="24">
        <span class="whatsapp-button-text">Write to us</span>
	</a>';
}


/**
 * Desactivar feeds de comentarios y otros feeds RSS innecesarios
 */
function puravidaescrow_disable_feeds() {
    wp_die( __( 'No feeds available, please visit our homepage!' ) );
}

// Reemplaza las llamadas a los feeds con la función de bloqueo
add_action('do_feed',      'puravidaescrow_disable_feeds', 1);
add_action('do_feed_rdf',  'puravidaescrow_disable_feeds', 1);
add_action('do_feed_rss',  'puravidaescrow_disable_feeds', 1);
add_action('do_feed_rss2', 'puravidaescrow_disable_feeds', 1);
add_action('do_feed_atom', 'puravidaescrow_disable_feeds', 1);

// Específicamente para feeds de comentarios
add_action('do_feed_rss2_comments', 'puravidaescrow_disable_feeds', 1);
add_action('do_feed_atom_comments', 'puravidaescrow_disable_feeds', 1);

// Eliminar los enlaces a los feeds del <head> de la página
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );


//REDIRECT PAGES OF THE SITE
function puravidaescrow_redirect_pages() {
    $pages = array(
        "inicio"=>'index.html',
        "home"=>'/en/home.html',
        //"servicios"=>'/servicios.html',
        //"services"=>'/en/services.html',
        "ok"=>'ok.html',
        "magazine"=>'magazine.html',
        "panorama-inmobiliario-en-costa-rica-para-el-2025"=>'panorama-inmobiliario-en-costa-rica-para-el-2025.html',
        "costa-rican-real-estate-landscape-for-2025"=>'/en/costa-rican-real-estate-landscape-for-2025.html',
        "situacion-inmobiliaria-en-santa-teresa"=>'situacion-inmobiliaria-en-santa-teresa.html',
        "real-estate-situation-in-santa-teresa"=>'/en/real-estate-situation-in-santa-teresa.html',
        "sean-diddy-combs-es-uno-de-los-duenos-de-x-corp-a-k-a-twitter"=>'sean-diddy-combs-es-uno-de-los-duenos-de-x-corp-a-k-a-twitter.html',
        "sean-diddy-combs-is-one-of-the-owners-of-x-corp-a-k-a-twitter"=>'/en/sean-diddy-combs-is-one-of-the-owners-of-x-corp-a-k-a-twitter.html',
        "no-hagas-una-inversion-inmobiliaria-sin-antes-leer-esto"=>'no-hagas-una-inversion-inmobiliaria-sin-antes-leer-esto.html',
        "dont-make-a-real-estate-investment-without-reading-this-first"=>'/en/dont-make-a-real-estate-investment-without-reading-this-first.html',
        "invertir-en-bienes-raices-en-costa-rica-5-pasos-clave"=>'invertir-en-bienes-raices-en-costa-rica-5-pasos-clave.html',
        "experience-the-pura-vida-real-estate-investment-guide-in-costa-rica-for-foreigners"=>'/en/experience-the-pura-vida-real-estate-investment-guide-in-costa-rica-for-foreigners.html',
    );
    
    foreach($pages as $key => $value){
        if (is_page($key) || is_single($key)) { 
            include ABSPATH . 'puravidascrowandtrustdist/' . $value;
            exit;
        }
    }
    
}
add_action('template_redirect', 'puravidaescrow_redirect_pages');

?>