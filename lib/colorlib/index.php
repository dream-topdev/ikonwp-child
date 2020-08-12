<?php
function RGBtoHSV($r,$g,$b) {
    $r=($r/255); $g=($g/255); $b=($b/255);
    $maxRGB=max($r,$g,$b); $minRGB=min($r,$g,$b); $chroma=$maxRGB-$minRGB;
    if($chroma==0) return array('h'=>0,'s'=>0,'v'=>$maxRGB);
    if($r==$minRGB)$h=3-(($g-$b)/$chroma);
    elseif($b==$minRGB)$h=1-(($r-$g)/$chroma); else $h=5-(($b-$r)/$chroma);
    return array('h'=>60*$h,'s'=>$chroma/$maxRGB,'v'=>$maxRGB);
} 
function HSVtoRGB($H, $S, $V) {
    //H, S and V input range = 0 ÷ 1.0
    //R, G and B output range = 0 ÷ 255

    if ( $S == 0 )
    {
        $R = $V * 255;
        $G = $V * 255;
        $B = $V * 255;
    }
    else
    {
        $var_h = $H * 6;
        if ( $var_h == 6 ) $var_h = 0;      //H must be < 1
        $var_i = floor( $var_h );             //Or ... var_i = floor( var_h )
        $var_1 = $V * ( 1 - $S );
        $var_2 = $V * ( 1 - $S * ( $var_h - $var_i ) );
        $var_3 = $V * ( 1 - $S * ( 1 - ( $var_h - $var_i ) ) );

        if      ( $var_i == 0 ) { $var_r = $V     ; $var_g = $var_3 ; $var_b = $var_1; }
        else if ( $var_i == 1 ) { $var_r = $var_2 ; $var_g = $V     ; $var_b = $var_1; }
        else if ( $var_i == 2 ) { $var_r = $var_1 ; $var_g = $V     ; $var_b = $var_3; }
        else if ( $var_i == 3 ) { $var_r = $var_1 ; $var_g = $var_2 ; $var_b = $V;     }
        else if ( $var_i == 4 ) { $var_r = $var_3 ; $var_g = $var_1 ; $var_b = $V;     }
        else                   { $var_r = $V     ; $var_g = $var_1 ; $var_b = $var_2; }

        $R = $var_r * 255;
        $G = $var_g * 255;
        $B = $var_b * 255;
    }
    return array(
        'R' => $R,
        'G' => $G,
        'B' => $B
    );
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
function RGBtoAnalog($x1, $y1, $z1) {
    $a1 = 254; $b1 = 186; $c1 = 243;
    $a2 = 170; $b2 = 33; $c2 = 172;
    $a3 = 32; $b3 = 53; $c3 = 58;

    $x2 = ($a2 / $a1) * $x1;
    $y2 = ($b2 / $b1) * $y1;
    $z2 = ($c2 / $c1) * $z1;
    $x3 = ($a3 / $a2) * $x2;
    $y3 = ($b3 / $b2) * $y2;
    $z3 = ($c3 / $c2) * $z2;
    return array(
        array(
            'R' => floor($x2),
            'G' => floor($y2),
            'B' => floor($z2)
        ),
        array(
            'R' => floor($x3),
            'G' => floor($y3),
            'B' => floor($z3)
        )
    );
}