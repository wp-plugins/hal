<?php
/**
* Plugin Name: HAL
* Plugin URI: http://www.ccsd.cnrs.fr
* Description: Crée une page qui remonte les publications d'un auteur ou d'une structure en relation avec HAL et un widget des dernières publications d'un auteur ou d'une structure.
* Version: 1.0
* Author: Baptiste Blondelle
* Author URI: http://www.ccsd.cnrs.fr
* Text Domain: wp-hal
* Domain Path: /lang/
*/


// Traduction de la description
__("Crée une page qui remonte les publications d'un auteur ou d'une structure en relation avec HAL et un widget des dernières publications d'un auteur ou d'une structure.", "wp-hal");

//Récupère les constantes
require_once("settings.php");

if (WPLANG == 'fr_FR') {
    define('lang', 'fr_');
} elseif (WPLANG == 'es_ES') {
    define('lang', 'es_');
} else {
    define('lang', 'en_');
}

// Création du shortcode ('nom du shortcode', 'fonction appelée')
add_shortcode( 'cv-hal', 'cv_hal' );


function charger_languages() {
    load_plugin_textdomain('wp-hal', false, dirname(plugin_basename(__FILE__)) . '/lang/');
}

add_action('plugins_loaded', 'charger_languages');

function hal_plugin_action_links( $links, $file ) {
    if ( $file != plugin_basename( __FILE__ ))
        return $links;

    $settings_link = '<a href="admin.php?page=wp-hal.php">' . __( 'Paramètres', 'wp-hal' ) . '</a>';

    array_unshift( $links, $settings_link );

    return $links;
}


add_filter( 'plugin_action_links', 'hal_plugin_action_links',10,2);


/***********************************************************************************************************************
 * PLUGIN SHORTCODE
 **********************************************************************************************************************/
