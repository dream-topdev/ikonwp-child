<?php
include_once('lib/autoload.php');
require_once('lib/colorlib/index.php');
use Phim\Color\Scheme\AnalogousScheme;
use Phim\Color\Scheme\SplitComplementaryScheme;
use Phim\Color\Scheme\TriadicScheme;
use Phim\Color\Scheme\TetradicScheme;
use Phim\Color;
add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );
function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

function add_rewrite_rules($aRules) {
    //echo "<pre>";
    //print_r($aRules);
    //echo "</pre>"; exit(1);
    $aNewRules = array('test/([^/]+)/?$' => 'index.php?pagename=test&color_hex=$matches[1]');
    $aRules = $aNewRules + $aRules;
    return $aRules;
}
// hook add_rewrite_rules function into rewrite_rules_array
add_filter('rewrite_rules_array', 'add_rewrite_rules');

function add_query_vars($aVars) {
    $aVars[] = "color_hex"; // represents the name of the product category as shown in the URL
    return $aVars;
}
     
// hook add_query_vars function into query_vars
add_filter('query_vars', 'add_query_vars');

// Google Seo Section
function gretathemes_meta_description() {
/*    global $post;
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
    }*/
    echo '<meta name="marse" content="to the sky!" />' . "\n";
    echo '<meta property="og:type" content="article">' . "\n";
    echo '<meta property="og:description" content="Red / #ff0000 hex color code information, schemes, description and conversion in RGB, HSL, HSV, CMYK, etc.">' . "\n";
    echo '<meta property="og:url" content="https://www.colorhexa.com/ff0000">' . "\n";
    echo '<meta property="og:site_name" content="ColorHexa">' . "\n";
    echo '<meta property="og:image" content="https://www.colorhexa.com/ff0000.png">' . "\n";
    echo '<meta property="og:image:width" content="150">' . "\n";
    echo '<meta property="og:image:height" content="200">' . "\n";
}
add_action( 'wp_head', 'gretathemes_meta_description');


// Function to add color title text to posts and pages
function color_title_shortcode() {
    $colorName = 'Color name';
    $colorHex = getHexFromArg();
    return $colorName . ' / #' . $colorHex  . ' hex color';
}
add_shortcode('color-title', 'color_title_shortcode');

// Function to add color hex to posts and pages
function color_hex_shortcode() {
    $colorHex = getHexFromArg();
    return '#'.$colorHex;
}
add_shortcode('color-hex', 'color_hex_shortcode');


