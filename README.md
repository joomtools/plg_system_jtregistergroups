# JT -RegisterGroups

#### Joomla - System-Plugin ([Download](https://github.com/JoomTools/plg_system_jtregistergroups/releases))
[![Joomla 5 - native](https://img.shields.io/badge/Joomla™-5.x_native-darkgreen?logo=joomla&logoColor=c2c9d6&style=for-the-badge)](https://downloads.joomla.org/cms) ![PHP8.1](https://img.shields.io/badge/PHP->=8.1-darkgreen?logo=php&style=for-the-badge)

### Beschreibung / Description
<details>
  <summary>Deutsch/German</summary>

## Deutsche Beschreibung

### Plugin
Dieses Plugin ermöglicht es mehrere Registrierungsseiten in Joomla als Menüpunkte anzulegen und dort eine Benutzergruppe festzulegen, die der Besucher nach der Registrierung haben wird. Beispiel: Registrierung als Käufer / Registrierung als Verkäufer


### Achtung
Wenn die Freischaltung der Registrierung durch E-Mail aktiviert ist, wird empfohlen nur Gruppen zu verwenden mit niedriger Berechtigungsstufe. Sicherer ist die Freischaltung durch einen Administrator.

### Plugin-Einstellungen
In den Plugin-Einstellungen kann man definieren, welche Gruppen später in den Menüpunkten für eine Registrierung eingestellt werden können. Es ist nicht nötig die Gruppe auszuwählen, welche schon in den globalen Einstellungen angegeben wurde.


### Menüpunkt-Einstellungen
Der Menüpunkt muss vom Typ `Benutzer -> Registrierungsformular` sein, damit der Tab `JT - RegisterGroups` auswählbar ist. Hier kann man eine Gruppe auswählen, welche vorher in den Plugin-Einstellungen definiert wurde. Jedem Menüpunkt kann nur eine Gruppe zugewiesen werden.  
Der neu registrierte Benutzer wird automatisch der vorher hinterlegten Gruppe zugewiesen, dashalb bitte den Warnhinweis unter [Achtung](#achtung) beachten.


### CustomFields-Einstellungen
In der Benutzerverwaltung gibt es die Möglichkeit zusätzliche Felder zu erstellen, welche dann bei der _Registrierung / Bearbeitung des Profils_ zur Verfügung stehen. Hier kann man nun zusätzlich unter dem Tab `Optionen` die Benutzergruppen einstellen, unter denen das Zusatzfeld angezeigt werden soll (auch im Registrierungsformular). Bleibt das Feld leer, wird es immer angezeigt.

### Danksagung
Ein besonderer Dank geht an die fleissigen Tester.  
Danke für das Feedback [_Barbara Aßmann_](https://github.com/webnet-assmann), [_Claudia Oerter_](https://github.com/coweb01), [_Elisa Foltyn_](https://github.com/coolcat-creations), [_Toni Gerns_](https://github.com/d4shoerncheN), [_Viviana Menzel_](https://github.com/drmenzelit) :+1:

</details>

<details>
  <summary>Englisch/English</summary>

## English description

### Plugin
This Plugin makes it possible to have several registration pages in Joomla and to create menu items where a specific user group is set. Example: Customer / Vendor Registration page


### Attention
If the activation of the registration is set by e-mail, it is recommended to use only groups with a low authorisation level. It's more secure is the activation by an administrator.


### Plugin settings
In the plugin settings you can define which groups can be set later in the menu items for a registration. It is not necessary to select the group that has already been specified in the global settings.


### Menu item settings
The menu item must be of the type `Users -> Registration Form`, so that the tab `JT - RegisterGroups` can be selected. Here you can select one of the groups, that was previously defined in the plugin settings. Each menu item can only be assigned to one group.
The newly registered user is automatically assigned to the selected group, therefore please note the warning under [Attention](#attention).


### CustomFields settings
In the user administration there is the possibility to create additional fields (custom fields), that will be available in _registration / editing of the profile_. Under the tab `Options` of a field, You can now set the user groups under which the additional field should be displayed (also in the registration form). If the field remains empty, it is always displayed.


### Special thanks
Special thanks goes to the diligent testers.  
Thanks for the feedback [_Barbara Aßmann_](https://github.com/webnet-assmann), [_Claudia Oerter_](https://github.com/coweb01), [_Elisa Foltyn_](https://github.com/coolcat-creations), [_Toni Gerns_](https://github.com/d4shoerncheN), [_Viviana Menzel_](https://github.com/drmenzelit) :+1:
</details>
