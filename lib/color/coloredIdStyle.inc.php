<?php

require_once __DIR__ . '/colorchange.inc.php';

/**
 * Merged from http://jiyuwiki.com/index.php?cmd=read&page=rep2%A4%C7%A3%C9%A3%C4%A4%CE%C7%D8%B7%CA%BF%A7%CA%D1%B9%B9&alias%5B%5D=pukiwiki%B4%D8%CF%A2
 *
 * @return  string
 */
function coloredIdStyle($idstr, $id, $count=0)
{
    static $idcount = array();
    static $idstyles = array();
    static $id_color_used= array() ;

    global $STYLE;

    if ($count >= 2) {
        //[$id] >= 2　ココの数字でスレに何個以上同じＩＤが出た時に背景色を変えるか決まる
        if (isset($idstyles[$id])) {
            return $idstyles[$id];
        } else {
            //	    	$alpha=0.8;	// アルファチャネル
            // IDから色の元を抽出

            $coldiv=64; // 色相環の分割数
            if (preg_match('/ID:/',$idstr)) { // IDが使える
                $rev_id=strrev(substr($id, 0, 8));
                $raw = base64_decode($rev_id);		// 8文字をバイナリデータ6文字分に変換
                $id_hex = unpack('H12', substr($raw, 0, 6));	// バイナリデータを16進文字列に変換
                $id_bin=base_convert($id_hex[1],16,2);	// さらに2進文字列に変換
                while ($id_bin) {
                    $arr[]=base_convert(substr($id_bin,-6),2,10);
                    $id_bin=substr($id_bin,0,-6);
                }

                $colors[0]=$arr[0];// % $coldiv;
                $idstr2=preg_split('/:/',$idstr,2); // コロンでID文字列を分割
                array_shift($idstr2);

                if ($id_color_used[$colors[0]]++) {
                    $colors[1]=$colors[0]+($id_color_used[$colors[0]]-1)+1;
                    $idstr2[1]=substr($idstr2[0],4);
                    $idstr2[0]=substr($idstr2[0],0,4); // コロンでID文字列を分割
                }
            } else { //シベリア板タイプ
                $ip_hex=preg_split('/\\./',$id);
                //var_dump($ip_hex);echo "<br>";
                $colors[1]=$ip_hex[1] % $coldiv;
                $idstr2=preg_split('/:/',$idstr,2); // コロンでID文字列を分割
                $idstr2[0].=':';

                if ($id_color_used[$colors[1]]++) {
                    $colors[2]=$colors[1]+($id_color_used[$colors[1]]-1)+1;
                    $idstr2[2]=".{$ip_hex[2]}.{$ip_hex[3]}";
                    $idstr2[1]="{$ip_hex[0]}.{$ip_hex[1]}"; // コロンでID文字列を分割
                }
            }
            $color_param=array();
            // HLS色空間
            // 色相H：値域0〜360（角度）
            // 輝度L(HLS)：値域0（黒）〜0.5（純色）〜1（白）
            // 彩度S(HLS)：値域0（灰色）〜1（純色）
            foreach ($colors as $key => $color) {
                //		    		var_dump(array(/*$raw,$id_hex,$arr,$col,*/$id_top,$c1,$c2));echo "<br>";
                $color_param[$key]=array();
                $angle=deg2rad($color*180/$coldiv);

                $color_param[$key]['H']=$color*360*4/$coldiv;
                while ($color_param[$key]['H']>360) {$color_param[$key]['H']-=360;}

                $color_param[$key]['L']=0.22+sin($angle)*0.08;
                $color_param[$key]['S']=0.4+sin($angle)*0.1;

                // RGBに変換
                $color_param[$key]=HLS2RGB($color_param[$key]);
                $color_param[$key]['Y']=(
                                         $color_param[$key]['R']*299+
                                         $color_param[$key]['G']*587+
                                         $color_param[$key]['B']*114
                                        )/1000;

            }

            // CSSで色をつける
            $uline=$STYLE['a_underline_none']==1 ? '' : "text-decoration:underline;";
            if ($count[$id]>=25 ) {     // 必死チェッカー発動
                $uline.="text-decoration:blink;";
            }
            $opacity=''; // "opacity:{$alpha};";
            foreach ($color_param as $area => $param) {
                $r=(int)$color_param[$area]['R'];
                $g=(int)$color_param[$area]['G'];
                $b=(int)$color_param[$area]['B'];
                if ($opacity || !$alpha) {
                    $bcolor[$area]="background-color:rgb({$r},{$g},{$b});";
                } else {
                    $bcolor[$area]="background-color:rgba({$r},{$g},{$b},{$alpha});";
                }

                // 背景色によって文字色を変える
              $y1=158;
              $y2=185;
                if ($param['Y']>=$y1) {
                    $y=($param['Y']-($param['Y']>=$y2 ? $y2 : $y1))/$param['Y'];

                        $r=(int)($r*$y);
                        $g=(int)($g*$y);
                        $b=(int)($b*$y);
                        $bcolor[$area].="color:rgb({$r},{$g},{$b});";
                } else {
                    $y1=140;
                    $y2=160;
                    if ($param['Y']<=255-$y1) {
                        $y=($param['Y']<=255-$y2 ? $y2 : $y1)/(255-$param['Y']);

                        $r+=(int)((255-$r)*$y);
                        $g+=(int)((255-$g)*$y);
                        $b+=(int)((255-$b)*$y);
                        $bcolor[$area].="color:rgb({$r},{$g},{$b});";
                    } else {
                        $bcolor[$area].="color:#fff;";
                    }
                }
                $idstr2[$area]="<span style=\"{$bcolor[$area]}{$border}{$uline}{$opacity}\">{$idstr2[$area]}</span>";
            }
//            var_dump(array('id'=>$id,'bcolor'=>$bcolor));echo "<br>";
            $idstr=join('',$idstr2);
            $idstyles[$id] = $bcolor;
            /*array(
                (isset($rgb[1]) ? "{$bcolor[1]}{$border}{$uline}" : ''),
                "{$bcolor[0]}{$border}{$uline}");
*/

        }
    }
//    var_dump(array('idstyles'=>$idstyles[$id]));echo "<br>";
    return $idstyles[$id];
}
