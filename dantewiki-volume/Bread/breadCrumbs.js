(() => {  // scope protection

  const globalMaxCrumbs = 20;
  const maxLength =   50;  
  const siteMaxCrumbs = 8; 

  window.doBreadNow = function () {    
    // get current breadcrumbs from localStorage (allows cross window breadcrumbs)
    var breadcrumbs = localStorage.getItem ("breadcrumbs");      
    if ( breadcrumbs ) { try {breadcrumbs = JSON.parse( breadcrumbs );} catch ( e ) { breadcrumbs = [];} } else {breadcrumbs = [];}

    // remove this URL from the breadcrumbs if it is already in it
    var url = location.pathname; // + location.search;
    var index = 0;
    while ( index < breadcrumbs.length ) {
      if ( breadcrumbs[ index ].url === url ) {breadcrumbs.splice( index, 1 );} else {index++;}
    }

    // format breadcrumbs for display
    var visibleCrumbs = [];
    for ( index = breadcrumbs.length - 1; index >= 0; index-- ) {  // step backwards through the crumbs
        if ( visibleCrumbs.length < siteMaxCrumbs ) {
          var breadcrumb = breadcrumbs[ index ];
          var link = '<a href="' + breadcrumb.url + '">';
          var title = breadcrumb.title;
          if ( title.length > maxLength ) {title = title.substr( 0, maxLength ) + '...';}
          link += title + '</a>';
          visibleCrumbs.push( link );
        } else {breadcrumbs.splice( index, 1 );}
      
    }

    // truncate breadcrumbs to maximal length
    if ( breadcrumbs.length > globalMaxCrumbs ) { breadcrumbs = breadcrumbs.slice( breadcrumbs.length - globalMaxCrumbs ); }
     
    localStorage.setItem ("breadcrumbs", JSON.stringify(breadcrumbs));   // save rbeadcrumbs
    
    var txt = "";
    for ( index = 0; index < visibleCrumbs.length; index++ ) {txt += ' ' + visibleCrumbs[ index ] + ' &raquo; ';}
    txt += "<a href='javascript:window.clearBreadcrumbs();' class='oo-ui-panelLayout-framed'   style='font-size:7pt; padding:1pt;' title='Clear breadcrumbs'>del</a>";
    document.getElementById ("breadcrumbinsert").innerHTML = txt;
    return txt;
    
  }


  window.addFreshCrumb = function (pageName) {  
    // get current breadcrumbs from localStorage (which allows cross window breadcrumbs)
    var breadcrumbs = localStorage.getItem ("breadcrumbs");      
    if ( breadcrumbs ) { try {breadcrumbs = JSON.parse( breadcrumbs );} catch ( e ) { breadcrumbs = [];} } else {breadcrumbs = [];}

    // remove this URL from the breadcrumb list if it is already in it
    var url = location.pathname; // + location.search;
    
    var index = 0;
    while ( index < breadcrumbs.length ) {
      if ( breadcrumbs[ index ].url === url ) {breadcrumbs.splice( index, 1 );} else {index++;}
    }

    // add the current URL to the breadcrumbs if it points to a valid page
    if ( !url.endsWith ("index.php") && pageName.substring( pageName.length - 8 ) !== 'Badtitle' ) {
      breadcrumbs.push( {url: url, title: pageName} );
    }
    
    // truncate breadcrumbs to maximal length
    if ( breadcrumbs.length > globalMaxCrumbs ) { breadcrumbs = breadcrumbs.slice( breadcrumbs.length - globalMaxCrumbs ); }
     
    localStorage.setItem ("breadcrumbs", JSON.stringify(breadcrumbs));   // save rbeadcrumbs
  }  
})();





















