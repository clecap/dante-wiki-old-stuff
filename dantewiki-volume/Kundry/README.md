## What is Kundry?

Kundry is a service for finding, storing and retrieving reference material in a mediawiki installation and streamlining
the workflow in mediawiki.
Developed originally for the Dantewiki project, it assume that it runs inside of a Dantewiki variant of mediawiki,
however, some aspects can also be useful as an extension to any newer mediawiki installation.



## Functions provided by Kundry

* Drag and drop of files 
* Modification of the pipe syntax for providing special new link features 


























## Standard Case: Open Document

Alice has a document open in any browser window. She drags the URL onto dantepedia. 

1) It is uploaded. 
2) 

















## Use Cases

This section describes the use cases Kundry currently supports or has on the roadmap for supporting.

### QuickUpload of Files

Alice wants to upload a file to her mediawiki. Since the goal of her wiki is that of a personal notebook and not of a world wide encylcopedia, she wants
a faster way to upload files to her mediawiki than the one offered by the classical mediawiki upload mechanism. She uses the Special:QuickUpload page
of the Kundry extension. There she finds three different drag-and-drop areas which she can use for her various upload tasks. 

### QuickUpload of Exotic Formats

As a researcher, Alice not only has PDF files of documents but also djvu, epub, md, which she wants to store independently of the specifics of the various formats. 
She wants to store them for future reference without having to convert them to PDf first, to find format converters, install them, update them and do all the nasty
maintenance work. Again, Kundry comes to her service.

### QuickUpload of Web Resources 

Sometimes Alice finds a web page she wants to archive for her research work. She finds it too cumbersome to have to download and possibly convert the resource first. 
She just wants to drag the URL to her mediawiki. 

### Anyplace QuickUpload 

Bob really has to do a lot of researching, intertwined with his own documentation tasks. Why bother to click on those SpecialPage links. Could he not just 
drag-and-drop, and view at the same time, any resource on all the mediawiki pages? So he turns on a special feature of Kundry to have the features 
available on every page or to have a magic word with the help of which he can configure selected of his own wiki pages to allow for such a functionality.


### Store Metadata information on files

Given her different use case, Alice wants different forms of metadata connected to her uploaded files. In particular, she wants to have 
BibTeX / LaTeX / Refer type of bibliographic information attached to her files. 



### Find a Reference



### Peek into a reference

Alice reads a document. The document contains a reference which shows specific markup. Alice hovers the mouse over the markup.
A small overlay frame appears over the markup which displays the content of the cited reference.


### Read a reference

Alice reads a document. The document contains a reference which shows specific markup. Alice clicks on the markup.
A popup window opens which displays the content of the cited reference together with some meta data.

### Citing a reference

* From the web via a publicly accessible URL
* From the web via a publicly accessible URL but with content which locally is cached such that the content does not suffer link-rot in the web
* Using a path to local filesystem
* Using a hash to find a file on local filesystem
* Using a unique name to find a file on local filesystem  



### Citing a reference from local directories

Can 


### Citing a reference from a Kundry server


### Provide a reference

Alice 


### Seek a reference


### BYOL Bring Your Own Literature

The author refers to something by DoI or by SHA1 or by a collection of metadata sufficiently precise.

The reader brings her own literature, eg on a USB stick, and makes it available to 

 

### Link Modification

Sometimes a specific opening behavior or hint window or link styling helps in the workflow.

`[https://youtu.be/kl7oUK3sMQo?t=4019 Sei mir gegrüßt ¦ title=Ausschnitt aus Lohengrin ¦ target=_blank]`
 
#### Add a specific hint window

Sometimes we want to add a specific hint window to an anchor. This is especially helpful for external links and for 
links in the sidebar. 

#### Force opening in new tab or new window.

The browser user is aware of the generic behavior of the browser: We can open links in the active window with clicking and
in a new tab or window on the context menu. The page author can (partially) override this behavior using the target attribute.
However, this possibility is not normally available in mediawiki, where all links open in the active window. This (mostly) makes 
sense for an encyclopedia but not always for a dantepedia. Some links should generically no open in the current tab but in a 
new tab. For example: Cheat sheets and other information which the user will always want to view in addition to the current 
context without losing the current context.


To open a link in a new tab we add a  `¦ target=_blank`

To open a link in a new window we add a  `¦ target=_popup`


To inform the use in advance, Kundry provides a different markup (double underline) for such links.

The popup feature, of course, only works when enabled by the browser user.


## Technical Use Cases


* Add a document the user has as file to Dante.
* Add a document which exists somewhere in the web to Dante.
  * Document exists as URL to a PDF document
  * Document exists in a Dantewiki 
  * Document exists in a Mediawiki 
  * Document is open in a local browser window of the user (and there is a PDF or HTML or a Mediawiki)    

## Architectural Components

The content provider is the mechanism which ultimately provides the content:
* From the web  
* From a content server 
* From the local file system (just anywhere)
* From a sandboxed area in the local file system
* From a USB stick
  
  
The content broker gets a content identification and returns a content location
      
              
* Path in local filesystem
* Hash

Content consumer: 



Content identification:  
* URL  
* 

## Architecture

Dragging a file onto the Kundry drop area
* opens a popup window displaying the document and offering meta-data content about the document
* 
* adds the hash into open edit field
* uploads the document to the Kundry database (option 1)
* 


The Kundry server 


## Kundry Link Convention

The Kundry user interface convention offers two forms of provisioning new content:
* **Navigation:** Interaction with the link leads away from the current attention context to a new attention context. We may think of at least 6 forms of implementing navigation:
 * *Replace:* The browser replaces the content which is displayed in the current context.
 * *Repositioning:* 
 * *New tab with focus:* The browser opens a fresh context in a new tab on focuses on this tab.
 * *New tab without focus:* The browser opens a fresh context in a new tab but does not switch focus.
 * *New window with focus:* The browser opens a fresh window and focuses on this window.
 * *New window without focus:* The browser opens a fresh window but does not focus on this window.

* **Modification:** Interaction with the links lead to a modification (adding or removing) of content to the existing content. We may think of at least
 * *Overlay:*  The overlay is a separate content which is not part of the main content flow and thus can cover ("overlay") existing content. 
    Usually it can be moved and closed. Implementation forms are hint windows, tooltips or out-of-flow frames.
 * *Collapsible:* The collapsible is a content which remains part of the main content flow but can be displayed as stub (collapsed content) or as
    fully opened content.       

While the difference between modification and navigation is somewhat arbitrary, navigation moves away to a fresh context (although the old context still remains accessible in principle), modification only modifies the context and has a more conservative approach to existing context.                    

The form of interaction may consist of
* Clicking
* Hovering
* Pressing


Ideally, the following principles of UI design are employed:
* User Choice: When there are different modes for provisioning new content, the user can chose among them.  
* Meaningful default: If the user does not want to make her own choice, a meaningful default is in place. 
* Adaptible default: The defaults may be selected by a user preference.
* Transparency or no-surprise: The action of the system is properly communicated to the user before the user effects an action. 


## Kundry Endpoints

* 

## References:

We use pdf.min.js and pdf.worker.min.js from https://cdn.jsdelivr.net/npm/pdfjs-dist@2.10.377/build/pdf.worker.min.js


## Command Line Tools 



## Known Bugs and Limitations

### Save a web page

I currently do not know how to save a web page directly to Kundry in a convenient and quick way without the detour via the PDF print/save dialogue
followed by a manual re-upload. The problem is:

* We cannot just use a headless chrome to do the conversion, since we often get a cookie consent covering the content.

Currently the recommended practice is to do a print-to-PDF of the page and then drag-and-drop the resulting file on to QuickUpload again.



## Why this name?

Amfortas: Du, Kundry?
Muss ich Dir nochmals danken,
Du rastlos scheue Magd?

Gurnemanz:
Das wird Dich wenig mühn;
auf Botschaft sendet sich's nicht mehr;
Kräuter und Wurzeln
findet ein jeder sich selbst,
wir lernten's im Walde vom Tier.
