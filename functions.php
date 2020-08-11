<?php
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

function RGBtoHSV($r,$g,$b) {
    $r=($r/255); $g=($g/255); $b=($b/255);
    $maxRGB=max($r,$g,$b); $minRGB=min($r,$g,$b); $chroma=$maxRGB-$minRGB;
    if($chroma==0) return array('h'=>0,'s'=>0,'v'=>$maxRGB);
    if($r==$minRGB)$h=3-(($g-$b)/$chroma);
    elseif($b==$minRGB)$h=1-(($r-$g)/$chroma); else $h=5-(($b-$r)/$chroma);
    return array('h'=>60*$h,'s'=>$chroma/$maxRGB,'v'=>$maxRGB);
} 

function RGBtoXYZ($sR, $sG, $sB) {
    //sR, sG and sB (Standard RGB) input range = 0 ÷ 255
    //X, Y and Z output refer to a D65/2° standard illuminant.

    $var_R = ( $sR / 255 );
    $var_G = ( $sG / 255 );
    $var_B = ( $sB / 255 );

    if ( $var_R > 0.04045 ) $var_R = ( ( $var_R + 0.055 ) / 1.055 ) ^ 2.4;
    else                   $var_R = $var_R / 12.92;
    if ( $var_G > 0.04045 ) $var_G = ( ( $var_G + 0.055 ) / 1.055 ) ^ 2.4;
    else                   $var_G = $var_G / 12.92;
    if ( $var_B > 0.04045 ) $var_B = ( ( $var_B + 0.055 ) / 1.055 ) ^ 2.4;
    else                   $var_B = $var_B / 12.92;

    $var_R = $var_R * 100;
    $var_G = $var_G * 100;
    $var_B = $var_B * 100;

    $X = $var_R * 0.4124 + $var_G * 0.3576 + $var_B * 0.1805;
    $Y = $var_R * 0.2126 + $var_G * 0.7152 + $var_B * 0.0722;
    $Z = $var_R * 0.0193 + $var_G * 0.1192 + $var_B * 0.9505;
    return array(
        'X' => $X / 3,
        'Y' => $Y / 3,
        'Z' => $Z / 3
    );
}

function RGBtoHSL($R, $G, $B) {
    //R, G and B input range = 0 ÷ 255
    //H, S and L output range = 0 ÷ 1.0

    $var_R = ( $R / 255 );
    $var_G = ( $G / 255 );
    $var_B = ( $B / 255 );

    $var_Min = min( $var_R, $var_G, $var_B );    //Min. value of RGB
    $var_Max = max( $var_R, $var_G, $var_B );   //Max. value of RGB
    $del_Max = $var_Max - $var_Min;             //Delta RGB value

    $L = ( $var_Max + $var_Min )/ 2;

    if ( $del_Max == 0 )                     //This is a gray, no chroma...
    {
        $H = 0;
        $S = 0;
    }
    else                                    //Chromatic data...
    {
    if ( $L < 0.5 ) $S = $del_Max / ( $var_Max + $var_Min );
    else           $S = $del_Max / ( 2 - $var_Max - $var_Min );

    $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
    $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
    $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

    if      ( $var_R == $var_Max ) $H = $del_B - $del_G;
    else if ( $var_G == $var_Max ) $H = ( 1 / 3 ) + $del_R - $del_B;
    else if ( $var_B == $var_Max ) $H = ( 2 / 3 ) + $del_G - $del_R;

        if ( $H < 0 ) $H += 1;
        if ( $H > 1 ) $H -= 1;
    }
    return array(
        'H' => $H,
        'S' => $S,
        'L' => $L
    );
}

