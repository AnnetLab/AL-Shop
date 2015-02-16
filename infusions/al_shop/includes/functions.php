<?php

// if total = true - each row must have total value
function build_cats_tree_array($cats, $total = false) {
    if (is_array($cats)) {
        $tree = array();
        foreach($cats as $id => &$row){
            if(empty($row['shp_cat_parent'])){
                $tree[$id] = &$row;
            } else {
                $cats[$row['shp_cat_parent']]['childs'][$id] = &$row;
                if ($total) {
                    $cats[$row['shp_cat_parent']]['total'] += $row['total'];
                }
            }
        }
    } else {
        return null;
    }
    return $tree;
}

function build_cats_tree_select($tree,$ident=0,$selected=0) {

    if (is_array($tree)) {
        $tree_select = "";
        foreach ($tree as $item) {
            $tree_select .= "<option value='".$item['shp_cat_id']."'".($selected == $item['shp_cat_id'] ? " selected='selected'" : "").">".make_ident($ident)." ".$item['shp_cat_title']."</option>";
            if (isset($item['childs'])) {
                $tree_select .= build_cats_tree_select($item['childs'],$ident+1,$selected);
            }
        }
    } else {
        return null;
    }
    return $tree_select;
}

function build_cats_tree_list($tree) {

    if (is_array($tree)) {
        $tree_list = "";
        $tree_list .= "<ul>";
        foreach ($tree as $item) {
            $tree_list .= "<li>";
            $tree_list .= "<span><a href='#".$item['shp_cat_id']."' cat-id='".$item['shp_cat_id']."'>".$item['shp_cat_title']."</a></span>";
            if (isset($item['childs'])) {
                $tree_list .= build_cats_tree_list($item['childs']);
            }
            $tree_list .= "</li>";
        }
        $tree_list .= "</ul>";
    } else {
        return null;
    }
    return $tree_list;
}

function make_ident($num) {
    $ident = "";
    for ($i=1;$i<=$num;$i++) {
        if ($i==1) $ident .= "&brvbar;";
        $ident .= "&minus;";
    }
    return $ident;
}

function get_current_currency() {

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CURRENCY);
    if (dbrows($result)) {
        while ($data = dbarray($result)) {
            $currency[$data['shp_currency_current']][$data['shp_currency_code']] = array('currency'=>$data['shp_currency_code'],'nominal'=>$data['shp_currency_nominal'],'rate'=>$data['shp_currency_rate']);
        }
        return $currency;
    } else {
        return false;
    }

}

function show_breadcrumbs($type,$id) {

    global $locale;

    switch ($type) {
        case "good":

            $good = dbarray(dbquery("SELECT shp_good_title, shp_good_cat, shp_good_id FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_id='".$id."'"));

            $str = "<a href='".BASEDIR."shop.php?action=good&id=".$good['shp_good_id']."'>".$good['shp_good_title']."</a>";

            $cat = dbarray(dbquery("SELECT shp_cat_title, shp_cat_parent, shp_cat_id FROM ".DB_AL_SHOP_CATS." WHERE shp_cat_id='".$good['shp_good_cat']."'"));
            $str = "<a href='".BASEDIR."shop.php?action=category&id=".$cat['shp_cat_id']."'>".$cat['shp_cat_title']."</a> > ".$str;

            if ($cat['shp_cat_parent'] != 0) {
                do {

                    $cat = dbarray(dbquery("SELECT shp_cat_title, shp_cat_parent, shp_cat_id FROM ".DB_AL_SHOP_CATS." WHERE shp_cat_id='".$cat['shp_cat_parent']."'"));
                    $str = "<a href='".BASEDIR."shop.php?action=category&id=".$cat['shp_cat_id']."'>".$cat['shp_cat_title']."</a> > ".$str;

                } while ($cat['shp_cat_parent'] != 0);

            }
            $str = "<a href='".BASEDIR."shop.php'>".$locale['shp85']."</a> > ".$str;

        break;
        case "cat":

            $cat = dbarray(dbquery("SELECT shp_cat_title, shp_cat_parent, shp_cat_id FROM ".DB_AL_SHOP_CATS." WHERE shp_cat_id='".$id."'"));
            $str = "<a href='".BASEDIR."shop.php?action=category&id=".$cat['shp_cat_id']."'>".$cat['shp_cat_title']."</a>";

            if ($cat['shp_cat_parent'] != 0) {
                do {

                    $cat = dbarray(dbquery("SELECT shp_cat_title, shp_cat_parent, shp_cat_id FROM ".DB_AL_SHOP_CATS." WHERE shp_cat_id='".$cat['shp_cat_parent']."'"));
                    $str = "<a href='".BASEDIR."shop.php?action=category&id=".$cat['shp_cat_id']."'>".$cat['shp_cat_title']."</a> > ".$str;

                } while ($cat['shp_cat_parent'] != 0);

            }
            $str = "<a href='".BASEDIR."shop.php'>".$locale['shp85']."</a> > ".$str;


        break;
        case "man":

            $man = dbarray(dbquery("SELECT shp_manufacturer_title, shp_manufacturer_id FROM ".DB_AL_SHOP_MANUFACTURES." WHERE shp_manufacturer_id='".$id."'"));
            $str = "<a href='".BASEDIR."shop.php?action=manufacturer&id=".$man['shp_manufacturer_id']."'>".$man['shp_manufacturer_title']."</a>";
            $str = "<a href='".BASEDIR."shop.php'>".$locale['shp85']."</a> > ".$str;


            break;
    }
    return $str;

}

