

### Requirements

The installation currently assumes a generic docker environment in a Linux environment. 

* **Yes**, this works with MacOS. 
 * Note that your command shell must be capable of using associative arrays.
 * `zsh` is fine and is the default with more recent versions of MacOS.
 * `bash` version 3 was the default in earlier versions of MacOS and it is *not* fine.
 * You can upgarde `bash` version 3 to version 4 but you must do this yourself. Apple seems to be unhappy with the 
  open source license of `bash` version 4 and therefore refuses to offer this version of the shell.
 * The set of `.sh` command files in this directory has a `/bin/zsh` in its shebang line and if you have to adpat this if you do not have 
  this shell installed.
* **No**, we will not be supporting Windows or any Microsoft-based environment and this is for a reason. 

### Concepts

Every instance of a dantewiki has a name.

There are several shell-script commands to generate, run, backup and restore a dantewiki.

* **Docker**: Install docker on your machine.
* **Repository**: Obtain a copy of this repository.
* **Customize**: 
 * Copy file `customize-SAMPLE.sh` to `customize-PRIVATE.sh` 
 * Edit `customize-PRIVATE.sh` accordingly
* **Favicon**: Optional customization: Get your own favicon (a default one is in place so you may skip this step)
 * *Why*: A favicon helps to quickly identify your site among a large number of tabs in the browser.
 * *How*: Obtain a favicon from https://favicon.io/favicon-generator/ or use a different tool.
 * *What next*: Place the files `favicon.ico` and `apple-touch-icon.png` into `/dantewiki`, replacing the existing files.
* **Generate Docker Images**:  `generate.sh` 
* **Initialize System**:
