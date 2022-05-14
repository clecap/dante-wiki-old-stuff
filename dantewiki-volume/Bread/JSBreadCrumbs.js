var jsbreadcrumbs_controller = ( function () {
	'use strict';

	return {

		initialize: function () {

			var config = mw.config.get( 'JSBreadCrumbs' ),
				pageName = config.PageName,
				action = config.Action,
				maxLength = config.MaxLength,
				siteMaxCrumbs = config.SiteMaxCrumbs,
				globalMaxCrumbs = config.GlobalMaxCrumbs,
				domain = config.Domain,
				showAction = config.ShowAction,
				showSite = config.ShowSite,
				horizontal = config.Horizontal,
				horizontalSeparator = config.HorizontalSeparator,
				leadingDescription = config.LeadingDescription,

				siteName = mw.config.get( 'wgSiteName' ),

				// get the breadcrumbs from the cookie
				breadcrumbs = $.cookie( 'mwext-jsbreadcrumbs' );
			if ( breadcrumbs ) {
				try {breadcrumbs = JSON.parse( breadcrumbs );} catch ( e ) {breadcrumbs = [];}
      } else {breadcrumbs = [];}

			// remove this URL from the breadcrumb list if it is already in it
			var url = location.pathname + location.search,
				index = 0;
			while ( index < breadcrumbs.length ) {
				if ( breadcrumbs[ index ].url === url ) {breadcrumbs.splice( index, 1 );} else {index++;}
			}

			// add the current URL to the breadcrumb list if it points
			// to a valid page
			if ( pageName.substring( pageName.length - 8 ) !== 'Badtitle' ) {
				breadcrumbs.push( {url: url, title: pageName, action: action, siteName: siteName} );
			}

			// get the list of breadcrumbs to display
			var visibleCrumbs = [];
			for ( index = breadcrumbs.length - 1; index >= 0; index-- ) {
				if ( domain || breadcrumbs[ index ].siteName === siteName ) {
					if ( visibleCrumbs.length < siteMaxCrumbs ) {
						var breadcrumb = breadcrumbs[ index ];
						if ( !( 'action' in breadcrumb ) || showAction ||
							breadcrumb.action.length === 0 ) {
							var link = '<a href="' + breadcrumb.url + '">';
							if ( showSite ) {link += breadcrumb.siteName + ': ';}
							var title = breadcrumb.title;
							if ( title.length > maxLength ) {title = title.substr( 0, maxLength ) + '...';}
							if ( 'action' in breadcrumb && breadcrumb.action.length > 0 ) {title += ' [' + breadcrumb.action + ']';}
							link += title + '</a>';
							visibleCrumbs.push( link );
						} else {breadcrumbs.splice( index, 1 );}
					} else {breadcrumbs.splice( index, 1 );}
				}
			}

			// truncate the breadcrumb list if necessary
			if ( breadcrumbs.length > globalMaxCrumbs ) {
				breadcrumbs = breadcrumbs.slice( breadcrumbs.length - globalMaxCrumbs );
			}

			// save the breadcrumbs to the cookie
			$.cookie( 'mwext-jsbreadcrumbs', JSON.stringify( breadcrumbs ),
				{ path: '/', expires: 30 } );

			var skin = mw.config.get( 'skin' ), selector;
			
			
			
			
			var txt = "";
			txt = "";
			for ( index = 0; index < visibleCrumbs.length; index++ ) {
				txt += ' ' + visibleCrumbs[ index ] + ' &raquo; ';
			}
			txt += "";
			$('#breadcrumbinsert').append(txt);
						
			
		}
	};
}() );

window.JSBreadCrumbsController = jsbreadcrumbs_controller;

( function () {
	$( function () {
		if ( mw.config.exists( 'JSBreadCrumbs' ) ) {
			window.JSBreadCrumbsController.initialize();
		}
	} );
}() );
