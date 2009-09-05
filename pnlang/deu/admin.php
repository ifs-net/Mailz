<?php

// general
define('_MAILZ_UNREG_READER', 'Gast');
define('_MAILZ_BACKEND', 'Administrationsbackend MAILZ');
define('_MAILZ_ADMIN_MAIN', 'Startseite, Modulkonfiguration');
define('_MAILZ_TARGET_GROUPS', 'Zielgruppen');
define('_MAILZ_NEWSLETTERS', 'Newsletter und Mailings');
define('_MAILZ_PLUGINS', 'Plugins');
define('_MAILZ_ARCHIVE', 'Archiv');
define('_MAILZ_YES', 'ja');
define('_MAILZ_NO', 'nein');
define('_MAILZ_DELETE', 'Daten unwiderruflich löschen');
define('_MAILZ_DELETED', 'Daten gelöscht');
define('_MAILZ_UPDATED', 'Daten aktualisiert');
define('_MAILZ_UPDATE_ERROR', 'Fehler beim Aktualisieren der Daten');
define('_MAILZ_DELETE_ERROR', 'Fehler beim Löschvorgang');
define('_MAILZ_STORE_ERROR', 'Fehler beim Speichern von Daten');
define('_MAILZ_HTML', 'html');
define('_MAILZ_PREVIEW_AND_SEND', 'Vorschau und verschicken');
define('_MAILZ_TEXT', 'nur text');
define('_MAILZ_COMBINED', 'kombiniert: text+html');
define('_MAILZ_INACTIVE', 'inaktiv');
define('_MAILZ_BACK', 'zurück');
define('_MAILZ_ACTIVE', 'aktiv');

// main
define('_MAILZ_MYPROFILE_NAME_FIELD', 'Hier kann ein Feld des MyProfile-Profils ausgewählt werden, welches später im gesamten Newsletter vor Auslieferung durch die Zeichenfolge %%NAME%% ersetzt wird');
define('_MAILZ_BACKEND_SETTINGS', 'Hier können einige globale Einstellungen für alle Newsletter eingestellt werden');
define('_MAILZ_CRON_URL', 'Wenn ein Croncode-Passwort für einen Newsletter eingegeben wurde kann damit der automatisierte Versand eingeleitet werden. Sofort nach Aufruf der URL wird der Newsletter in den Versand eingereiht. Bitte URL (Platzhalter ID und Passwort entsprechend ersetzen) wie folgt verwenden');
define('_MAILZ_REPLACEMENTS', 'Es werden folgende Ersetzungen vorgenommen, wenn ein Newsletter generiert wird. Die entsprechenden Platzhalter können überall bedenkenlos verwendet werden:');

// plugins
define('_MAILZ_PLUGINS_DESCRIPTION', 'Hier können Plugins für Newsletter aktiviert und bearbeitet werden');
define('_MAILZ_LOAD_NL_FIRST', 'Bitte zuerst einen Newsletter auswählen, welcher bearbeitet werden soll!');
define('_MAILZ_ADD', 'hinzufügen');
define('_MAILZ_PLUGIN_MANAGEMENT_FOR', 'Pluginmanagement für');
define('_MAILZ_PLUGIN_USE_TEXT', 'Plugins einfach aus der Liste auswählen und dem Newsletter hinzufügen. Dann Reihenfolge beliebig anpassen und Plugin-Parameter konfigurieren!');
define('_MAILZ_PLUGIN_ADDED', 'Plugin wurde hinzugefügt und kann nun bearbeitet werden!');
define('_MAILZ_PLEASE_DESCRIBE', 'Bitte hier gewünschten Text eingeben');
define('_MAILZ_HEADER_HTML', 'Frei definierbares Textfeld für eigene Inhalte für Kopfteil des Plugins für Inhaltstyp HTML');
define('_MAILZ_FOOTER_HTML', 'Frei definierbares Textfeld für eigene Inhalte für Schlussteil des Plugins für Inhaltstyp HTML');
define('_MAILZ_HEADER_TEXT', 'Frei definierbares Textfeld für eigene Inhalte für Kopfteil des Plugins für Inhaltstyp TEXT');
define('_MAILZ_SHOW_PLUGINLIST', 'Liste aller verfügbaren Plugins aufklappen');
define('_MAILZ_HIDE_PLUGINLIST', 'Liste aller verfügbarer Plugins ausblenden');
define('_MAILZ_FOOTER_TEXT', 'Frei definierbares Textfeld für eigene Inhalte für Schlussteil des Plugins für Inhaltstyp TEXT');
define('_MAILZ_PLUGIN_PARAMS', 'Parameter für das Plugin. Pärchen mit "=" untereinander und "&" zum Nachbarn getrennt, Beispiel: param1,value1;param2,value2');
define('_MAILZ_NO_PLUGINS', 'Noch keine Plugins hinzugefügt');
define('_MAILT_SWITCH_ERROR', 'Beim umsortieren ist ein Fehler aufgetreten');
define('_MAILZ_POSITION', 'Position');
define('_MAILZ_PL_ID', 'Plugin ID');
define('_MAILZ_PLUGIN_DESCRIPTION', 'Plugin-Beschreibung');
define('_MAILZ_MODULENAME', 'Herausgeber-Modul');
define('_MAILZ_PREVIEW', 'Vorschau');
define('_MAILZ_PREVIEW_TEXT', 'text');
define('_MAILZ_PREVIEW_HTML', 'html');

