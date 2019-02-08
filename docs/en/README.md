# Youless
[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-%3E%205.1-green.svg)](https://www.symcon.de/service/dokumentation/installation/migration-v40-v41/)

Module for IP-Symcon from version 5.1. Reads data from Youless.

## Documentation

**Table of Contents**

1. [Features](#1-features)
2. [Requirements](#2-requirements)
3. [Installation](#3-installation)
4. [Function reference](#4-functionreference)
5. [Configuration](#5-configuration)
6. [Annex](#6-annex)

## 1. Features

The module reads data from a Youless LS120 and saves the values in IP Symcon.
 
## 2. Requirements

 - IP-Symcon 5.x
 - Youless LS120.

## 3. Installation

### a. Installation Youless LS 120

If the Youless LS 120 has not already been installed according to the [user manual Youless LS120](http://bg-etech.de/download/Youless/youless-benutzerhandbuch-ls120.pdf "Benutzerhandbuch Youless LS120")
put it into operation so that it can be reached via the IP address with a Browser.

### b. Loading the module

Open the IP Console's web console with _http://<IP-Symcon IP>:3777/console/_.

Then click on the module store icon (IP-Symcon > 5.1) in the upper right corner.

![Store](img/store_icon.png?raw=true "open store")

In the search field type

```
Youless
```  


![Store](img/module_store_search_en.png?raw=true "module search")

Then select the module and click _Install_

![Store](img/install_en.png?raw=true "install")


#### Install alternative via Modules instance (IP-Symcon < 5.1)

Open the IP Console's web console with _http://<IP-Symcon IP>:3777/console/_.

_Open_ the object tree .

![Objektbaum](img/object_tree.png?raw=true "Objektbaum")	

Open the instance _'Modules'_ below core instances in the object tree of IP-Symcon (>= Ver 5.x) with a double-click and press the _Plus_ button.

![Modules](img/modules.png?raw=true "Modules")	

![Plus](img/plus.png?raw=true "Plus")	

![ModulURL](img/add_module.png?raw=true "Add Module")
 
Enter the following URL in the field and confirm with _OK_:

```
https://github.com/Wolbolar/IPSymconYouless
```  
	     
and confirm with _OK_.    
    
Then an entry for the module appears in the list of the instance _Modules_

By default, the branch _master_ is loaded, which contains current changes and adjustments.
Only the _master_ branch is kept current.

![Master](img/master.png?raw=true "master") 

If an older version of IP-Symcon smaller than version 5.x (min 4.3) is used, click on the gear on the right side of the list.
It opens another window,

![SelectBranch](img/select_branch_en.png?raw=true "select branch") 

here you can switch to another branch, for older versions smaller than 4.1 is here
_Old-Version_ to select.

### b. Configuration in IP-Symcon

In IP-Symcon add _Instance_ (_rightclick -> add object -> instance_) under the category under which you want to add the Youless,
and select _Youless_.
	 
## 4. Function reference

### Youless:

No separate functions, the data are read out in the interval specified in the module and stored in variables in IP-Symcon.


## 5. Configuration:

### Youless:

| Property       | Type    | Value        | Description                               |
| :------------: | :-----: | :----------: | :---------------------------------------: |
| Host           | string  |              | IP Adress Youless                         |
| UpdateInterval | string  |              | IP Adress Doorbird                        |


## 6. Annnex

###  a. Functions:

#### Youless:

`Youless_Update(integer $InstanceID)`

Read current data of the Youless


###  b. GUIDs and data exchange:

#### Youless:

GUID: `{B72E3E8B-1139-5338-53D8-65533F6998F1}` 