function cv_hal(){

    if (get_option('option_groupe')=='grouper'){
    //cURL sur l'API pour récupérer les données
    $url = api . '?q=*:*&fq='.get_option('option_type').':('. urlencode(get_option('option_idhal')).')&group=true&group.field=docType_s&group.limit=1000&fl=docid,citationFull_s&facet.field=' . lang .'domainAllCodeLabel_fs&facet.field=keyword_s&facet.field=journalIdTitle_fs&facet.field=producedDateY_i&facet.field=authIdLastNameFirstName_fs&facet.field=instStructIdName_fs&facet.field=labStructIdName_fs&facet.field=deptStructIdName_fs&facet.field=rteamStructIdName_fs&facet.mincount=1&facet=true&sort=' . producedDateY . '&wt=json&json.nl=arrarr';
    } elseif (get_option('option_groupe')=='paginer'){
    $url = api . '?q=*:*&fq='.get_option('option_type').':('.urlencode(get_option('option_idhal')).')&fl=docid,citationFull_s&facet.field=' . lang .'domainAllCodeLabel_fs&facet.field=keyword_s&facet.field=journalIdTitle_fs&facet.field=producedDateY_i&facet.field=authIdLastNameFirstName_fs&facet.field=instStructIdName_fs&facet.field=labStructIdName_fs&facet.field=deptStructIdName_fs&facet.field=rteamStructIdName_fs&facet.mincount=1&facet=true&sort=' . producedDateY . '&wt=json&json.nl=arrarr';
    }

    $ch = curl_init($url);
    // Options
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Content-type: application/json'),
        CURLOPT_TIMEOUT => 10,
    );

    // Bind des options et de l'objet cURL que l'on va utiliser
    curl_setopt_array($ch, $options);
    // Récupération du résultat JSON
    $json = json_decode(curl_exec($ch));
    curl_close($ch);

    //cURL sur l'API doctype pour récupérer les labels des docType
    $chtype = curl_init(urltype);
    // Options
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Content-type: application/json'),
        CURLOPT_TIMEOUT => 10,
    );

    // Bind des options et de l'objet cURL que l'on va utiliser
    curl_setopt_array($chtype, $options);
    // Récupération du résultat JSON
    $jsontype = json_decode(curl_exec($chtype));
    curl_close($chtype);

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    $content = '
<div id="content-plugin">
    <ul id="tabs" class="nav nav-pills">';
    $content .= '<li class="active"><a href="#publications" data-toggle="tab" style="font-size:18px; text-decoration: none;">' . __('Publications', 'wp-hal'). '</a></li>';
    $content .= '<li><a href="#filtres" data-toggle="collapse" style="font-size:18px; text-decoration: none;">' . __('Filtres', 'wp-hal'). '<span class="caret"></span></a></li><li>';
    $content .= '<ul class="nav nav-pills collapse" id="filtres">';
    if (get_option('option_choix')[1] == 'contact') {
        $content .= '<li><a class="subnavtab" href="#contact" data-toggle="tab" style="margin:1px; text-decoration: none;">' . __('Contact', 'wp-hal');
    $content .= '</a></li>';}
    if (get_option('option_choix')[2] == 'disciplines') {
        $content .= '<li><a class="subnavtab" href="#disciplines" data-toggle="tab" style="margin:1px; text-decoration: none;">' . __('Disciplines', 'wp-hal');
    $content .= '</a></li>';}
    if (get_option('option_choix')[3] == 'mots-clefs') {
        $content .= '<li><a class="subnavtab" href="#keywords" data-toggle="tab" style="margin:1px; text-decoration: none;">' . __('Mots-clefs', 'wp-hal');
    $content .= '</a></li>';}
    if (get_option('option_choix')[4] == 'auteurs') {
        $content .= '<li><a class="subnavtab" href="#auteurs" data-toggle="tab" style="margin:1px; text-decoration: none;">' . __('Auteurs', 'wp-hal');
    $content .= '</a></li>';}
    if (get_option('option_choix')[5] == 'revues') {
        $content .= '<li><a class="subnavtab" href="#revues" data-toggle="tab" style="margin:1px; text-decoration: none;">' . __('Revues', 'wp-hal');
    $content .= '</a></li>';}
    if (get_option('option_choix')[6] == 'annee') {
        $content .= '<li><a class="subnavtab" href="#annees" data-toggle="tab" style="margin:1px; text-decoration: none;">' . __('Année de production', 'wp-hal');
    $content .= '</a></li>';}
    if (get_option('option_choix')[7] == 'institution') {
        $content .= '<li><a class="subnavtab" href="#insts" data-toggle="tab" style="margin:1px; text-decoration: none;">' . __('Institutions', 'wp-hal');
    $content .= '</a></li>';}
    if (get_option('option_choix')[8] == 'laboratoire') {
        $content .= '<li><a class="subnavtab" href="#labs" data-toggle="tab" style="margin:1px; text-decoration: none;">' . __('Laboratoires', 'wp-hal');
    $content .= '</a></li>';}
    if (get_option('option_choix')[9] == 'departement') {
        $content .= '<li><a class="subnavtab" href="#depts" data-toggle="tab" style="margin:1px; text-decoration: none;">' . __('Départements', 'wp-hal');
    $content .= '</a></li>';}
    if (get_option('option_choix')[10] == 'equipe') {
        $content .= '<li><a class="subnavtab" href="#equipes" data-toggle="tab" style="margin:1px; text-decoration: none;">' . __('Équipes de recherche', 'wp-hal');
    $content .= '</a></li>';}
    $content .= '</ul></li>';
    $content .= '</ul><br/><hr>';

    $content .= '<div id="my-tab-content" class="tab-content">
        <div class="tab-pane" id="contact">
            <h3>' . __('Contact','wp-hal');  $content .= '</h3>

            <ul style="list-style-type: none;">';
                if (get_option('option_email') != ''){
                    $content .= '<li><img alt="mail" src=" ' . plugin_dir_url( __FILE__ ) . 'img/mail.svg" style=" width:16px; margin-left:2px; margin-right:2px;"/><a href="mailto:' . get_option('option_email') . '" target="_blank">' . get_option('option_email') . '</a></li>';
                }
                if (get_option('option_tel') != ''){
                    $content .= '<li><img alt="phone" src=" ' . plugin_dir_url( __FILE__ ) . 'img/phone.svg" style="width:16px; margin-left:2px; margin-right:2px;"/>' . get_option('option_tel') . '</li>';
                }
                if (get_option('option_social0') != ''){
                    $content .= '<li><a href="http://www.facebook.com/' . get_option('option_social0') . '" target="_blank"><img src=" ' . plugin_dir_url( __FILE__ ) . 'img/facebook.svg" style="width:32px; margin:4px;"/></a>';
                }
                if (get_option('option_social1') != ''){
                    $content .= '<a href="http://www.twitter.com/' . get_option('option_social1') . '" target="_blank"><img src=" ' . plugin_dir_url( __FILE__ ) . 'img/twitter.svg" style="width:32px; margin:4px;"/></a>';
                }
                if (get_option('option_social2') != ''){
                    $content .= '<a href="https://plus.google.com/u/0/+' . get_option('option_social2') . '" target="_blank"><img src=" ' . plugin_dir_url( __FILE__ ) . 'img/google-plus.svg" style="width:32px; margin:4px;"/></a>';
                }
                if (get_option('option_social3') != ''){
                    $content .= '<a href="http://sa.linkedin.com/pub/' . get_option('option_social3') . '" target="_blank"><img src=" ' . plugin_dir_url( __FILE__ ) . 'img/linkedin.svg" style="width:32px; margin:4px;"/></a></li>';
                }
            $content .= '</ul>
        </div>
        <div class="tab-pane" id="disciplines">
            <h3>' . __('Disciplines','wp-hal').'</h3>';

    if (WPLANG == 'fr_FR') {
        $facetdomain = $json->facet_counts->facet_fields->fr_domainAllCodeLabel_fs;
    } elseif (WPLANG == 'es_ES') {
        $facetdomain = $json->facet_counts->facet_fields->es_domainAllCodeLabel_fs;
    } else {
        $facetdomain = $json->facet_counts->facet_fields->en_domainAllCodeLabel_fs;
    }

    if(!is_null($facetdomain)  && !empty($facetdomain)){

        $content .= '<div id="listdisci">';
        $content .= '<ul class="discipline list-group">';
        $content .= '<li class="list-group-item">';
        $content .= '<span id="tridisciplines">';
        $content .= '<a class="btn btn-default btn-xs" data-placement="bottom" data-toggle="tooltip" data-original-title="Tri Alphabétique" href="" id="tridisci" onclick="javascript:toggleSort(this, true, \'discipline\', \'discipline\', \'tridisci\'); return false;" style="font-size:16px;  text-decoration: none;" ><span class="glyphicon glyphicon-sort-by-alphabet"></span></a>';
        $content .= '<a class="pull-right btn btn-default btn-xs"  data-placement="bottom" data-toggle="tooltip" data-original-title="Tri par Nombre d\'Occurences" href="" id="trinbdisci" onclick="javascript:toggleSort(this, false, \'discipline\', \'discipline\', \'trinbdisci\'); return false;" style="font-size:16px;  text-decoration: none;"><span class="glyphicon glyphicon-sort-by-order"></span></a>';
        $content .= '</span>';
        $content .= '</li>';
        $content .= '<div id="discipline">';
        foreach ($facetdomain as $res){
            $name = explode(delimiter,$res[0]);
            $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=domainAllCode_s:' . urlencode($name[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">'.$name[1].'</a><span class="badge badge-default">' .$res[1]. '</span></li>';
        }
        $content .= '</div>';
        $content .= '</ul>';
        $content .= '</div>';

        $content .= '<div id="graph" style="display:none;">';
        $content .= '<div id="toto"></div>';
        $content .= '</div>';
        $content .= '<a class="btn btn-default btn-sm" href="" style="text-decoration: none;" id="disci" onclick="javascript:visibilitedisci(\'listdisci\',\'graph\',\'tridisciplines\'); return false;" >' . __('Graphique','wp-hal'); $content .= '</a> ';
    }
    $content .= '</div>
        <div class="tab-pane" id="keywords">
            <h3>' . __('Mots-clefs','wp-hal').'</h3>';
    if(!is_null($json->facet_counts->facet_fields->keyword_s)  && !empty($json->facet_counts->facet_fields->keyword_s)){
        $content .= '<div id="keys">';
        $r = 0;
        // CSS Nuage de mots
        $maxsize=25;
        $minsize=10;
        $maxval=max(array_values($json->facet_counts->facet_fields->keyword_s[1]));
        $minval=min(array_values($json->facet_counts->facet_fields->keyword_s[1]));
        $spread=($maxval-$minval);
        $step=($maxsize-$minsize)/$spread;
        $tab = array();
        foreach ($json->facet_counts->facet_fields->keyword_s as $res){
            $r = $r+1;
            if ($r > 20){
                break;
            }
            $tab[] = $res;
        }
        asort($tab); // Tri du tableau par ordre alphabétique
        foreach ($tab as $res){
            $size= round($minsize + (($res[1]- $minval)*$step));
            $content .= '<a style="font-size:'.$size.'px" href="'. halv3 .'?q=keyword_s:' . urlencode('"'.$res[0].'"') . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $res[0] . '</a>&nbsp;';
        }
        $content .= '</div>';
        $content .= '<div id="keysuite" style="display:none;">';
        $content .= '<ul class="keyword list-group">';
        $content .= '<li class="list-group-item">';
        $content .= '<span id="trikeywords">';
        $content .= '<a class="btn btn-default btn-xs" data-placement="bottom" data-toggle="tooltip" data-original-title="Tri Alphabétique" href="" id="trikey" onclick="javascript:toggleSort(this, true, \'keyw\', \'keyword\', \'trikey\'); return false;" style="font-size:16px;  text-decoration: none;" ><span class="glyphicon glyphicon-sort-by-alphabet"></span></a>';
        $content .= '<a class="pull-right btn btn-default btn-xs"  data-placement="bottom" data-toggle="tooltip" data-original-title="Tri par Nombre d\'Occurences" href="" id="trinbkey" onclick="javascript:toggleSort(this, false, \'keyw\', \'keyword\', \'trinbkey\'); return false;" style="font-size:16px;  text-decoration: none;"><span class="glyphicon glyphicon-sort-by-order"></span></a>';
        $content .= '</span>';
        $content .= '</li>';
        $content .= '<div id="keyw">';
        foreach ($json->facet_counts->facet_fields->keyword_s as $res){
            $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=keyword_s:'.urlencode('"'.$res[0].'"'). "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $res[0] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
        }
        $content .= '</div>';
        $content .= '</ul>';
        $content .= '</div>';
        $content .= '<a class="btn btn-default btn-sm" href="" style="text-decoration: none;" id="key" onclick="javascript:visibilitekey(\'keys\',\'keysuite\', \'trikeywords\'); return false;" >' . __('Liste complète','wp-hal'); $content .= '</a> ';
    }
    $content .= '</div>
        <div class="tab-pane" id="auteurs">
            <h3>' . __('Auteurs','wp-hal').'</h3>';
    if(!is_null($json->facet_counts->facet_fields->authIdLastNameFirstName_fs)  && !empty($json->facet_counts->facet_fields->authIdLastNameFirstName_fs)){
        $content .= '<ul class="auteurs list-group" style="list-style-type: none;">';
        $content .= '<li class="list-group-item">';
        $content .= '<span id="triauteurs">';
        $content .= '<a class="btn btn-default btn-xs" data-placement="bottom" data-toggle="tooltip" data-original-title="Tri Alphabétique" href="" id="triaut" onclick="javascript:toggleSort(this, true, \'aut\', \'auteursuite\', \'triaut\'); return false;" style="font-size:16px;  text-decoration: none;" ><span class="glyphicon glyphicon-sort-by-alphabet"></span></a>';
        $content .= '<a class="pull-right btn btn-default btn-xs"  data-placement="bottom" data-toggle="tooltip" data-original-title="Tri par Nombre d\'Occurences" href="" id="trinbaut" onclick="javascript:toggleSort(this, false, \'aut\', \'auteursuite\', \'trinbaut\'); return false;" style="font-size:16px;  text-decoration: none;"><span class="glyphicon glyphicon-sort-by-order"></span></a>';
        $content .= '</span>';
        $content .= '</li>';
        $content .= '<div id="aut">';
        $r = 0;
        foreach ($json->facet_counts->facet_fields->authIdLastNameFirstName_fs as $res){
            $r = $r+1;
            if ($r > 10){
                break;
            }
            $name = explode(delimiter,$res[0]);
            $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><img alt="user" src=" ' . plugin_dir_url( __FILE__ ) . '/img/user.svg" style="width:16px; margin-left:2px; margin-right:2px;"/><a href="'. halv3 .'?q=authId_i:' . $name[0] . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $name[1] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
        }
        $i = 1;
        $content .= '<div id="auteursuite" style="display:none;">';
        foreach ($json->facet_counts->facet_fields->authIdLastNameFirstName_fs as $res){
            if($r < 10){
                break;
            }
            if ($i < $r){
                $i = $i+1;
            } else {
                $name = explode(delimiter,$res[0]);
                $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><img alt="user" src=" ' . plugin_dir_url( __FILE__ ) . '/img/user.svg" style="width:16px; margin-left:2px; margin-right:2px;"/><a href="'. halv3 .'?q=authId_i:' . $name[0] . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $name[1] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
            }
        }
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</ul>';
        if ($r > 10){
            $content .= '<a class="btn btn-default btn-sm" href="" style="text-decoration: none;" id="auteur" onclick="javascript:visibilite(\'auteursuite\'); return false;" >' . __('Liste complète','wp-hal'); $content .= '</a> ';
        }
    }
    $content .= '</div>
        <div class="tab-pane" id="revues">
            <h3>' . __('Revues','wp-hal').'</h3>';
    if(!is_null($json->facet_counts->facet_fields->journalIdTitle_fs)  && !empty($json->facet_counts->facet_fields->journalIdTitle_fs)){
        $content .= '<ul class="revues list-group">';
        $content .= '<li class="list-group-item">';
        $content .= '<span id="trirevues">';
        $content .= '<a class="btn btn-default btn-xs" data-placement="bottom" data-toggle="tooltip" data-original-title="Tri Alphabétique" href="" id="trirev" onclick="javascript:toggleSort(this, true, \'rev\', \'revuesuite\', \'trirev\'); return false;" style="font-size:16px;  text-decoration: none;" ><span class="glyphicon glyphicon-sort-by-alphabet"></span></a>';
        $content .= '<a class="pull-right btn btn-default btn-xs"  data-placement="bottom" data-toggle="tooltip" data-original-title="Tri par Nombre d\'Occurences" href="" id="trinbrev" onclick="javascript:toggleSort(this, false, \'rev\', \'revuesuite\', \'trinbrev\'); return false;" style="font-size:16px;  text-decoration: none;"><span class="glyphicon glyphicon-sort-by-order"></span></a>';
        $content .= '</span>';
        $content .= '</li>';
        $content .= '<div id="rev">';
        $r = 0;
        foreach ($json->facet_counts->facet_fields->journalIdTitle_fs as $res){
            $r = $r+1;
            if ($r > 10){
                break;
            }
            $name = explode(delimiter,$res[0]);
            $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=journalId_i:' . urlencode($name[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $name[1] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
        }
        $i = 1;
        $content .= '<div id="revuesuite" style="display:none;">';
        foreach ($json->facet_counts->facet_fields->journalIdTitle_fs as $res){
            if($r < 10){
                break;
            }
            if ($i < $r){
                $i = $i+1;
            } else {
                $name = explode(delimiter,$res[0]);
                $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=journalId_i:' . urlencode($name[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $name[1] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
            }
        }
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</ul>';
        if ($r > 10){
            $content .= '<a class="btn btn-default btn-sm" href="" style="text-decoration: none;" id="revue" onclick="javascript:visibiliterevues(\'revuesuite\'); return false;" >' . __('Liste complète','wp-hal'); $content .= '</a> ';
        }
    }
    $content .= '</div>
        <div class="tab-pane" id="annees">
            <h3>' . __('Année de production','wp-hal').'</h3>';
    if(!is_null($json->facet_counts->facet_fields->producedDateY_i)  && !empty($json->facet_counts->facet_fields->producedDateY_i)){
        $content .= '<ul class="annees list-group">';
        $content .= '<li class="list-group-item">';
        $content .= '<span id="triannees">';
        $content .= '<a class="btn btn-default btn-xs" data-placement="bottom" data-toggle="tooltip" data-original-title="Tri Chronologique" href="" id="trian" onclick="javascript:toggleSort(this, true, \'an\', \'annees\', \'trian\'); return false;" style="font-size:16px;  text-decoration: none;" ><span class="glyphicon glyphicon-sort-by-order"></span></a>';
        $content .= '<a class="pull-right btn btn-default btn-xs"  data-placement="bottom" data-toggle="tooltip" data-original-title="Tri par Nombre d\'Occurences" href="" id="trinban" onclick="javascript:toggleSort(this, false, \'an\', \'annees\', \'trinban\'); return false;" style="font-size:16px;  text-decoration: none;"><span class="glyphicon glyphicon-sort-by-order"></span></a>';
        $content .= '</span>';
        $content .= '</li>';
        $content .= '<div id="an">';

        rsort($json->facet_counts->facet_fields->producedDateY_i);
        $r = 0;
        foreach ($json->facet_counts->facet_fields->producedDateY_i as $res){
            $r = $r+1;
            if ($r > 10){
                break;
            }
            $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=producedDateY_i:' . urlencode($res[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $res[0] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
        }
        $i = 1;
        $content .= '<div id="anneesuite" style="display:none;">';
        foreach ($json->facet_counts->facet_fields->producedDateY_i as $res){
            if($r < 10){
                break;
            }
            if ($i < $r){
                $i = $i+1;
            } else {
                $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=producedDateY_i:' . urlencode($res[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $res[0] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
            }
        }
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</ul>';
        if ($r > 10){
            $content .= '<a class="btn btn-default btn-sm" href="" style="text-decoration: none;" id="annee" onclick="javascript:visibiliteannee(\'anneesuite\'); return false;" >' . __('Liste complète','wp-hal'); $content .= '</a> ';
        }
    }
    $content .= '</div>
        <div class="tab-pane" id="insts">
            <h3>' . __('Institutions','wp-hal').'</h3>';
    if(!is_null($json->facet_counts->facet_fields->instStructIdName_fs)  && !empty($json->facet_counts->facet_fields->instStructIdName_fs)){
        $content .= '<ul class="insts list-group">';
        $content .= '<li class="list-group-item">';
        $content .= '<span id="triinsts">';
        $content .= '<a class="btn btn-default btn-xs" data-placement="bottom" data-toggle="tooltip" data-original-title="Tri Alphabétique" href="" id="triinst" onclick="javascript:toggleSort(this, true, \'institu\', \'instsuite\', \'triinst\');  return false;" style="font-size:16px;  text-decoration: none;" ><span class="glyphicon glyphicon-sort-by-alphabet"></span></a>';
        $content .= '<a class="pull-right btn btn-default btn-xs"  data-placement="bottom" data-toggle="tooltip" data-original-title="Tri par Nombre d\'Occurences" href="" id="trinbinst" onclick="javascript:toggleSort(this, false, \'institu\', \'instsuite\', \'trinbinst\'); return false;" style="font-size:16px;  text-decoration: none;"><span class="glyphicon glyphicon-sort-by-order"></span></a>';
        $content .= '</span>';
        $content .= '</li>';
        $content .= '<div id="institu">';
        $r = 0;
        foreach ($json->facet_counts->facet_fields->instStructIdName_fs as $res){
            $r = $r+1;
            if ($r > 10){
                break;
            }
            $name = explode(delimiter,$res[0]);
            $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=instStructId_i:' . urlencode($name[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $name[1] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
        }
        $i = 1;
        $content .= '<div id="instsuite" style="display:none;">';
        foreach ($json->facet_counts->facet_fields->instStructIdName_fs as $res){
            if($r < 10){
                break;
            }
            if ($i < $r){
                $i = $i+1;
            } else {
                $name = explode(delimiter,$res[0]);
                $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=instStructId_i:' . urlencode($name[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $name[1] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
            }
        }
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</ul>';
        if ($r > 10){
            $content .= '<a class="btn btn-default btn-sm" href="" style="text-decoration: none;" id="inst" onclick="javascript:visibiliteinst(\'instsuite\'); return false;" >' . __('Liste complète','wp-hal'); $content .= '</a> ';
        }
    }
    $content .= '</div>
       <div class="tab-pane" id="labs">
            <h3>' . __('Laboratoires','wp-hal').'</h3>';
    if(!is_null($json->facet_counts->facet_fields->labStructIdName_fs) && !empty($json->facet_counts->facet_fields->labStructIdName_fs)){
        $content .= '<ul class="labs list-group">';
        $content .= '<li class="list-group-item">';
        $content .= '<span id="trilabs">';
        $content .= '<a class="btn btn-default btn-xs" data-placement="bottom" data-toggle="tooltip" data-original-title="Tri Alphabétique" href="" id="trilab" onclick="javascript:toggleSort(this, true, \'labo\', \'labsuite\', \'trilab\');  return false;" style="font-size:16px;  text-decoration: none;" ><span class="glyphicon glyphicon-sort-by-alphabet"></span></a>';
        $content .= '<a class="pull-right btn btn-default btn-xs"  data-placement="bottom" data-toggle="tooltip" data-original-title="Tri par Nombre d\'Occurences" href="" id="trinblab" onclick="javascript:toggleSort(this, false, \'labo\', \'labsuite\', \'trinblab\'); return false;" style="font-size:16px;  text-decoration: none;"><span class="glyphicon glyphicon-sort-by-order"></span></a>';
        $content .= '</span>';
        $content .= '</li>';
        $content .= '<div id="labo">';

        $r = 0;
        foreach ($json->facet_counts->facet_fields->labStructIdName_fs as $res){
            $r = $r+1;
            if ($r > 10){
                break;
            }
            $name = explode(delimiter,$res[0]);
            $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=labStructId_i:' . urlencode($name[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $name[1] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
        }
        $i = 1;
        $content .= '<div id="labsuite" style="display:none;">';
        foreach ($json->facet_counts->facet_fields->labStructIdName_fs as $res){
            if($r < 10){
                break;
            }
            if ($i < $r){
                $i = $i+1;
            } else {
                $name = explode(delimiter,$res[0]);
                $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=labStructId_i:' . urlencode($name[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $name[1] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
            }
        }
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</ul>';
        if ($r > 10){
            $content .= '<a class="btn btn-default btn-sm" href="" style="text-decoration: none;" id="lab" onclick="javascript:visibilitelab(\'labsuite\'); return false;" >' . __('Liste complète','wp-hal'); $content .= '</a> ';
        }
    }
    $content .= '</div>
       <div class="tab-pane" id="depts">
            <h3>' . __('Départements','wp-hal').'</h3>';
    if(!is_null($json->facet_counts->facet_fields->deptStructIdName_fs) && !empty($json->facet_counts->facet_fields->deptStructIdName_fs)){
        $content .= '<ul class="depts list-group">';
        $content .= '<li class="list-group-item">';
        $content .= '<span id="tridept">';
        $content .= '<a class="btn btn-default btn-xs" data-placement="bottom" data-toggle="tooltip" data-original-title="Tri Alphabétique" href="" id="tridept" onclick="javascript:toggleSort(this, true, \'dpt\', \'deptsuite\', \'tridept\');  return false;" style="font-size:16px;  text-decoration: none;" ><span class="glyphicon glyphicon-sort-by-alphabet"></span></a>';
        $content .= '<a class="pull-right btn btn-default btn-xs"  data-placement="bottom" data-toggle="tooltip" data-original-title="Tri par Nombre d\'Occurences" href="" id="trinbdept" onclick="javascript:toggleSort(this, false, \'dpt\', \'deptsuite\', \'trinbdept\'); return false;" style="font-size:16px;  text-decoration: none;"><span class="glyphicon glyphicon-sort-by-order"></span></a>';
        $content .= '</span>';
        $content .= '</li>';
        $content .= '<div id="dpt">';
        $r = 0;
        foreach ($json->facet_counts->facet_fields->deptStructIdName_fs as $res){
            $r = $r+1;
            if ($r > 10){
                break;
            }
            $name = explode(delimiter,$res[0]);
            $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=deptStructId_i:' . urlencode($name[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $name[1] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
        }
        $i = 1;
        $content .= '<div id="deptsuite" style="display:none;">';
        foreach ($json->facet_counts->facet_fields->deptStructIdName_fs as $res){
            if($r < 10){
                break;
            }
            if ($i < $r){
                $i = $i+1;
            } else {
                $name = explode(delimiter,$res[0]);
                $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=deptStructId_i:' . urlencode($name[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $name[1] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
            }
        }
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</ul>';
        if ($r > 10){
            $content .= '<a class="btn btn-default btn-sm" href="" style="text-decoration: none;" id="dept" onclick="javascript:visibilitedept(\'deptsuite\'); return false;" >' . __('Liste complète','wp-hal'); $content .= '</a> ';
        }
    }
    $content .= '</div>
       <div class="tab-pane" id="equipes">
            <h3>' . __('Équipes de recherche','wp-hal').'</h3>';
    if(!is_null($json->facet_counts->facet_fields->rteamStructIdName_fs) && !empty($json->facet_counts->facet_fields->rteamStructIdName_fs)){
        $content .= '<ul class="equipes list-group">';
        $content .= '<li class="list-group-item">';
        $content .= '<span id="triequipe">';
        $content .= '<a class="btn btn-default btn-xs" data-placement="bottom" data-toggle="tooltip" data-original-title="Tri Alphabétique" href="" id="triequipe" onclick="javascript:toggleSort(this, true, \'rteam\', \'equipesuite\', \'triequipe\');  return false;" style="font-size:16px;text-decoration: none;" ><span class="glyphicon glyphicon-sort-by-alphabet"></span></a>';
        $content .= '<a class="pull-right btn btn-default btn-xs"  data-placement="bottom" data-toggle="tooltip" data-original-title="Tri par Nombre d\'Occurences" href="" id="trinbequipe" onclick="javascript:toggleSort(this, false, \'rteam\', \'equipesuite\', \'trinbequipe\'); return false;" style="font-size:16px;  text-decoration: none;"><span class="glyphicon glyphicon-sort-by-order"></span></a>';
        $content .= '</span>';
        $content .= '</li>';
        $content .= '<div id="rteam">';

        $r = 0;
        foreach ($json->facet_counts->facet_fields->rteamStructIdName_fs as $res){
            $r = $r+1;
            if ($r > 10){
                break;
            }
            $name = explode(delimiter,$res[0]);
            $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=rteamStructId_i:' . urlencode($name[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $name[1] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
        }
        $i = 1;
        $content .= '<div id="equipesuite" style="display:none;">';
        foreach ($json->facet_counts->facet_fields->rteamStructIdName_fs as $res){
            if($r < 10){
                break;
            }
            if ($i < $r){
                $i = $i+1;
            } else {
                $content .= '<li class="test list-group-item" data-percentage="'.$res[1].'"><a href="'. halv3 .'?q=rteamStructId_i:' . urlencode($name[0]) . "+AND+" .get_option('option_type').':'.get_option('option_idhal').'" target="_blank">' . $name[1] . '</a><span class="badge badge-default">' .$res[1]. '</span></li>';
            }
        }
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</ul>';
        if ($r > 10){
            $content .= '<a class="btn btn-default btn-sm" href="" style="text-decoration: none;" id="equipe" onclick="javascript:visibiliteequipe(\'equipesuite\'); return false;" >' . __('Liste complète','wp-hal'); $content .= '</a> ';
        }
    }
    $content .= '</div>
    <div class="tab-pane active" id="publications">
         <h3>' . __('Publications','wp-hal');  $content .= '</h3>';

    if (get_option('option_groupe')=='grouper'){

//LISTE DES DOCUMENTS PAR GROUPE

        $content .= '<div class="counter-doc">
' . __('Nombre de documents', 'wp-hal') . '
<h2 class="nbdoc"><span class="label label-primary">' . $json->grouped->docType_s->matches . '</span></h2><br/>
</div>';

        $content .= '<ul style="list-style-type: none;">';
        for($i=0; $json->grouped->docType_s->groups[$i] != null ; $i++){
            foreach ($jsontype as $j => $res){
                if ($json->grouped->docType_s->groups[$i]->groupValue == $j){
                    if (WPLANG == "fr_FR"){
                        $titre = $res->fr;
                    } else {
                        $titre = $res->en;
                    }
                }
            }
            $content .= '<li><div class="doc-group"><h3 class="doc-header">' . $titre . '<small class="doc-nb">' . $json->grouped->docType_s->groups[$i]->doclist->numFound .' ' . _n('document','documents',$json->grouped->docType_s->groups[$i]->doclist->numFound,'wp-hal') .'</small></h3>';
            $content .= '<div class="doc-content">';
            $content .= '<ul>';
            foreach ($json->grouped->docType_s->groups[$i]->doclist->docs as $result){
                $content .= '<li>' . $result->citationFull_s . '</li>';
            }
            $content .= '</ul></div>';
            $content .= '</div></li>';
        }
        $content .=  '</ul>';
    } elseif(get_option('option_groupe')=='paginer'){

//LISTE DES DOCUMENTS AVEC PAGINATION

        $content .= '<div class="counter-doc">
' . __('Nombre de documents', 'wp-hal');
        $content .='<h2 class="nbdoc"><span class="label label-primary">' . $json->response->numFound . '</span></h2><br/>
</div>';

//--MODULE PAGINATION--//
        $messagesParPage = 10;

        $total = $json->response->numFound;

        $nombreDePages = ceil($total / $messagesParPage);

        if (isset($paged)) {
            $pageActuelle = intval($paged);

            if ($pageActuelle > $nombreDePages) {
                $pageActuelle = $nombreDePages;
            }
        } else {
            $pageActuelle = 1;
        }
        $premiereEntree = ($pageActuelle - 1) * $messagesParPage;

        $url = api . '?q=*:*&fq='.get_option('option_type').':('. urlencode(get_option('option_idhal')).')&fl=docid,citationFull_s,keyword_s,bookTitle_s,producedDate_s,authFullName_s&start=' .$premiereEntree . '&rows=' . $messagesParPage . '&sort=' . producedDateY . '&wt=json';
        $ch = curl_init($url);
// Options
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_TIMEOUT => 10,
        );