function show_cost($cost,$currency,$currency_to_conv) {

    global $locale, $shop_settings;

    /*if ($shop_settings['currency_default'] != $currency) {

        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CURRENCY." WHERE shp_currency_current='".$shop_settings['currency_default']."' AND shp_currency_code='".$currency."'");
        if (dbrows($result)) {
            $data = dbarray($result);
            $conv_cost = round(($cost/$data['shp_currency_nominal'])*$data['shp_currency_rate'],2);
            return $conv_cost." ".$locale['shp'.$shop_settings['currency_default']];
        } else {
            return $cost." ".$locale['shp'.$currency]." <i style='font-size:10px;'>(не указаны курсы для конверсии)</i>";
        }

    } else {
        return $cost." ".$locale['shp'.$currency];
    }*/

    if ($currency != $currency_to_conv) {

        if ($currency != $shop_settings['currency_default'] && $currency_to_conv != $shop_settings['currency_default']) {
            //konvertim v 2 deistviya;
            $result1 = dbquery("SELECT * FROM ".DB_AL_SHOP_CURRENCY." WHERE shp_currency_current='".$shop_settings['currency_default']."' AND shp_currency_code='".$currency."'");
            $result2 = dbquery("SELECT * FROM ".DB_AL_SHOP_CURRENCY." WHERE shp_currency_current='".$shop_settings['currency_default']."' AND shp_currency_code='".$currency_to_conv."'");
            if (dbrows($result1) && dbrows($result2)) {
                $data1 = dbarray($result1);
                $data2 = dbarray($result2);
                $cost_def = ($cost/$data1['shp_currency_nominal'])*$data1['shp_currency_rate'];
                $conv_cost = round(($cost_def/$data2['shp_currency_rate'])*$data2['shp_currency_nominal'],2);
                return $conv_cost." ".$locale['shp'.$currency_to_conv];

            } else {
                return $cost." ".$locale['shp'.$currency]." <i style='font-size:10px;'>(не указаны курсы для конверсии)</i>";
            }

        } else {
            // konvertim v odno deistvie;
            if ($currency == $shop_settings['currency_default']) {

                $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CURRENCY." WHERE shp_currency_current='".$currency."' AND shp_currency_code='".$currency_to_conv."'");
                if (dbrows($result)) {
                    $data = dbarray($result);
                    $conv_cost = round(($cost/$data['shp_currency_rate'])*$data['shp_currency_nominal'],2);
                    return $conv_cost." ".$locale['shp'.$currency_to_conv];
                } else {
                    return $cost." ".$locale['shp'.$currency]." <i style='font-size:10px;'>(не указаны курсы для конверсии)</i>";
                }

            } else if ($currency_to_conv == $shop_settings['currency_default']) {

                $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CURRENCY." WHERE shp_currency_current='".$currency_to_conv."' AND shp_currency_code='".$currency."'");
                if (dbrows($result)) {
                    $data = dbarray($result);
                    $conv_cost = round(($cost/$data['shp_currency_nominal'])*$data['shp_currency_rate'],2);
                    return $conv_cost." ".$locale['shp'.$currency_to_conv];
                } else {
                    return $cost." ".$locale['shp'.$currency]." <i style='font-size:10px;'>".$locale['shp89']."</i>";
                }

            }
        }

    } else {
        return $cost." ".$locale['shp'.$currency];
    }

}

