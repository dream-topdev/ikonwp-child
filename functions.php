<?php
add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );
function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}
// Google Seo Section
function gretathemes_meta_description() {
    return;
    global $post;
    if ( is_singular() ) {
        $des_post = strip_tags( $post->post_content );
        $des_post = strip_shortcodes( $post->post_content );
        $des_post = str_replace( array("\n", "\r", "\t"), ' ', $des_post );
        $des_post = mb_substr( $des_post, 0, 300, 'utf8' );
        echo '<meta name="description" content="' . $des_post . '" />' . "\n";
    }
    if ( is_home() ) {
        echo '<meta name="description" content="' . get_bloginfo( "description" ) . '" />' . "\n";
    }
    if ( is_category() ) {
        $des_cat = strip_tags(category_description());
        echo '<meta name="description" content="' . $des_cat . '" />' . "\n";
    }
    echo '<meta name="marse" content="to the sky!" />' . "\n";
}
add_action( 'wp_head', 'gretathemes_meta_description');


// Function to add color title text to posts and pages
function color_title_shortcode() {
    $colorName = 'Red';
    $colorHex = '#ff0000';
    return $colorName . ' / ' . $colorHex  . ' hex color';
}
add_shortcode('color-title', 'color_title_shortcode');

// Function to add color hex to posts and pages
function color_hex_shortcode() {
    $colorHex = '#ff0000';
    return $colorHex;
}
add_shortcode('color-hex', 'color_hex_shortcode');

// Function to add other properties of color to posts and pages
function color_value_shortcode($atts) {
    extract( shortcode_atts( array(
        'type' => 'HEX',
        'prop' => '',
    ), $atts, 'multilink' ) );
    switch ($type) {
        case 'HEX': // RGB hex
            return 'ff0000';
        break;
        case 'RGBD': // RGB decimal
            switch($prop) {
                case 'R':
                    return 255;
                case 'G':
                    return 0;
                case 'B':
                    return 0;
                default:
                    return 0;
            }
        break;
        case 'RGBP': // RGB percent
            switch($prop) {
                case 'R':
                    return 100;
                case 'G':
                    return 0;
                case 'B':
                    return 0;
                default:
                    return 0;
            }
        break;
        case 'CMYKP': // CMYK percent
            switch($prop) {
                case 'C':
                    return 0;
                case 'M':
                    return 100;
                case 'Y':
                    return 100;
                case 'K':
                    return 0;
                default:
                    return 0;
            }
        break;
        case 'CMYKD': // CMYK decimal
            switch($prop) {
                case 'C':
                    return 0;
                case 'M':
                    return 1;
                case 'Y':
                    return 1;
                case 'K':
                    return 0;
                default:
                    return 0;
            }
        break;
        case 'HSL': // HSL
            switch($prop) {
                case 'H':
                    return 0;
                case 'S':
                    return 100;
                case 'L':
                    return 50;
                default:
                    return 0;
            }
        break;
        case 'HSV': // HSV/HSB            
            switch($prop) {
                case 'H':
                    return 0;
                case 'S':
                    return 100;
                case 'V':
                    return 100;
                default:
                    return 0;
            }
        break;
        case 'WEB': // Web safe
            return 'ff0000';
        break;
        case 'CIE': // CIE LAB                     
            switch($prop) {
                case 'C':
                    return 53.239;
                case 'I':
                    return 80.09;
                case 'E':
                    return 67.201;
                default:
                    return 0;
            }
        break;
        case 'XYZ': // XYZ                     
            switch($prop) {
                case 'X':
                    return 41.242;
                case 'Y':
                    return 21.266;
                case 'Z':
                    return 1.933;
                default:
                    return 0;
            }
        break;
        case 'xyY': // xyY                     
            switch($prop) {
                case 'x':
                    return 0.64;
                case 'y':
                    return 0.33;
                case 'Y':
                    return 21.266;
                default:
                    return 0;
            }
        break;
        case 'LCH': // CIE-LCH                     
            switch($prop) {
                case 'L':
                    return 53.239;
                case 'C':
                    return 104.549;
                case 'H':
                    return 39.999;
                default:
                    return 0;
            }
        break;
        case 'LUV': // CIE-LUV                     
            switch($prop) {
                case 'L':
                    return 53.239;
                case 'U':
                    return 175.009;
                case 'V':
                    return 37.755;
                default:
                    return 0;
            }
        break;
        case 'HUNTER': //HUNTER-LAB                  
            switch($prop) {
                case 'L':
                    return 46.115;
                case 'A':
                    return 79.94;
                case 'B':
                    return 29.795;
                default:
                    return 0;
            }
        break;
        case 'BIN': //Binary                              
            switch($prop) {
                case 'R':
                    return '11111111';
                case 'G':
                    return '00000000';
                case 'B':
                    return '00000000';
                default:
                    return 0;
            }
        break;
        default:
            break;
    }
    return $r;
}
add_shortcode('color-value', 'color_value_shortcode');


// Function to add color schemes to posts and pages
function color_schemes_shortcode(){
    set_query_var( 'title', "Complemetary Color" );
    set_query_var( 'colors', array("#ff0000", "#00ffff"));
    get_template_part( 'partials/color', 'scheme' );

    set_query_var( 'title', "Analogous Color" );
    set_query_var( 'colors', array("#ff0080", "#ff0000", "#ff8000"));
    get_template_part( 'partials/color', 'scheme' );
    
    set_query_var( 'title', "Split Complementary Color" );
    set_query_var( 'colors', array("#0080ff", "#ff0000", "#00ff80"));
    get_template_part( 'partials/color', 'scheme' );

    set_query_var( 'title', "Triadic Color" );
    set_query_var( 'colors', array("#0000ff", "#ff0000", "#00ff00"));
    get_template_part( 'partials/color', 'scheme' );    

    set_query_var( 'title', "Tetradic Color" );
    set_query_var( 'colors', array("#ff00ff", "#ff0000", "#00ff00", "#00ffff"));
    get_template_part( 'partials/color', 'scheme' );

    set_query_var( 'title', "Monochromatic Color" );
    set_query_var( 'colors', array("#b30000", "#cc0000", "#e60000", "#ff0000", "#ff1a1a", "#ff3333", "#ff4d4d" ));
    get_template_part( 'partials/color', 'scheme' );
}
add_shortcode('color-scheme', 'color_schemes_shortcode');
?>