// Bind des options et de l'objet cURL que l'on va utiliser
        curl_setopt_array($ch, $options);
// Récupération du résultat JSON
        $json = json_decode(curl_exec($ch));
        curl_close($ch);


//--MODULE PAGINATION--//

        $content .= '<ul>';
        for ($i = 0; $json->response->docs[$i]!= ''; $i++){
            $content .= '<li>' . $json->response->docs[$i]->citationFull_s . '</li>';
        }
        $content .=  '</ul>';

//--AFFICHAGE PAGINATION--//
        $precedents=$pageActuelle-2;
        $suivants= $pageActuelle+2;
        $precedent=$pageActuelle-1;
        $suivant= $pageActuelle+1;
        $penultimate = $nombreDePages - 1;

        $content .= '<div class="pagination_module">';
        $content .= '<ul class="pagination">';
//--BOUTON PRECEDENT--//
        if($pageActuelle == 1){
            $content .= '<li class="disabled"><a href="#">&laquo;</a></li>';
        }
        else{
            $content .= '<li><a href="?paged='.$precedent.'">&laquo;</a></li>';
        }
        if($nombreDePages <= 6){// Cas 1 : 6 pages ou moins - Pas de troncature
            for ($i = 1; $i <= $nombreDePages; $i++) {
                if ($i == $pageActuelle) {
                    $content .= '<li class="active"><a href="#">'.$i.'</a></li>';
                }
                else {
                    $content .= ' <li><a href="?paged='.$i.'">' . $i . '</a></li> ';
                }
            }
        }
        elseif($nombreDePages >= 7){// Cas 2 : 7 pages ou plus - Troncature
            if($pageActuelle <= 3){// Lorsque l'on est sur les trois premières pages
                for($i = 1; $i <= 4; $i++){
                    if ($i == $pageActuelle) {
                        $content .= '<li class="active"><a href="#">'.$i.'</a></li>';
                    }
                    else {
                        $content .= '<li><a href="?paged='.$i.'">'.$i.'</a></li>';
                    }
                }
                $content .= '<li class="disabled"><a href="#">&hellip;</a></li>';
                $content .= '<li><a href="?paged='.$penultimate.'">'.$penultimate.'</a></li>';
                $content .= '<li><a href="?paged='.$nombreDePages.'">'.$nombreDePages.'</a></li>';
            }
            if($pageActuelle >=4 && $pageActuelle <= $penultimate-2){//Lorsque l'on arrive sur la quatrième page
                $content .= '<li><a href="?paged=">1</a></li>';
                $content .= '<li class="disabled"><a href="#">&hellip;</a></li>';
                $content .= '<li><a href="?paged='.$precedents.'">'.$precedents.'</a></li>';
                $content .= '<li><a href="?paged='.$precedent.'">'.$precedent.'</a></li>';
                $content .= '<li class="active"><a href="#">'.$pageActuelle.'</a></li>';
                $content .= '<li><a href="?paged='.$suivant.'">'.$suivant.'</a></li>';
                $content .= '<li><a href="?paged='.$suivants.'">'.$suivants.'</a></li>';
                $content .= '<li class="disabled"><a href="#">&hellip;</a></li>';
                $content .= '<li><a href="?paged='.$nombreDePages.'">'.$nombreDePages.'</a></li>';
            }
            if($pageActuelle >= $penultimate-1){
                $content .= '<li><a href="?paged=1">1</a></li>';
                $content .= '<li><a href="?paged=2">2</a></li>';
                $content .= '<li class="disabled"><a href="#">&hellip;</a></li>';
                for($i = $penultimate-2; $i <= $nombreDePages; $i++){
                    if ($i == $pageActuelle) {
                        $content .= '<li class="active"><a href="#">'.$i.'</a></li>';
                    }
                    else {
                        $content .= '<li><a href="?paged='.$i.'">'.$i.'</a></li>';
                    }
                }
            }
        }
