# Youless

Modul für IP-Symcon ab Version 4.3

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

 - IPS 4.3
 - Youless LS120.

## 3. Installation

### a. Installation Youless LS 120

Falls der Youless LS 120 nicht bereits installiert wurde entsprechend dem [Benutzerhandbuch Youless LS120](http://bg-etech.de/download/Youless/youless-benutzerhandbuch-ls120.pdf "Benutzerhandbuch Youless LS120")
 in Betrieb nehmen, damit dieser über die IP Adresse mit einem Brwoser erreicht werden kann.

### b. Laden des Moduls

Die IP-Symcon (min Ver. 4.3) Konsole öffnen. Im Objektbaum unter Kerninstanzen die Instanz __*Modules*__ durch einen doppelten Mausklick öffnen.

In der _Modules_ Instanz rechts oben auf den Button __*Hinzufügen*__ drücken.
 
In dem sich öffnenden Fenster folgende URL hinzufügen:

	
    `https://github.com/Wolbolar/IPSymconYouless`  
    
und mit _OK_ bestätigen.    
        
Anschließend erscheint ein Eintrag für das Modul in der Liste der Instanz _Modules_    


### c. Einrichtung in IPS

In IP-Symcon nun _Instanz hinzufügen_ (_CTRL+1_) auswählen unter der Kategorie, unter der man die Instanz hinzufügen will, und _xxx_ auswählen.


## 4. Funktionsreferenz

### Youless:

Keine gesonderten Funktionen, die daten werden in dem im Modul angegebenen Intervall ausgelsen und in Variablen in IP-Symcon abgespeichert.
	


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

#### Überschrift:

GUID: `{B72E3E8B-1139-5338-53D8-65533F6998F1}` 