function convert_cost($cost,$currency,$currency_to_conv) {

    global $locale, $shop_settings;

    if ($currency != $currency_to_conv) {

        if ($currency != $shop_settings['currency_default'] && $currency_to_conv != $shop_settings['currency_default']) {
            //konvertim v 2 deistviya;
            $result1 = dbquery("SELECT * FROM ".DB_AL_SHOP_CURRENCY." WHERE shp_currency_current='".$shop_settings['currency_default']."' AND shp_currency_code='".$currency."'");
            $result2 = dbquery("SELECT * FROM ".DB_AL_SHOP_CURRENCY." WHERE shp_currency_current='".$shop_settings['currency_default']."' AND shp_currency_code='".$currency_to_conv."'");
            if (dbrows($result1) && dbrows($result2)) {
                $data1 = dbarray($result1);
                $data2 = dbarray($result2);
                $cost_def = ($cost/$data1['shp_currency_nominal'])*$data1['shp_currency_rate'];
                $conv_cost = round(($cost_def/$data2['shp_currency_rate'])*$data2['shp_currency_nominal'],2);
                return $conv_cost;

            } else {
                return $cost;
            }

        } else {
            // konvertim v odno deistvie;
            if ($currency == $shop_settings['currency_default']) {

                $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CURRENCY." WHERE shp_currency_current='".$currency."' AND shp_currency_code='".$currency_to_conv."'");
                if (dbrows($result)) {
                    $data = dbarray($result);
                    $conv_cost = round(($cost/$data['shp_currency_rate'])*$data['shp_currency_nominal'],2);
                    return $conv_cost;
                } else {
                    return $cost;
                }

            } else if ($currency_to_conv == $shop_settings['currency_default']) {

                $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CURRENCY." WHERE shp_currency_current='".$currency_to_conv."' AND shp_currency_code='".$currency."'");
                if (dbrows($result)) {
                    $data = dbarray($result);
                    $conv_cost = round(($cost/$data['shp_currency_nominal'])*$data['shp_currency_rate'],2);
                    return $conv_cost;
                } else {
                    return $cost;
                }

            }
        }

    } else {
        return $cost;
    }

}