//--BOUTON SUIVANT--//
        if($pageActuelle<$nombreDePages){
            $content .= '<li><a href="?paged='.$suivant.'">&raquo;</a></li>';
        }
        else{
            $content .= '<li class="disabled"><a href="#">&raquo;</a></li>';
        }
        $content .= '</ul>';
        $content .= '</div>';
//--AFFICHAGE PAGINATION--//
    }
        $content .='</div>
    </div>
</div>';

    $content .= '<div class="modal-footer">';
    $content .= '<p style="color:#B3B2B0">' . __("Documents récupérés de l'archive ouverte HAL",'wp-hal') . '&nbsp;<a href="' . site . '" target="_blank"><img alt="logo" src=" ' . plugin_dir_url( __FILE__ ) . 'img/logo-hal.svg" style="width:32px;"></a></p>';
    $content .= '</div>';

    return  $content;
}

function wp_adding_style() {
    wp_register_style('wp-hal-style1', plugins_url('/css/bootstrap.css', __FILE__));
    wp_register_style('wp-hal-style2', plugins_url('/css/style.css', __FILE__));
    wp_register_style('wp-hal-style3', plugins_url('/css/jquery.jqplot.css', __FILE__));

    wp_enqueue_style('wp-hal-style1');
    wp_enqueue_style('wp-hal-style2');
    wp_enqueue_style('wp-hal-style3');
}

