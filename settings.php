<?php
/**
 * Created by PhpStorm.
 * User: baptiste
 * Date: 26/05/14
 * Time: 10:10
 */

// Constante pour gérer certaines facet
define('delimiter', '_FacetSep_');

// Constante pour l'api utilisé
define('api', 'http://api.archives-ouvertes.fr/search/index/');

// Constante pour le webservice des docType utilisé
define('urltype', 'http://api.archives-ouvertes.fr/ref/docType/wt/json2');

// Constante pour la redirection vers le site halv3 onglet recherche
define('halv3', 'https://hal.archives-ouvertes.fr/search/index/');

// Constante pour la redirection vers le site halv3 onglet accueil
define('site', 'https://hal.archives-ouvertes.fr/');

// Constante pour le tri par date
define('producedDateY', urlencode('producedDateY_i desc'));

// Constante de langue
define('locale', get_locale());