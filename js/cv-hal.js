/**
 * Created by baptiste on 18/04/14.
 */


jQuery(document).ready(function() {
    jQuery('.btn[data-toggle*="tooltip"]').each(function(){
        jQuery(this).tooltip();
    });
});


function displayElem(e)
{
    var targetElement;
    targetElement = document.getElementById(e);
    jQuery('.display').hide();
    targetElement.style.display = "inline";
}

function visibilite(thingId)
{
    var targetElement;
    targetElement = document.getElementById(thingId) ;
    if (targetElement.style.display == "none")
    {
        targetElement.style.display = "inline" ;
    } else {
        targetElement.style.display = "none" ;
    }
    if (document.getElementById('auteur').innerHTML== translate.compl){
        document.getElementById('auteur').innerHTML= translate.princ;
    } else {
        document.getElementById('auteur').innerHTML= translate.compl;
    }
}

function visibiliterevues(idrevues)
{
    var targetElement;
    targetElement = document.getElementById(idrevues) ;
    if (targetElement.style.display == "none")
    {
        targetElement.style.display = "inline" ;
    } else {
        targetElement.style.display = "none" ;
    }
    if (document.getElementById('revue').innerHTML== translate.compl){
        document.getElementById('revue').innerHTML= translate.princ;
    } else {
        document.getElementById('revue').innerHTML= translate.compl;
    }
}

function visibiliteannee(idannee)
{
    var targetElement;
    targetElement = document.getElementById(idannee) ;
    if (targetElement.style.display == "none")
    {
        targetElement.style.display = "inline" ;
    } else {
        targetElement.style.display = "none" ;
    }
    if (document.getElementById('annee').innerHTML== translate.compl){
        document.getElementById('annee').innerHTML=  translate.princ;
    } else {
        document.getElementById('annee').innerHTML=  translate.compl;
    }
}

function visibiliteinst(idinst)
{
    var targetElement;
    targetElement = document.getElementById(idinst) ;
    if (targetElement.style.display == "none")
    {
        targetElement.style.display = "inline" ;
    } else {
        targetElement.style.display = "none" ;
    }
    if (document.getElementById('inst').innerHTML== translate.compl){
        document.getElementById('inst').innerHTML=  translate.princ;
    } else {
        document.getElementById('inst').innerHTML=  translate.compl;
    }
}

function visibilitelab(idlab)
{
    var targetElement;
    targetElement = document.getElementById(idlab) ;
    if (targetElement.style.display == "none")
    {
        targetElement.style.display = "inline" ;
    } else {
        targetElement.style.display = "none" ;
    }
    if (document.getElementById('lab').innerHTML== translate.compl){
        document.getElementById('lab').innerHTML=  translate.princ;
    } else {
        document.getElementById('lab').innerHTML=  translate.compl;
    }
}

function visibilitedept(iddept)
{
    var targetElement;
    targetElement = document.getElementById(iddept) ;
    if (targetElement.style.display == "none")
    {
        targetElement.style.display = "inline" ;
    } else {
        targetElement.style.display = "none" ;
    }
    if (document.getElementById('dept').innerHTML== translate.compl){
        document.getElementById('dept').innerHTML=  translate.princ;
    } else {
        document.getElementById('dept').innerHTML=  translate.compl;
    }
}

function visibiliteequipe(idequipe)
{
    var targetElement;
    targetElement = document.getElementById(idequipe) ;
    if (targetElement.style.display == "none")
    {
        targetElement.style.display = "inline" ;
    } else {
        targetElement.style.display = "none" ;
    }
    if (document.getElementById('equipe').innerHTML== translate.compl){
        document.getElementById('equipe').innerHTML=  translate.princ;
    } else {
        document.getElementById('equipe').innerHTML=  translate.compl;
    }
}

function visibilitedisci(iddisci,idgraph,idtri)
{
    var data = jQuery.parseJSON(WPWallSettings);
    var plot1 = jQuery.jqplot ('piedisci', [data],
        {
            grid: {
                drawBorder: false,
                drawGridlines: false,
                background: '#ffffff',
                shadow:false
            },
            highlighter: {
                show:true,
                formatString: "%s<br><div style='display:none'>%d</div>%s",
                tooltipLocation: 'ne',
                useAxesFormatters: false
            },
            seriesDefaults:{
                shadow : false,
                renderer:jQuery.jqplot.PieRenderer,
                rendererOptions: {
                    startAngle:-90,
                    showDataLabels: true
                }
            }
        }
    );
    var targetElement;
    var tri;
    tri = document.getElementById(idtri) ;
    graphElement = document.getElementById(idgraph) ;
    targetElement = document.getElementById(iddisci) ;
    if (targetElement.style.display == "none")
    {
        graphElement.style.display = "none" ;
        targetElement.style.display = "inline" ;
        tri.style.display = "" ;
    } else {
        targetElement.style.display = "none" ;
        graphElement.style.display = "inline";
        tri.style.display = "none" ;
        plot1.replot();
    }
    if (document.getElementById('disci').innerHTML== translate.graph){
        document.getElementById('disci').innerHTML= translate.liste;
    } else {
        document.getElementById('disci').innerHTML= translate.graph;
    }
}