function num2str($num,$currency='RUB') {

    global $locale;
    $nul=$locale['shp_0'];
    $ten=array(
        array('',$locale['shp_1'],$locale['shp_2'],$locale['shp_3'],$locale['shp_4'],$locale['shp_5'],$locale['shp_6'],$locale['shp_7'],$locale['shp_8'],$locale['shp_9']),
        array('',$locale['shp__1'],$locale['shp__2'],$locale['shp__3'],$locale['shp__4'],$locale['shp__5'],$locale['shp__6'],$locale['shp__7'],$locale['shp__8'],$locale['shp__9']),
    );
    $a20=array($locale['shp_10'],$locale['shp_11'],$locale['shp_12'],$locale['shp_13'],$locale['shp_14'],$locale['shp_15'],$locale['shp_16'],$locale['shp_17'],$locale['shp_18'],$locale['shp_19']);
    $tens=array(2=>$locale['shp_20'],$locale['shp_30'],$locale['shp_40'],$locale['shp_50'],$locale['shp_60'],$locale['shp_70'],$locale['shp_80'],$locale['shp_90']);
    $hundred=array('',$locale['shp_100'],$locale['shp_200'],$locale['shp_300'],$locale['shp_400'],$locale['shp_500'],$locale['shp_600'],$locale['shp_700'],$locale['shp_800'],$locale['shp_900']);
    $unit=array( // Units
        array($locale['shp__'.$currency.'1'] ,$locale['shp__'.$currency.'2'],$locale['shp__'.$currency.'3'], 1),
        array($locale['shp_'.$currency.'1'] ,$locale['shp_'.$currency.'2'],$locale['shp_'.$currency.'3'], 0),
        array($locale['shp_k1'] ,$locale['shp_k2'],$locale['shp_k3']     ,1),
        array($locale['shp_mil1'] ,$locale['shp_mil2'],$locale['shp_mil3'] ,0),
        array($locale['shp_mill1'] ,$locale['shp_mill2'],$locale['shp_mill3'] ,0),
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    $kop = ltrim($kop,'0');
    if (intval($kop)>0) {
        if ($kop>=10 && $kop<=20) {
            $out[] = $a20[$kop%10];
        } else if ($kop>20) {
            $out[] = $tens[($kop-$kop%10)/10];
            $out[] = $ten[1][$kop%10];
        } else {
            $out[] = $ten[1][$kop];
        }
    } else $out[] = $nul;
    $out[] = morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}

function make_assoc($result) {
    $assoc = array();
    while ($data = dbarray($result)) {
        $assoc[] = $data;
    }
    return $assoc;
}

function get_good_cover($cover,$type='thumb') {

    /*if ($images_str != "") {
        $images_array = explode(".",$images_str); $k = 0;
        do {
            $img_result = dbquery("SELECT * FROM ".DB_AL_SHOP_IMAGES." WHERE shp_image_id='".$images_array[$k]."'");
            $k++;
        } while (!$img_result && !dbrows($img_result));
        $data = dbarray($img_result);
        $img = file_exists(AL_SHOP_DIR."asset/goods/".$data['shp_image_thumb']) ? AL_SHOP_DIR."asset/goods/".$data['shp_image_thumb'] : AL_SHOP_DIR."asset/no_image.gif";
    } else {
        $img = AL_SHOP_DIR."asset/no_image.gif";
    }
    return $img;*/
    if ($cover != 0) {
        $img_result = dbquery("SELECT * FROM ".DB_AL_SHOP_IMAGES." WHERE shp_image_id='".$cover."'");
        $data = dbarray($img_result);
        $img = file_exists(AL_SHOP_DIR."asset/goods/".$data['shp_image_'.$type]) ? AL_SHOP_DIR."asset/goods/".$data['shp_image_'.$type] : AL_SHOP_DIR."asset/no_image.gif";
    } else {
        $img = AL_SHOP_DIR."asset/no_image.gif";
    }
    return $img;

}

function make_search_index($good_id) {

    $result = dbquery("SELECT shp_good_title, shp_good_cat FROM ".DB_AL_SHOP_GOODS." WHERE shp_good_id='".$good_id."'");
    if (dbrows($result)) {
        $good = dbarray($result);
        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CATS." WHERE shp_cat_id='".$good['shp_good_cat']."'");
        if (dbrows($result)) {
            $cat = dbarray($result);
            $cats[] = $cat;
            if ($cat['shp_cat_parent'] != 0) {
                do {
                    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CATS." WHERE shp_cat_id='".$cat['shp_cat_parent']."'");
                    if (dbrows($result)) {
                        $cat = dbarray($result);
                        $cats[] = $cat;
                    } else {
                        return false;
                    }
                } while ($cat['shp_cat_parent'] != 0);
            }
            array_reverse($cats);
            $str = "";
            foreach ($cats as $cat) {
                $str .= $cat['shp_cat_title']." ";
            }
            $str .= $good['shp_good_title'];
            $result = dbquery("SELECT * FROM ".DB_AL_SHOP_SEARCH." WHERE shp_search_good='".$good_id."'");
            if (dbrows($result)) {
                $result = dbquery("UPDATE ".DB_AL_SHOP_SEARCH." SET shp_search_key='".$str."' WHERE shp_search_good='".$good_id."'");
            } else {
                $result = dbquery("INSERT INTO ".DB_AL_SHOP_SEARCH." (shp_search_key,shp_search_good) VALUES ('".$str."','".$good_id."')");
            }
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }

}

function show_payed_status($status) {
    global $locale;
    switch ($status) {
        case 0:
            return $locale['shp127'];
        break;
        case 1:
            return $locale['shp173'];
        break;
        case 2:
            return $locale['shp290'];
        break;

    }
}

function show_payment_type($type) {
    global $locale;
    switch ($type) {
        case 1:
            return $locale['shp131'];
        break;
        case 2:
            return $locale['shp183'];
        break;
        case 3:
            return $locale['shp286'];
        break;
        case 4:
            return $locale['shp291'];
        break;
    }
}


function show_delivery($id) {

    global $locale;
    if ($id == 0) {
        return $locale['shp267'];
    } else {
        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_DELIVERIES." WHERE shp_delivery_id='".$id."'");
        if (dbrows($result)) {
            $data = dbarray($result);
            return $data['shp_delivery_title'];
        } else {
            return $locale['shp267'];
        }
    }

}

function get_client_data() {

    global $userdata;

    if (iMEMBER) {
        $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CLIENTS_DATA." WHERE shp_client_user='".$userdata['user_id']."'");
        if (dbrows($result)) {
            $data = dbarray($result);
            return json_decode($data['shp_client_data'],true);
        } else {
            return false;
        }
    } else {
        return false;
    }

}





?>