function wp_adding_script() {
    wp_register_script('wp-hal-script1', plugins_url('/js/bootstrap.js', __FILE__));
    wp_register_script('wp-hal-script2',plugins_url('/js/highcharts.js', __FILE__));
    wp_register_script('wp-hal-script3',plugins_url('/js/jquery.jqplot.js', __FILE__));
    wp_register_script('wp-hal-script4',plugins_url('/js/jqplot.pieRenderer.js', __FILE__));
    wp_register_script('wp-hal-script5',plugins_url('/js/cv-hal.js', __FILE__));

    wp_enqueue_script("jquery");
    wp_enqueue_script('wp-hal-script1');
    wp_enqueue_script('wp-hal-script2');
    wp_enqueue_script('wp-hal-script3');
    wp_enqueue_script('wp-hal-script4');
    wp_enqueue_script('wp-hal-script5');


    $url = api . '?q=*:*&fq='. get_option('option_type').':('. urlencode(get_option('option_idhal')).')&fl=docid,citationFull_s&facet.field='. lang .'domainAllCodeLabel_fs&facet.field=keyword_s&facet.field=journalIdTitle_fs&facet.field=producedDateY_i&facet.field=authIdLastNameFirstName_fs&facet.mincount=1&facet=true&wt=json&json.nl=arrarr';
    $ch = curl_init($url);
    // Options
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Content-type: application/json'),
        CURLOPT_TIMEOUT => 10,
    );

    // Bind des options et de l'objet cURL que l'on va utiliser
    curl_setopt_array($ch, $options);
    // Récupération du résultat JSON
    $json = json_decode(curl_exec($ch));
    curl_close($ch);
    if (WPLANG == 'fr_FR') {
        $facetdomain = $json->facet_counts->facet_fields->fr_domainAllCodeLabel_fs;
    } elseif (WPLANG == 'en_US') {
        $facetdomain = $json->facet_counts->facet_fields->en_domainAllCodeLabel_fs;
    } elseif (WPLANG == 'es_ES') {
        $facetdomain = $json->facet_counts->facet_fields->es_domainAllCodeLabel_fs;
    } else {
        $facetdomain = $json->facet_counts->facet_fields->eu_domainAllCodeLabel_fs;
    }
    if(!is_null($facetdomain)){
    foreach ($facetdomain as $res){
        $name = explode(delimiter,$res[0])[1];
        $value = $res[1];
        $array[] = array($name,$value);
    }
    }
    wp_localize_script('wp-hal-script5', 'WPWallSettings', json_encode($array));
    $translation = array ('liste' => __('Liste', 'wp-hal'), 'compl' => __('Liste complète', 'wp-hal'), 'princ' => __('Liste principale', 'wp-hal'), 'graph' => __('Graphique', 'wp-hal'), 'nuage' => __('Nuage de mots', 'wp-hal'));
    wp_localize_script('wp-hal-script5', 'translate', $translation);
}

