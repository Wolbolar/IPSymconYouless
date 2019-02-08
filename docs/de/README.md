# Youless
[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-%3E%205.1-green.svg)](https://www.symcon.de/service/dokumentation/installation/migration-v40-v41/)

Modul für IP-Symcon ab Version 5.1

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)  
2. [Voraussetzungen](#2-voraussetzungen)  
3. [Installation](#3-installation)  
4. [Funktionsreferenz](#4-funktionsreferenz)
5. [Konfiguration](#5-konfiguartion)  
6. [Anhang](#6-anhang)  

## 1. Funktionsumfang

Das Modul liest Daten einen Youless LS120 aus und speichern diese in IP-Symcon. 

### Funktionen:  

 - Auslesen der Daten eines Youless LS120 
	  

## 2. Voraussetzungen

 - IPS 5.1
 - Youless LS120.

## 3. Installation

### a. Installation Youless LS 120

Falls der Youless LS 120 nicht bereits installiert wurde entsprechend dem [Benutzerhandbuch Youless LS120](http://bg-etech.de/download/Youless/youless-benutzerhandbuch-ls120.pdf "Benutzerhandbuch Youless LS120")
in Betrieb nehmen, damit dieser über die IP Adresse mit einem Browser erreicht werden kann.

### b. Laden des Moduls

Die Webconsole von IP-Symcon mit _http://<IP-Symcon IP>:3777/console/_ öffnen. 


Anschließend oben rechts auf das Symbol für den Modulstore (IP-Symcon > 5.1) klicken

![Store](img/store_icon.png?raw=true "open store")

Im Suchfeld nun

```
Youless
```  

eingeben

![Store](img/module_store_search.png?raw=true "module search")

und schließend das Modul auswählen und auf _Installieren_

![Store](img/install.png?raw=true "install")

drücken.


#### Alternatives Installieren über Modules Instanz (IP-Symcon < 5.1)

Die Webconsole von IP-Symcon mit _http://<IP-Symcon IP>:3777/console/_ öffnen. 

Anschließend den Objektbaum _Öffnen_.

![Objektbaum](img/objektbaum.png?raw=true "Objektbaum")	

Die Instanz _'Modules'_ unterhalb von Kerninstanzen im Objektbaum von IP-Symcon (>=Ver. 5.x) mit einem Doppelklick öffnen und das  _Plus_ Zeichen drücken.

![Modules](img/Modules.png?raw=true "Modules")	

![Plus](img/plus.png?raw=true "Plus")	

![ModulURL](img/add_module.png?raw=true "Add Module")
 
Im Feld die folgende URL eintragen und mit _OK_ bestätigen:

```
https://github.com/Wolbolar/IPSymconYouless
```  
	        
Anschließend erscheint ein Eintrag für das Modul in der Liste der Instanz _Modules_    

Es wird im Standard der Zweig (Branch) _master_ geladen, dieser enthält aktuelle Änderungen und Anpassungen.
Nur der Zweig _master_ wird aktuell gehalten.

![Master](img/master.png?raw=true "master") 

Sollte eine ältere Version von IP-Symcon die kleiner ist als Version 5.x (min 4.3) eingesetzt werden, ist auf das Zahnrad rechts in der Liste zu klicken.
Es öffnet sich ein weiteres Fenster,

![SelectBranch](img/select_branch.png?raw=true "select branch") 

hier kann man auf einen anderen Zweig wechseln, für ältere Versionen kleiner als 4.1 ist hier
_Old-Version_ auszuwählen. 

### c. Einrichtung in IPS

In IP-Symcon nun _Instanz hinzufügen_ (_Rechtsklick -> Objekt hinzufügen -> Instanz_) auswählen unter der Kategorie, unter der man Youless hinzufügen will,
und _Youless_ auswählen.


## 4. Funktionsreferenz

### Youless:

Keine gesonderten Funktionen, die Daten werden in dem im Modul angegebenen Intervall ausgelesen und in Variablen in IP-Symcon abgespeichert.
	
## 5. Konfiguration:

### Youless:

| Eigenschaft   | Typ     | Standardwert | Funktion                                  |
| :-----------: | :-----: | :----------: | :---------------------------------------: |
| Host          | string  |              | IP Adresse Youless                        |
| UpdateInterval| integer |    15        | Update Interval                           |






## 6. Anhang

###  a. Funktionen:

#### Youless:

`Youless_Update(integer $InstanceID)`

Aktuelle Daten des Youless auslesen 


###  b. GUIDs und Datenaustausch:

#### Youless:

GUID: `{B72E3E8B-1139-5338-53D8-65533F6998F1}` 