// groups
define('_MAILZ_GROUP_DESCRIPTION', 'Hier können verschiedenste Zielgruppen definiert werden. Eine Zielgruppe kann von mehreren Newslettern verwendet werden.');
define('_MAILZ_GROUP_TITLE', 'Titel der Zielgruppe');
define('_MAILZ_DESCRIPTION', 'Beschreibung (nur intern) der Zielgruppe');
define('_MAILZ_SQL_QUERY', 'SQL-Abfrage für Einspaltiges User-ID-Ergebnis der Zielgruppe');
define('_MAILZ_API_CALL', 'API-Aufruf im Format MODNAME:TYPE:FUNC::param1,value1;param2,value2 mit Rückgabe eines Arrays an User-IDs');
define('_MAILZ_STORE', 'speichern');
define('_MAILZ_SETTINGS_STORED', 'Daten gespeichert');
define('_MAILZ_GROUP_CREATION_ERROR', 'Fehler beim erstellen der Zielgruppe');
define('_MAILZ_NO_GROUPS', 'Keine Gruppen angelegt');
define('_MAILZ_MODIFY', 'editieren');
define('_MAILZ_ACTION', 'Aktionen');
define('_MAILZ_TITLE', 'Titel');
define('_MAILZ_GROUP_ID', 'Gruppen-ID');
define('_MAILZ_QUERY', 'SQL-Query');
define('_MAILZ_API', 'API-Call');
define('_MAILZ_LOADED', 'Daten geladen');
define('_MAILZ_LOAD_ERROR', 'Fehler beim Laden der Daten');
define('_MAILZ_RANGE', 'Reichweite');
define('_MAILZ_SQL_REPLACEMENTS', 'Folgende Ersetzungen werden automatisch beim SQL-Statement durchgeführt');
define('_MAILZ_CUR_YEAR', 'aktuelle Jahreszahl, vierstellig');
define('_MAILZ_CUR_MONTH', 'aktueller Monat, zweistellig');
define('_MAILZ_CUR_DAY', 'aktueller Tag, zweistellig');

