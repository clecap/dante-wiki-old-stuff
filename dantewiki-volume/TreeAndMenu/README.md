# TreeAndMenuNG

TreeAndMenuNG is an extension offering tree and menu functionalities, especially for usage in sidebar and UI aspects, adapted to needs of the dantewiki project.

## Additional Features

* Display the specific dantewiki name as part of the personal portlet.
* Provide a category tree together with a tree-based editor.
* Category links in the category tree open in a seperate window on the left of the screen.


## Special Sidebar Areas

The structure of the sidebar is described by MediaWiki:Sidebar.
Three special keywords may be used:
* startus
* catus
* socialus

### Startus

Startus is a general tree-like area and can provide further tree-like structure for the site.

The tree-like structure is modified by editing MediaWiki:SidebarTree.

### Catus

Catus provides a category tree, which displays ...........all cats which are ........................

* Click on a category link opens a category inspector, i.e. a seperate page describing the category and allowing some additional navigation, preferably in the category namespace, while editing the page.
* The checkboxes can be clicked while viewing a page, which leads to a recategorization of the page.
* The tree shows the categoriey applied to the current page
* The tree opens automagically whenever categories of a displayed page are deeper down in the category structure.
 

### Socialus  






## TODO Patches:

* Hilite a category in the tree if below that category there is a marking, hidden inside of the collapsed tree.


## Category Editor Functionality ##

We have a category tree showing sub categories of category root
  TODO: make more flexible, not necessariy root bur rather "sidebar"
The categories of a page are ticked.

When viewing pages: Changing the checkboxes is disabled.

When editing a page: Changing them is enabled
When saving a page:  We have to check which of them are clicked
  PROBLEM: How do we solve a discrepancy between 1) text in the wiki page using category links and 2) settings in the tree?
  PROBLEM: How do we ensure that category links are at the end of a wiki page text and not somewhere in the middle?




## Patches and Changes Done

* Adapt to the new mediawiki extension loading mechanism.
* Streamlined and compressed the code layout in some places.
* Removed unnecessary animation, which only serves as eye candy and does not convey essential UI information.
* Combined several extension functions into one.
* Reduced flicker while loading.
* Some resource bundles were renamed in MW 1.35 and thus not found. Adjusted them in extension.json ext.fanccytree




## Features ##

### Link Modification Features ###

A link which is descendant of a <span> with class
* "removeTargetClass" has its target removed
* "blankTargetClass"  opens in target _blank

* "showReferrer" is explicitely allowed to show referrers

This allows us to define the opening mode in the sidebar tree menus.

Links with target _blank are dotted red, not underlined.


### Main / Side hide feature ###
If we add to the URL a specode=side to the URL then we only get the sidebar
If we add specode=main then we only get the main contents

## Why a multi functional extension ? ##

A multi functional extension is counter the design principle of separation of concerns. However,
* a larger number of independent extensions is not practical since they are not on a common maintenance standard and with
a change in an interface of the core functions all would have to be maintained individually


## Installation

* The extension must not be placed into directory `./extensions`  but into the sibling directory `./myExtensions`. 
* The extension must not be renamed.
* The specific restrictions are a consequence of the way the mediawiki ressource loader is written and documented, neither of which I am responsible for.


## History

TreeAndMenuNG is a new generation version of the TreeAndMenu mediawiki extension available at https://www.mediawiki.org/wiki/Extension:TreeAndMenu and https://gitlab.com/organicdesign/TreeAndMenu/
Years ago I started to hack the original extension a bit without properly documenting my changes. Then somehow the original was updated and my branch made it difficult for me to keep up with it. 


## Roadmap and Todos

* The load of the dynamic elements of the sidebar is getting quite heavy. Thus, we would like to consider a cache architecture.
* The sidebar currently is generated in many aspects on the fly and in the client. 1) The categories of a page are marked in the client via JS. 2) The sidebar itself is built in the client in JS. 3) The tree opening is persisted in the client (which is ok)
  but this is implemented in the client. To prevent to much flickering, we switch the sidebar to hidden, build the sidebar in the client and then show it. As a result, the sidebar is faulted in, after the main page already is displayed. This is unfortunate.
  We might eventually move on to a better sidebar architecture, reducing the load, doing more caching in the sidebar and displaying it at the same time as the main page is displayed.
