/**
 * Récupère les fichiers css et js
 */
add_action( 'wp_enqueue_scripts', 'wp_adding_style' );
add_action( 'wp_enqueue_scripts', 'wp_adding_script' );

/**
 * Charge lorsque le plugin est désactivé
 */
register_deactivation_hook( __FILE__, 'reset_option' );

/**
 * Ajoute le widget wphal à l'initialisation des widgets
 */
add_action('widgets_init','wphal_init');

/**
 * Ajoute le menu à l'initialisation du menu admin
 */
add_action( 'admin_menu', 'wphal_menu' );

/**
 * Fonction de création du menu
 */
function wphal_menu() {
    add_menu_page( 'Options', 'Hal', 'manage_options', 'wp-hal.php', 'wphal_option' , '', 21);

    add_action( 'admin_init', 'register_settings' );
}


/**
 * Initialise le nouveau widget
 */
function wphal_init(){
    register_widget("wphal_widget");
}

/***********************************************************************************************************************
 * PLUGIN WIDGET
 **********************************************************************************************************************/

/**
 * Classe du widget wphal
 */

class wphal_widget extends WP_widget{


    /**
     * Défini les propriétés du widget
     */
    function wphal_widget(){
        $options = array(
            "classname" => "wphal-publications",
            "description" => __("Afficher les dernières publications d'un auteur ou d'une structure.", 'wp-hal')
        );

        $this->WP_widget("hal-publications", __("Publications récentes", 'wp-hal'), $options);
    }

