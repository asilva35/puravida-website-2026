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
        //"inicio"=>'index.html',
        //"home"=>'/en/home.html',
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


// ── Helper: render a single page of blog cards ───────────────────────────────
function puravidaescrow_render_blog_cards( $paged, $posts_per_page, $read_more_label, $cat_id = 0 ) {
    $query_args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    // Filter by category when an ID is provided
    if ( $cat_id > 0 ) {
        $query_args['cat'] = $cat_id;
    }

    $query = new WP_Query( $query_args );

    $total_pages = $query->max_num_pages;
    $output      = '';

    $arrow = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>';

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id   = get_the_ID();
            $permalink = get_permalink();
            $date      = get_the_date( 'd M Y' );
            $title     = get_the_title();
            $excerpt   = get_the_excerpt()
                ? wp_trim_words( get_the_excerpt(), 28, '…' )
                : wp_trim_words( strip_shortcodes( wp_strip_all_tags( get_the_content() ) ), 28, '…' );

            if ( has_post_thumbnail() ) {
                $img_url        = get_the_post_thumbnail_url( $post_id, 'medium_large' );
                $thumbnail_html = '<img class="blog-post-card__thumbnail" src="' . esc_url( $img_url ) . '" alt="' . esc_attr( $title ) . '" loading="lazy">';
            } else {
                $thumbnail_html = '<div class="blog-post-card__thumbnail-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 18.75h16.5M3.75 4.5h16.5a.75.75 0 0 1 .75.75v13.5a.75.75 0 0 1-.75.75H3.75a.75.75 0 0 1-.75-.75V5.25a.75.75 0 0 1 .75-.75Z" />
                    </svg>
                </div>';
            }

            $output .= '
            <div class="blog-post-card">
                <a href="' . esc_url( $permalink ) . '" tabindex="-1" aria-hidden="true">' . $thumbnail_html . '</a>
                <div class="blog-post-card__body">
                    <span class="blog-post-card__date">' . esc_html( $date ) . '</span>
                    <h2 class="blog-post-card__title">
                        <a href="' . esc_url( $permalink ) . '" style="color:inherit;text-decoration:none;">' . esc_html( $title ) . '</a>
                    </h2>
                    <p class="blog-post-card__excerpt">' . esc_html( $excerpt ) . '</p>
                    <div class="blog-post-card__footer">
                        <a href="' . esc_url( $permalink ) . '" class="blog-post-card__btn">
                            ' . esc_html( $read_more_label ) . ' ' . $arrow . '
                        </a>
                    </div>
                </div>
            </div>';
        }
        wp_reset_postdata();
    }

    return array( 'html' => $output, 'total_pages' => (int) $total_pages );
}

// ── AJAX handler (logged-in & guests) ────────────────────────────────────────
function puravidaescrow_ajax_blog_page() {
    check_ajax_referer( 'puravida_blog_nonce', 'nonce' );

    $paged          = isset( $_POST['paged'] )           ? max( 1, (int) $_POST['paged'] )           : 1;
    $posts_per_page = isset( $_POST['posts_per_page'] )  ? max( 1, (int) $_POST['posts_per_page'] )  : 6;
    $read_more      = isset( $_POST['read_more_label'] ) ? sanitize_text_field( $_POST['read_more_label'] ) : 'Leer más';
    $cat_id         = isset( $_POST['category_id'] )     ? max( 0, (int) $_POST['category_id'] )     : 0;

    $result = puravidaescrow_render_blog_cards( $paged, $posts_per_page, $read_more, $cat_id );

    wp_send_json_success( $result );
}
add_action( 'wp_ajax_puravida_blog_page',        'puravidaescrow_ajax_blog_page' );
add_action( 'wp_ajax_nopriv_puravida_blog_page', 'puravidaescrow_ajax_blog_page' );