function RGBtoCMYK($R, $G, $B) {
    //R, G and B input range = 0 ÷ 255
    //C, M and Y output range = 0 ÷ 1.0

    $C = 1 - ( $R / 255 );
    $M = 1 - ( $G / 255 );
    $Y = 1 - ( $B / 255 );

    //C, M, Y and K range = 0 ÷ 1.0

    $var_K = 1;

    if ( $C < $var_K )   $var_K = $C;
    if ( $M < $var_K )   $var_K = $M;
    if ( $Y < $var_K )   $var_K = $Y;
    if ( $var_K == 1 ) {
        $C = 0;          //Black only
        $M = 0;
        $Y = 0;
    }
    else {
        $C = ( $C - $var_K ) / ( 1 - $var_K );
        $M = ( $M - $var_K ) / ( 1 - $var_K );
        $Y = ( $Y - $var_K ) / ( 1 - $var_K );
    }
    $K = $var_K;
    return array(
        'C' => $C,
        'M' => $M,
        'Y' => $Y,
        'K' => $K,
    );
}
function XYZtoLAB($X, $Y, $Z) {
    //Reference-X, Y and Z refer to specific illuminants and observers.
    //Common reference values are available below in this same page.
    $RX = 95.047; $RY = 100.000; $RZ = 108.883;
    $var_X = $X / $RX;
    $var_Y = $Y / $RY;
    $var_Z = $Z / $RZ;

    if ( $var_X > 0.008856 ) $var_X = pow($var_X, ( 1/3 ));
    else                    $var_X = ( 7.787 * $var_X ) + ( 16 / 116 );
    if ( $var_Y > 0.008856 ) $var_Y = pow($var_Y, ( 1/3 ));
    else                    $var_Y = ( 7.787 * $var_Y ) + ( 16 / 116 );
    if ( $var_Z > 0.008856 ) $var_Z = pow($var_Z, ( 1/3 ));
    else                    $var_Z = ( 7.787 * $var_Z ) + ( 16 / 116 );

    $L = ( 116 * $var_Y ) - 16;
    $A = 500 * ( $var_X - $var_Y );
    $B = 200 * ( $var_Y - $var_Z );
    return array(
        'L' => $L,
        'A' => $A,
        'B' => $B
    );
}
function XYZtoxyY($X, $Y, $Z) {
    return array (
        'Y' => $Y,
        'x' => $X / ( $X + $Y + $Z ),
        'y' => $Y / ( $X + $Y + $Z )
    );
}
function LABtoLCH($L, $A, $B) {
    $var_H = atan($B/ $A);  //Quadrant by signs

    $RX = 95.047; $RY = 100.000; $RZ = 108.883;
    if ( $var_H > 0 ) $var_H = ( $var_H / pi() ) * 180;
    else             $var_H = 360 - ( abs( $var_H ) / pi() ) * 180;

    return array(
        'L' => $L,
        'C' => sqrt( pow($A, 2) + pow($B, 2) ),
        'H' => $var_H
    );
}
function XYZtoLUV($X, $Y, $Z) {
    //Reference-X, Y and Z refer to specific illuminants and observers.
    //Common reference values are available below in this same page.

    $RX = 95.047; $RY = 100.000; $RZ = 108.883;

    $var_U = ( 4 * $X ) / ( $X + ( 15 * $Y ) + ( 3 * $Z ) );
    $var_V = ( 9 * $Y ) / ( $X + ( 15 * $Y ) + ( 3 * $Z ) );

    $var_Y = $Y / 100;
    if ( $var_Y > 0.008856 ) $var_Y = pow($var_Y, ( 1/3 ));
    else                    $var_Y = ( 7.787 * $var_Y ) + ( 16 / 116 );

    $ref_U = ( 4 * $RX ) / ( $RX + ( 15 * $RY ) + ( 3 * $RZ ) );
    $ref_V = ( 9 * $RY ) / ( $RX + ( 15 * $RY ) + ( 3 * $RZ ) );

    $L = ( 116 * $var_Y ) - 16;
    $U = 13 * $L * ( $var_U - $ref_U );
    $V = 13 * $L * ( $var_V - $ref_V );
    return array(
        'L' => $L,
        'U' => $U,
        'V' => $V
    );
}
function XYZtoHunter($X, $Y, $Z) {
    //Reference-X, Y and Z refer to specific illuminants and observers.
    //Common reference values are available below in this same page.
    $RX = 95.047; $RY = 100.000; $RZ = 108.883;
    $var_Ka = ( 175.0 / 198.04 ) * ( $RY + $RX );
    $var_Kb = (  70.0 / 218.11 ) * ( $RY + $RZ );

    $L = 100.0 * sqrt( $Y / $RY );
    $A = $var_Ka * ( ( ( $X / $RX ) - ( $Y / $RY ) ) / sqrt( $Y / $RY ) );
    $B = $var_Kb * ( ( ( $Y / $RY ) - ( $Z / $RZ ) ) / sqrt( $Y / $RY ) );
    return array(
        'L' => $L,
        'A' => $A,
        'B' => $B
    );
}
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
                    return $cXYZ['X'];
                case 'Y':
                    return $cXYZ['Y'];
                case 'Z':
                    return $cXYZ['Z'];
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
    
    $colorHex = getHexFromArg();
    set_query_var( 'title', "Complemetary Color" );
    set_query_var( 'colors', array('#'.$colorHex, color_inverse($colorHex)));
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