    /**
     * Crée le widget
     * @param $args
     * @param $instance
     */
    function widget($args, $instance){
        $num = 5;
        $url = api .'?q=*:*&fq='.$instance['select'].':' . $instance['idhal'] . '&fl=uri_s,title_s&sort=' . producedDateY . '&rows=' . $num . '&wt=json';

        $ch = curl_init($url);
        // Options
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_TIMEOUT => 10,
        );

        // Bind des options et de l'objet cURL que l'on va utiliser
        curl_setopt_array($ch, $options);
        // Récupération du résultat JSON
        $json = json_decode(curl_exec($ch));
        curl_close($ch);

        $content = '<ul>';
        for ($i = 0; $i < $num; $i++){
            $content .= '<li class="liwidget"><a href="' . $json->response->docs[$i]->uri_s . '" target="_blank">' . $json->response->docs[$i]->title_s[0] . '</a></li>';
        }
        $content .= '</ul>';

        extract($args);
        echo $before_widget;
        echo $before_title.$instance['titre'].$after_title;
        echo $content;
        echo $after_widget;
    }

    /**
     * Sauvegarde des données
     * @param $new
     * @param $old
     */
    function update($new, $old){
        return $new;
    }

    /**
     * Formulaire du widget
     * @param $instance
     */
    function form($instance){

        $defaut = array(
            'titre' => __("Publications récentes", 'wp-hal'),
            'select' => "authIdHal_s"
        );
        $instance = wp_parse_args($instance,$defaut);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id("titre");?>"><?php echo __('Titre :','wp-hal')?></label>
            <input value="<?php echo $instance['titre'];?>" name="<?php echo $this->get_field_name("titre");?>" id="<?php echo $this->get_field_id("titre");?>" class="widefat" type="text"/>
        </p>
        <p>
            <select id="<?php echo $this->get_field_id("select");?>" name="<?php echo $this->get_field_name("select");?>">
                <option id="<?php echo $this->get_field_id("Idhal");?>" value="authIdHal_s" <?php echo ($instance["select"] == "authIdHal_s")?'selected':''; ?>><label for="<?php echo $this->get_field_id("Idhal");?>">Id Hal</label><span style="font-style: italic;"> <?php echo __('(Exemple : laurent-capelli)','wp-hal');?></span></option>
                <option id="<?php echo $this->get_field_id("Structid");?>" value="authStructId_i" <?php echo ($instance["select"] == "authStructId_i")?'selected':''; ?>><label for="<?php echo $this->get_field_id("Structid");?>">Struct Id</label><span style="font-style: italic;"> <?php echo __('(Exemple : 129)','wp-hal');?></span></option>
                <option id="<?php echo $this->get_field_id("Anrproject");?>" value="anrProjectId_i" <?php echo ($instance["select"] == "anrProjectId_i")?'selected':''; ?>><label for="<?php echo $this->get_field_id("Anrproject");?>">anrProject Id</label><span style="font-style: italic;"> <?php echo __('(Exemple : 1646)','wp-hal');?></span></option>
                <option id="<?php echo $this->get_field_id("Europeanproject");?>" value="europeanProjectId_i" <?php echo ($instance["select"] == "europeanProjectId_i")?'selected':''; ?>><label for="<?php echo $this->get_field_id("Europeanproject");?>">europeanProject Id</label><span style="font-style: italic;"> <?php echo __('(Exemple : 17877)','wp-hal');?></span></option>
                <option id="<?php echo $this->get_field_id("Collection");?>" value="collCode_s" <?php echo ($instance["select"] == "collCode_s")?'selected':''; ?>><label for="<?php echo $this->get_field_id("Collection");?>">Collection</label><span style="font-style: italic;"> <?php echo __('(Exemple : TICE2014)','wp-hal');?></span></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id("idhal");?>">Id :</label>
            <input value="<?php echo $instance['idhal'];?>" name="<?php echo $this->get_field_name("idhal");?>" id="<?php echo $this->get_field_id("idhal");?>" class="widefat" type="text"/>
        </p>
        <?php
    }
}


function register_settings() {
    register_setting( 'wphal_option', 'option_choix' );
    register_setting( 'wphal_option', 'option_type' );
    register_setting( 'wphal_option', 'option_groupe' );
    register_setting( 'wphal_option', 'option_idhal' );
    register_setting( 'wphal_option', 'option_lang' );
    register_setting( 'wphal_option', 'option_email' );
    register_setting( 'wphal_option', 'option_tel' );
    register_setting( 'wphal_option', 'option_social0' );
    register_setting( 'wphal_option', 'option_social1' );
    register_setting( 'wphal_option', 'option_social2' );
    register_setting( 'wphal_option', 'option_social3' );
}

/**
 * Crée le menu d'option du plugin
 */