//ADD SHORTCODE TO GET BLOGPOSTS
function puravidaescrow_get_blog_posts($atts) {
    $atts = shortcode_atts(
        array(
            'posts_per_page'  => 6,
            'read_more_label' => 'Leer más',
            'category_id'     => 0,     // 0 = no filter; pass a WP category ID to filter
        ),
        $atts,
        'puravida_blog_posts'
    );

    $posts_per_page  = (int) $atts['posts_per_page'];
    $read_more_label = $atts['read_more_label'];
    $cat_id          = max( 0, (int) $atts['category_id'] );

    // Render page 1 on initial load
    $first_page  = puravidaescrow_render_blog_cards( 1, $posts_per_page, $read_more_label, $cat_id );
    $total_pages = $first_page['total_pages'];
    $cards_html  = $first_page['html'];

    // ── Inline styles (output once) ──────────────────────────────────────────
    $output = '<style>
        .blog-post-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        }
        .blog-post-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -10px rgba(56, 178, 172, 0.15);
            background: #ffffff;
        }
        .blog-post-card__thumbnail {
            width: 100%;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            display: block;
        }
        .blog-post-card__thumbnail-placeholder {
            width: 100%;
            aspect-ratio: 16 / 9;
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .blog-post-card__thumbnail-placeholder svg {
            width: 3rem;
            height: 3rem;
            color: #94a3b8;
            opacity: 0.6;
        }
        .blog-post-card__body {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            padding: 1.75rem 2rem 2rem;
        }
        .blog-post-card__date {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #38b2ac;
            margin-bottom: 0.75rem;
        }
        .blog-post-card__title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.4;
            margin: 0 0 1rem;
        }
        .blog-post-card__excerpt {
            font-size: 0.9375rem;
            color: #64748b;
            line-height: 1.65;
            flex-grow: 1;
            margin-bottom: 1.5rem;
        }
        .blog-post-card__footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 1.25rem;
        }
        .blog-post-card__btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: #38b2ac;
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: gap 0.2s ease, color 0.2s ease;
        }
        .blog-post-card__btn:hover {
            color: #2c9490;
            gap: 0.75rem;
        }
        .blog-post-card__btn svg {
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }
        .blog-post-card__btn:hover svg {
            transform: translateX(3px);
        }
    </style>';

    // ── Card markup ──────────────────────────────────────────────────────────
    // (Cards are already rendered by puravidaescrow_render_blog_cards above)

    // ── Pagination styles ─────────────────────────────────────────────────────
    $output .= '<style>
        .blog-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 3rem;
            flex-wrap: wrap;
        }
        .blog-pagination__btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.75rem;
            height: 2.75rem;
            padding: 0 0.875rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        .blog-pagination__btn:hover:not(:disabled) {
            background: #ffffff;
            border-color: #38b2ac;
            color: #38b2ac;
            box-shadow: 0 4px 12px -2px rgba(56, 178, 172, 0.2);
        }
        .blog-pagination__btn.is-active {
            background: #38b2ac;
            border-color: #38b2ac;
            color: #ffffff;
            box-shadow: 0 4px 12px -2px rgba(56, 178, 172, 0.35);
        }
        .blog-pagination__btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
        }
        .blog-pagination__btn svg {
            width: 1rem;
            height: 1rem;
        }
        #blog-posts-grid {
            transition: opacity 0.25s ease;
        }
        #blog-posts-grid.is-loading {
            opacity: 0.4;
            pointer-events: none;
        }
    </style>';

    // Nonce for AJAX
    $nonce = wp_create_nonce( 'puravida_blog_nonce' );

    // ── Wrapper: grid + pagination ─────────────────────────────────────────────
    $output .= '<div id="blog-posts-wrapper"
        data-posts-per-page="' . esc_attr( $posts_per_page ) . '"
        data-read-more-label="' . esc_attr( $read_more_label ) . '"
        data-category-id="' . esc_attr( $cat_id ) . '"
        data-nonce="' . esc_attr( $nonce ) . '"
        data-total-pages="' . esc_attr( $total_pages ) . '"
        data-ajax-url="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '">';

    // Grid (page 1 pre-rendered)
    $output .= '<div id="blog-posts-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">';
    $output .= $cards_html;
    $output .= '</div>';

    // Pagination controls (only if more than 1 page)
    if ( $total_pages > 1 ) {
        $chevron_left  = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>';
        $chevron_right = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>';

        $output .= '<nav class="blog-pagination" id="blog-pagination" aria-label="Paginación del blog">';
        $output .= '<button class="blog-pagination__btn" id="blog-prev-btn" aria-label="Página anterior" disabled>' . $chevron_left . '</button>';

        for ( $i = 1; $i <= $total_pages; $i++ ) {
            $active = $i === 1 ? ' is-active' : '';
            $output .= '<button class="blog-pagination__btn' . $active . '" data-page="' . $i . '" aria-label="Página ' . $i . '">' . $i . '</button>';
        }

        $output .= '<button class="blog-pagination__btn" id="blog-next-btn" aria-label="Página siguiente"' . ( $total_pages <= 1 ? ' disabled' : '' ) . '>' . $chevron_right . '</button>';
        $output .= '</nav>';
    }

    $output .= '</div>'; // #blog-posts-wrapper

    // ── AJAX pagination script ───────────────────────────────────────────────
    $output .= '<script>
    (function () {
        var wrapper     = document.getElementById("blog-posts-wrapper");
        if (!wrapper) return;

        var grid        = document.getElementById("blog-posts-grid");
        var pagination  = document.getElementById("blog-pagination");
        var prevBtn     = document.getElementById("blog-prev-btn");
        var nextBtn     = document.getElementById("blog-next-btn");
        var ajaxUrl     = wrapper.dataset.ajaxUrl;
        var nonce       = wrapper.dataset.nonce;
        var perPage     = wrapper.dataset.postsPerPage;
        var readMore    = wrapper.dataset.readMoreLabel;
        var categoryId  = wrapper.dataset.categoryId || \'0\';
        var totalPages  = parseInt(wrapper.dataset.totalPages, 10);
        var currentPage = 1;

        function goToPage(page) {
            if (page < 1 || page > totalPages || page === currentPage) return;

            grid.classList.add("is-loading");

            var body = new URLSearchParams({
                action:          "puravida_blog_page",
                nonce:           nonce,
                paged:           page,
                posts_per_page:  perPage,
                read_more_label: readMore,
                category_id:     categoryId,
            });

            fetch(ajaxUrl, {
                method:  "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body:    body.toString(),
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    grid.innerHTML = data.data.html;
                    currentPage = page;
                    updateControls();
                    // Smooth scroll back to grid top
                    wrapper.scrollIntoView({ behavior: "smooth", block: "start" });
                }
            })
            .finally(function () {
                grid.classList.remove("is-loading");
            });
        }

        function updateControls() {
            if (!pagination) return;

            // Page number buttons
            pagination.querySelectorAll(".blog-pagination__btn[data-page]").forEach(function (btn) {
                var p = parseInt(btn.dataset.page, 10);
                if (p === currentPage) {
                    btn.classList.add("is-active");
                } else {
                    btn.classList.remove("is-active");
                }
            });

            // Prev / Next
            if (prevBtn) prevBtn.disabled = currentPage <= 1;
            if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
        }

        // Page number buttons
        if (pagination) {
            pagination.querySelectorAll(".blog-pagination__btn[data-page]").forEach(function (btn) {
                btn.addEventListener("click", function () {
                    goToPage(parseInt(this.dataset.page, 10));
                });
            });
        }

        if (prevBtn) prevBtn.addEventListener("click", function () { goToPage(currentPage - 1); });
        if (nextBtn) nextBtn.addEventListener("click", function () { goToPage(currentPage + 1); });
    })();
    </script>';

    return $output;
}
add_shortcode('puravida_blog_posts', 'puravidaescrow_get_blog_posts');

?>