// newsletters
define('_MAILZ_NEWSLETTERS_DESCRIPTION', 'Hier können Newslettertypen definiert und konfiguriert werden. Aktivierte Newsletter können sogleich von Benutzern abonniert werden.');
define('_MAILZ_NL_DESCRIPTION', 'Öffentliche Beschreibung');
define('_MAILZ_USE_ARCHIVE', 'Verschickte Newsletter archivieren');
define('_MAILZ_CONTENT_TYPE', 'Inhaltstypen des Newsletters');
define('_MAILZ_IS_SUBSCRIBABLE', 'Newsletter soll abonnierbar sein');
define('_MAILZ_IS_PUBLIC', 'Versand und Abonnement öffentlich (d.h. auch Gäste können diesen abonnieren)');
define('_MAILZ_IS_INACTIVE', 'Eintrag ist aktiv');
define('_MAILZ_NL_CRONCODE', 'Cronjob-Passwort für Nutzung des automatisierten Versands (optional, leer lassen für Nichtnutzung)');
define('_MAILZ_NO_NEWSLETTERS', 'Keine Newsletter bisher angelegt');
define('_MAILZ_NEWSLETTER_CREATION_ERROR', 'Fehler beim Speichern des Newsletters');
define('_MAILZ_DEFINE_GROUPS_FIRST', 'Zuerst müssen Zielgruppen angelegt werden - nur so können Newsletter angelegt und bearbeitet werden!');
define('_MAILZ_MIN_DELAY', 'Verzögerung beim Versenden in Minuten');
define('_MAILZ_ID', 'ID');
define('_MAILZ_NL_DESCRIPTION', 'Beschreibung (wird öffentlich angezeigt) für den Newsletter');
define('_MAILZ_MANAGE_PREVIEW_SEND', 'Vorschau & Senden');
define('_MAILZ_SHOW_CREATE_NL_FORM', 'Formular zum Erstellen neuer Newsletter einblenden');
define('_MAILZ_HIDE_CREATE_NL_FORM', 'Formular zum Erstellen neuer Newsletter ausblenden    ');
define('_MAILZ_SERIAL_NR', 'Durchläufe');
define('_MAILZ_NL_USE_ARCHIVE', 'Archiv');
define('_MAILZ_NL_CONTENT_TYPE', 'Inhalt');
define('_MAILZ_ADD_DATE_TO_SUBJECT', 'Versandatum an Betreffzeile des Newsletters anhängen');
define('_MAILZ_FROMADDRESS', 'Absenderadresse, wenn von Zikula-Standardeinstellung abgewichen werden soll');
define('_MAILZ_PRIORITY', 'Priorität für Mailer-Warteschlange (1=hoch, 10=gering)');
define('_MAILZ_NL_PUBLIC', 'Öffentlich');
define('_MAILZ_ACTIVE', 'aktiv');
define('_MAILZ_GROUPS_SET_ERROR', 'Fehler beim Speichern der Zielgruppenzuordnungen');
define('_MAILZ_IGNORE_MAILER_TEMPLATES', 'Im Zikula-Mailer eingestellte Templates komplett ignorieren');

// send
define('_MAILZ_LOAD_NEWSLETTER_FIRST', 'Bitte den gewünschten Newsletter laden');
define('_MAILZ_SEND_TO_USER', 'Newsletter an einzelnen Benutzer mailen');
define('_MAILZ_SEND', 'senden');
define('_MAILZ_USER_NOT_FOUND', 'Benutzer konnte nicht gefunden werden');
define('_MAILZ_SENDING_HINTS', 'Der Versand findet komplett im Hintergrund statt. Erst nachdem der Versand abgeschlossen ist, wird der Newsletter ins Archiv gesetzt. Während dieser Zeitspanne keinen neuen Versand starten!');
define('_MAILZ_OPEN_PREVIEW', 'Vorschau öffnen');
define('_MAILZ_NL_SENT_TO', 'Newsletter wurde als Testexemplar verschickt an');
define('_MAILZ_SEND_PROCESS', 'Versand durchführen');
define('_MAILZ_SEND_NOW', 'Versand starten');
define('_MAILZ_QUEUED_WAIT', 'Der Newsletter wurde erfolgreich zum Senden eingestellt. Dies kann etwas dauern. Nach dem vollständigen Abschluss des Sendens wird der Newsletter - sofern archivieren für diesen aktiviert ist, automatisch archiviert.');
define('_MAILT_QUEUE_ERROR', 'Ein Fehler ist im Warteschlangensystem aufgetreten, der Newsletter wurde nicht verschickt.');
define('_MAILZ_NEWSLETTER_ALREADY_IN_QUEUE', 'Es befindet sich noch ein nicht vollständig abgearbeiteter Newsletter in der Warteschlange. Bitte diese notfalls manuell bereinigen und dann Empfängerkreis prüfen!');