function wphal_option() {

    if (get_option('option_type')==''){
        update_option('option_type', 'authIdHal_s');
    }
    if (get_option('option_groupe')==''){
        update_option('option_groupe', 'paginer');
    }
    ?>
    <div class="wrap">
        <h2><?php echo __('Plugin HAL','wp-hal');?></h2>
        <form method="post" enctype="multipart/form-data" action="options.php">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <?php settings_fields( 'wphal_option' ); ?>
                        <?php do_settings_sections( 'wphal_option' ); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row" style="font-size: 18px;"><?php echo __('Paramètre de la page :','wp-hal');?></th>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php echo __('Type d\'Id','wp-hal');?></th>
                                <td><select name="option_type">
                                        <option id="Idhal" value="authIdHal_s" <?php echo (get_option('option_type') == "authIdHal_s")?'selected':''; ?>><label for="Idhal">Id Hal</label><span style="font-style: italic;"> <?php echo __('(Exemple : laurent-capelli)','wp-hal');?></span></option>
                                        <option id="Structid" value="authStructId_i" <?php echo (get_option('option_type') == "authStructId_i")?'selected':''; ?>><label for="Structid">Struct Id</label><span style="font-style: italic;"> <?php echo __('(Exemple : 129)','wp-hal');?></span></option>
                                        <option id="Anrproject" value="anrProjectId_i" <?php echo (get_option('option_type') == "anrProjectId_i")?'selected':''; ?>><label for="Anrproject">anrProject Id</label><span style="font-style: italic;"> <?php echo __('(Exemple : 1646)','wp-hal');?></span></option>
                                        <option id="Europeanproject" value="europeanProjectId_i" <?php echo (get_option('option_type') == "europeanProjectId_i")?'selected':''; ?>><label for="Europeanproject">europeanProject Id</label><span style="font-style: italic;"> <?php echo __('(Exemple : 17877)','wp-hal');?></span></option>
                                        <option id="Collection" value="collCode_s" <?php echo (get_option('option_type') == "collCode_s")?'selected':''; ?>><label for="Collection">Collection</label><span style="font-style: italic;"> <?php echo __('(Exemple : TICE2014)','wp-hal');?></span></option>
                                    </select>
                                    <input type="text" name="option_idhal" id="option_idhal" value="<?php echo get_option('option_idhal'); ?>"/>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php echo __('Affichage des documents','wp-hal');?></th>
                                <td><input type="radio" name="option_groupe" id="paginer" value="paginer" <?php echo (get_option('option_groupe') == "paginer")?'checked':''; ?>><label for="paginer"><?php echo __('Documents avec pagination','wp-hal');?></label><br>
                                    <input type="radio" name="option_groupe" id="grouper" value="grouper" <?php echo (get_option('option_groupe') == "grouper")?'checked':''; ?>><label for="grouper"><?php echo __('Documents groupés par type','wp-hal');?></label><br>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php echo __('Choix des éléments menu','wp-hal');?></th>
                                <td><input type="checkbox" name="option_choix[1]" id="Contact" value="contact" <?php echo (get_option('option_choix')[1] == "contact")?'checked':''; ?>><label for="Contact"><?php echo __('Contact','wp-hal');?></label><br/>
                                    <input type="checkbox" name="option_choix[2]" id="Disciplines" value="disciplines" <?php echo (get_option('option_choix')[2] == "disciplines")?'checked':''; ?>><label for="Disciplines"><?php echo __('Disciplines','wp-hal');?></label><br/>
                                    <input type="checkbox" name="option_choix[3]" id="Mots-clefs" value="mots-clefs" <?php echo (get_option('option_choix')[3] == "mots-clefs")?'checked':''; ?>><label for="Mots-clefs"><?php echo __('Mots-clefs','wp-hal');?></label><br/>
                                    <input type="checkbox" name="option_choix[4]" id="Auteurs" value="auteurs" <?php echo (get_option('option_choix')[4] == "auteurs")?'checked':''; ?>><label for="Auteurs"><?php echo __('Auteurs','wp-hal');?></label><br/>
                                    <input type="checkbox" name="option_choix[5]" id="Revues" value="revues" <?php echo (get_option('option_choix')[5] == "revues")?'checked':''; ?>><label for="Revues"><?php echo __('Revues','wp-hal');?></label><br/>
                                    <input type="checkbox" name="option_choix[6]" id="Annee" value="annee" <?php echo (get_option('option_choix')[6] == "annee")?'checked':''; ?>><label for="Annee"><?php echo __('Année de production','wp-hal');?></label><br/>
                                    <input type="checkbox" name="option_choix[7]" id="Institution" value="institution" <?php echo (get_option('option_choix')[7] == "institution")?'checked':''; ?>><label for="Institution"><?php echo __('Institutions','wp-hal');?></label><br/>
                                    <input type="checkbox" name="option_choix[8]" id="Laboratoire" value="laboratoire" <?php echo (get_option('option_choix')[8] == "laboratoire")?'checked':''; ?>><label for="Laboratoire"><?php echo __('Laboratoires','wp-hal');?></label><br/>
                                    <input type="checkbox" name="option_choix[9]" id="Departement" value="departement" <?php echo (get_option('option_choix')[9] == "departement")?'checked':''; ?>><label for="Departement"><?php echo __('Départements','wp-hal');?></label><br/>
                                    <input type="checkbox" name="option_choix[10]" id="Equipe" value="equipe" <?php echo (get_option('option_choix')[10] == "equipe")?'checked':''; ?>><label for="Equipe"><?php echo __('Équipes de recherche','wp-hal');?></label>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row" style="font-size: 18px;"><?php echo __('Contact :','wp-hal');?></th>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php echo __('Email','wp-hal');?></th>
                                <td><input type="text" name="option_email" id="option_email" value="<?php echo get_option('option_email'); ?>"/><img alt="email" src="<?php echo plugin_dir_url( __FILE__ )  ?>img/mail.svg" style="vertical-align:middle; width:32px; margin-left:2px; margin-right:2px;"/></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php echo __('Téléphone','wp-hal');?></th>
                                <td><input type="text" name="option_tel" id="option_tel" value="<?php echo get_option('option_tel');?>"/><img alt="phone" src="<?php echo plugin_dir_url( __FILE__ )  ?>img/phone.svg" style="vertical-align:middle; width:32px; margin-left:2px; margin-right:2px;"/></td>
                            </tr>
                            <tr>
                                <th>Facebook</th>
                                <td>http://www.facebook.com/<input type="text" name="option_social0" id="option_social0" value="<?php echo get_option('option_social0'); ?>"/><img alt="facebook" src="<?php echo plugin_dir_url( __FILE__ )  ?>img/facebook.svg" style="vertical-align:middle; width:32px; margin-left:2px; margin-right:2px;"/></td>
                            </tr>
                            <tr>
                                <th>Twitter</th>
                                <td>http://www.twitter.com/<input type="text" name="option_social1" id="option_social1" value="<?php echo get_option('option_social1'); ?>"/><img alt="twitter" src="<?php echo plugin_dir_url( __FILE__ )  ?>img/twitter.svg" style="vertical-align:middle; width:32px; margin-left:2px; margin-right:2px;"/></td>
                            </tr>
                            <tr>
                                <th>Google +</th>
                                <td>https://plus.google.com/u/0/+<input type="text" name="option_social2" id="option_social2" value="<?php echo get_option('option_social2'); ?>"/><img alt="google" src="<?php echo plugin_dir_url( __FILE__ )  ?>img/google-plus.svg" style="vertical-align:middle; width:32px; margin-left:2px; margin-right:2px;"/></td>
                            </tr>
                            <tr>
                                <th>LinkedIn</th>
                                <td>http://sa.linkedin.com/pub/<input type="text" name="option_social3" id="option_social3" value="<?php echo get_option('option_social3'); ?>"/><img alt="linkedin" src="<?php echo plugin_dir_url( __FILE__ )  ?>img/linkedin.svg" style="vertical-align:middle; width:32px; margin-left:2px; margin-right:2px;"/></td>
                            </tr>
                        </table>
                        <?php
                         update_option('option_idhal', str_replace(',',' OR ',get_option('option_idhal')));
                         submit_button(__('Enregistrer','wp-hal'), 'primary large', 'submit', true); ?>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">

                    </div>
                </div>
                <br class="clear"><br/>
            </div>
        </form>

    </div>

<?php
}


/**
 * Reset les données Hal
 */
function reset_option() {
    delete_option('option_choix');
    delete_option('option_type');
    delete_option('option_groupe');
    delete_option('option_idhal');
    delete_option('option_lang');
    delete_option('option_email');
    delete_option('option_tel');
    delete_option('option_social0');
    delete_option('option_social1');
    delete_option('option_social2');
    delete_option('option_social3');
}