function visibilitekey(idkey,idsuite,idtri)
{
    var targetElement;
    var keyElement;
    var tri;

    keyElement = document.getElementById(idkey) ;
    tri = document.getElementById(idtri) ;
    targetElement = document.getElementById(idsuite) ;
    if (targetElement.style.display == "none")
    {
        keyElement.style.display = "none" ;
        targetElement.style.display = "";
        tri.style.display = "" ;
    } else {
        targetElement.style.display = "none";
        keyElement.style.display = "";
        tri.style.display = "none" ;
    }
    if (document.getElementById('key').innerHTML== translate.compl){
        document.getElementById('key').innerHTML= translate.nuage;
    } else {
        document.getElementById('key').innerHTML= translate.compl;
    }
}

function toggleSort(e, b, div, divsuite, bouton)
{
    if (b) { //Tri alpha
        if (jQuery(e).attr('data-sort')) {
            triZA(div, divsuite, bouton);
            jQuery(e).removeAttr('data-sort');
        } else {
            triAZ(div, divsuite, bouton);
            jQuery(e).attr('data-sort', true);
        }
    } else { //Tri nb
        if (jQuery(e).attr('data-sort')) {
            triNB(div, divsuite, bouton);
            jQuery(e).removeAttr('data-sort');
        } else {
            triBN(div, divsuite, bouton);
            jQuery(e).attr('data-sort', true);
        }
    }
}

function triAZ(div, divsuite, bouton){
    var jQuerywrapper = jQuery('#'+div);
    var nb = jQuery('#'+divsuite).find('.metadata').length;

    jQuerywrapper.find('.metadata').sort(function (a, b) {
        return jQuery(a).text().toUpperCase().localeCompare(
            jQuery(b).text().toUpperCase());
    })
        .appendTo( jQuerywrapper );

    jQuerywrapper.find('.metadata').slice(jQuerywrapper.find('.metadata').length - nb).appendTo(jQuerywrapper.find('#'+divsuite));
    jQuerywrapper.find('#'+divsuite).appendTo(jQuerywrapper);

}

function triZA(div, divsuite, bouton){
    var jQuerywrapper = jQuery('#'+div);
    var nb = jQuery('#'+divsuite).find('.metadata').length;

    jQuerywrapper.find('.metadata').sort(function (a, b) {
        return jQuery(b).text().toUpperCase().localeCompare(
            jQuery(a).text().toUpperCase());
    })
        .appendTo( jQuerywrapper );

    jQuerywrapper.find('.metadata').slice(jQuerywrapper.find('.metadata').length - nb).appendTo(jQuerywrapper.find('#'+divsuite));
    jQuerywrapper.find('#'+divsuite).appendTo(jQuerywrapper);
}

function triNB(div, divsuite, bouton){
    var jQuerywrapper = jQuery('#'+div);
    var nb = jQuery('#'+divsuite).find('.metadata').length;


    jQuerywrapper.find('.metadata').sort(function (a, b) {
        if (+a.dataset.percentage > +b.dataset.percentage)
            return -1;
        if ( +a.dataset.percentage < +b.dataset.percentage )
            return 1;
        return 0;
    })
        .appendTo( jQuerywrapper );

    jQuerywrapper.find('.metadata').slice(jQuerywrapper.find('.metadata').length - nb).appendTo(jQuerywrapper.find('#'+divsuite));
    jQuerywrapper.find('#'+divsuite).appendTo(jQuerywrapper);

}

function triBN(div, divsuite, bouton){
    var jQuerywrapper = jQuery('#'+div);
    var nb = jQuery('#'+divsuite).find('.metadata').length;

    jQuerywrapper.find('.metadata').sort(function (a, b) {
        if (+a.dataset.percentage > +b.dataset.percentage)
            return 1;
        if ( +a.dataset.percentage < +b.dataset.percentage )
            return -1;
        return 0;
    })
        .appendTo( jQuerywrapper );

    jQuerywrapper.find('.metadata').slice(jQuerywrapper.find('.metadata').length - nb).appendTo(jQuerywrapper.find('#'+divsuite));
    jQuerywrapper.find('#'+divsuite).appendTo(jQuerywrapper);

}