function getHexFromArg() {
    $colorHex = 'ff0000';
    if (get_query_var("color_hex"))
        $colorHex = get_query_var("color_hex");
    $colorHex = str_pad($colorHex, 8);
    $colorHex = substr($colorHex, 0, 6);
    return $colorHex;
}
// Function to add other properties of color to posts and pages
function color_value_shortcode($atts) {
    $colorHex = getHexFromArg();
    $cInfo = array(
        'r' => hexdec(substr($colorHex, 0, 2)),
        'g' => hexdec(substr($colorHex, 2, 2)),
        'b' => hexdec(substr($colorHex, 4, 2)),
    );
    $cnInfo = array(
        'r' => $cInfo['r'] / 255,
        'g' => $cInfo['g'] / 255,
        'b' => $cInfo['b'] / 255,
    );
    $cCMYK = RGBtoCMYK($cInfo['r'], $cInfo['g'], $cInfo['b']);
    $cHSL = RGBtoHSL($cInfo['r'], $cInfo['g'], $cInfo['b']);
    $cHSV = RGBtoHSV($cInfo['r'], $cInfo['g'], $cInfo['b']);
    $cXYZ = RGBtoXYZ($cInfo['r'], $cInfo['g'], $cInfo['b']);
    $cLAB = XYZtoLAB($cXYZ['X'], $cXYZ['Y'], $cXYZ['Z']);
    $cxyY = XYZtoxyY($cXYZ['X'], $cXYZ['Y'], $cXYZ['Z']);
    $cLCH = LABtoLCH($cLAB['L'], $cLAB['A'], $cLAB['B']);
    $cLUV = XYZtoLUV($cXYZ['X'], $cXYZ['Y'], $cXYZ['Z']);
    $cHunter = XYZtoHunter($cXYZ['X'], $cXYZ['Y'], $cXYZ['Z']);
    extract( shortcode_atts( array(
        'type' => 'HEX',
        'prop' => '',
    ), $atts, 'multilink' ) );
    switch ($type) {
        case 'DEC':
            return hexdec($colorHex);
        case 'HEX': // RGB hex
            return $colorHex;
        break;
        case 'RGBD': // RGB decimal
            switch($prop) {
                case 'R':
                    return $cInfo['r'];
                case 'G':
                    return $cInfo['g'];
                case 'B':
                    return $cInfo['b'];
                default:
                    return 0;
            }
        break;
        case 'RGBP': // RGB percent
            switch($prop) {
                case 'R':
                    return number_format($cnInfo['r'] * 100, 2);
                case 'G':
                    return number_format($cnInfo['g'] * 100, 2);
                case 'B':
                    return number_format($cnInfo['b'] * 100, 2);
                default:
                    return 0;
            }
        break;
        case 'CMYKP': // CMYK percent
            switch($prop) {
                case 'C':
                    return floor($cCMYK['C'] * 100);
                case 'M':
                    return floor($cCMYK['M'] * 100);
                case 'Y':
                    return floor($cCMYK['Y'] * 100);
                case 'K':
                    return floor($cCMYK['K'] * 100);
                default:
                    return 0;
            }
        break;
        case 'CMYKD': // CMYK decimal
            switch($prop) {
                case 'C':
                    return number_format($cCMYK['C'], 2);
                case 'M':
                    return number_format($cCMYK['M'], 2);
                case 'Y':
                    return number_format($cCMYK['Y'], 2);
                case 'K':
                    return number_format($cCMYK['K'], 2);
                default:
                    return 0;
            }
        break;
        case 'HSL': // HSL
            switch($prop) {
                case 'H':
                    return number_format($cHSL["H"] * 100, 1);
                case 'S':
                    return number_format($cHSL["S"] * 100, 1);
                case 'L':
                    return number_format($cHSL["L"] * 100, 1);
                default:
                    return 0;
            }
        break;
        case 'HSB':
        case 'HSV': // HSV/HSB            
            switch($prop) {
                case 'H':
                    return $cHSV['h'];
                case 'S':
                    return number_format($cHSV["s"] * 100, 1);
                case 'V':
                    return number_format($cHSV["v"] * 100, 1);
                default:
                    return 0;
            }
        break;
        case 'WEB': // Web safe
            return $colorHex;
        break;
        case 'LAB': // CIE LAB                     
            switch($prop) {
                case 'L':
                    return number_format($cLAB['L'], 3);
                case 'A':
                    return number_format($cLAB['A'], 3);
                case 'B':
                    return number_format($cLAB['B'], 3);
                default:
                    return 0;
            }
        break;
        case 'XYZ': // XYZ                     
            switch($prop) {
                case 'X':
                    return number_format($cXYZ['X'], 3);
                case 'Y':
                    return number_format($cXYZ['Y'], 3);
                case 'Z':
                    return number_format($cXYZ['Z'], 3);
                default:
                    return 0;
            }
        break;
        case 'xyY': // xyY                     
            switch($prop) {
                case 'x':
                    return number_format($cxyY['x'], 2);
                case 'y':
                    return number_format($cxyY['y'], 2);
                case 'Y':
                    return number_format($cxyY['Y'], 2);
                default:
                    return 0;
            }
        break;
        case 'LCH': // CIE-LCH                     
            switch($prop) {
                case 'L':
                    return number_format($cLCH['L'], 3);
                case 'C':
                    return number_format($cLCH['C'], 3);
                case 'H':
                    return number_format($cLCH['H'], 3);
                default:
                    return 0;
            }
        break;
        case 'LUV': // CIE-LUV                     
            switch($prop) {
                case 'L':
                    return number_format($cLUV['L'], 3);
                case 'U':
                    return number_format($cLUV['U'], 3);
                case 'V':
                    return number_format($cLUV['V'], 3);
                default:
                    return 0;
            }
        break;
        case 'HUNTER': //HUNTER-LAB                  
            switch($prop) {
                case 'L':
                    return number_format($cHunter['L'],3);
                case 'A':
                    return number_format($cHunter['A'],3);
                case 'B':
                    return number_format($cHunter['B'],3);
                default:
                    return 0;
            }
        break;
        case 'BIN': //Binary                              
            switch($prop) {
                case 'R':
                    return str_pad(decbin($cInfo['r']), 8, '0', STR_PAD_LEFT);
                case 'G':
                    return str_pad(decbin($cInfo['g']), 8, '0', STR_PAD_LEFT);
                case 'B':
                    return str_pad(decbin($cInfo['b']), 8, '0', STR_PAD_LEFT);
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

function color_inverse($color){
    $color = str_replace('#', '', $color);
    if (strlen($color) != 6){ return '000000'; }
    $rgb = '';
    for ($x=0;$x<3;$x++){
        $c = 255 - hexdec(substr($color,(2*$x),2));
        $c = ($c < 0) ? 0 : dechex($c);
        $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
    }
    return '#'.$rgb;
}

// Function to add color schemes to posts and pages
function color_schemes_shortcode(){
    if ($_GET["action"] === "edit" || $_GET["elementor-preview"]) // for disable preloading
        return;
    $colorHex = getHexFromArg();
    $cInfo = array(
        'r' => hexdec(substr($colorHex, 0, 2)),
        'g' => hexdec(substr($colorHex, 2, 2)),
        'b' => hexdec(substr($colorHex, 4, 2)),
    );
    $analog = RGBtoAnalog($cInfo['r'], $cInfo['g'], $cInfo['b']);
    $cHSV = RGBtoHSV($cInfo['r'], $cInfo['g'], $cInfo['b']);
    $start = sprintf("rgb(%d,%d,%d)", $analog[0]['R'], $analog[0]['G'], $analog[0]['B']);
    $end = sprintf("rgb(%d,%d,%d)", $analog[1]['R'], $analog[1]['G'], $analog[1]['B']);
    set_query_var( 'title', "Complemetary Color" );
    set_query_var( 'colors', array('#'.$colorHex, Color::inverse(Color::get("#".$colorHex))->__toString()));
    get_template_part( 'my-partials/color', 'scheme' );

    $colors = new AnalogousScheme("#".$colorHex);
    set_query_var( 'title', "Analogous Color" );
    set_query_var( 'colors', array($colors[0]->toRgb()->__toString(), $colors[1]->toRgb()->__toString(), $colors[2]->toRgb()->__toString()));
    get_template_part( 'my-partials/color', 'scheme' );

    $colors = new SplitComplementaryScheme("#".$colorHex);
    set_query_var( 'title', "Split Complementary Color" );
    set_query_var( 'colors', array($colors[1]->toRgb()->__toString(), $colors[0]->toRgb()->__toString(), $colors[2]->toRgb()->__toString()));
    get_template_part( 'my-partials/color', 'scheme' );

    $colors = new TriadicScheme("#".$colorHex);
    set_query_var( 'title', "Triadic Color" );
    set_query_var( 'colors', array($colors[1]->toRgb()->__toString(), $colors[0]->toRgb()->__toString(), $colors[2]->toRgb()->__toString()));
    get_template_part( 'my-partials/color', 'scheme' );    

    $colors = new TetradicScheme("#".$colorHex);
    set_query_var( 'title', "Tetradic Color" );
    set_query_var( 'colors', array($colors[1]->toRgb()->__toString(), $colors[0]->toRgb()->__toString(), $colors[2]->toRgb()->__toString(), $colors[3]->toRgb()->__toString()));
    get_template_part( 'my-partials/color', 'scheme' );

    set_query_var( 'title', "Monochromatic Color" );
    set_query_var( 'colors', array("#".$base->bg['-3'], "#".$base->bg['-2'], "#".$base->bg['-1'], "#".$base->bg['0'], "#".$base->bg['+1'], "#".$base->bg['+2'], "#".$base->bg['+3'] ));
    get_template_part( 'my-partials/color', 'scheme' );
}
add_shortcode('color-scheme', 'color_schemes_shortcode');



add_action( 'wp_ajax_nopriv_color_image', 'color_image' );
add_action( 'wp_ajax_color_image', 'color_image' );
function color_image() {      
    $im = imagecreatetruecolor(150, 200);
    
    $colorHex = $_GET['color'];
    $cInfo = array(
        'r' => hexdec(substr($colorHex, 0, 2)),
        'g' => hexdec(substr($colorHex, 2, 2)),
        'b' => hexdec(substr($colorHex, 4, 2)),
    );
    $background = imagecolorallocate($im, $cInfo['r'], $cInfo['g'], $cInfo['b']);
    imagefill($im, 0, 0,$background);

    // Set the content type header - in this case image/jpeg
    header('Content-Type: image/jpeg');

    // Output the image
    imagepng($im);

    // Free up memory
    imagedestroy($im);
}

?>