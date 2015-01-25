
Om det här dokumentet
=====================

Detta är ett långt och ambivalent dokument. Jag har helt enkelt sammanställt all
dokumentation jag skrivit under projektets gång. Vissa delar riktar sig till
programmerare som vill förstå och kanske arbeta med systemet. Andra delar riktar
sig till styrgrupp och beställare. Ytterligare andra delar riktar sig
till användare av systemet. Läs det som intresserar er.


Filsystemet
===========

Projektet är organiserat i följande filsystem:

## /

I systemets rot finns

* _build.xml_: se **Tester och kodanalys**
* _composer.json_: se **Serverkonfiguration**
* _php.ini_: de PHP inställningar systemet behöver. Se **Serverkonfiguration**
* _mreg.local_: den apache virtual host som använts under utvecklingen.

## bin

Kompilerade binärer.

* _wkhtmltopdf_ används för att generera pdf från html.

## build

Genererad metadata, dokumentation mm. Se **Tester och kodanalys**.

## cli

Cli-skript skript systemet använder sig av. Körs som cron-jobb.

## db

Databasen.

* _DB.sql_ En tillfällig kopia av databasen till versionshantering. När systemet
  går i drift ska denna såklart inte användas längre.

* _EMPTY.sql_ Den tomma databasen, vilken såklart inte är en helt tom, utan
  innehåller en hel del grundläggande data. Använd denna för att sätta upp
  ett nytt lokalt system.

* _events.sql_ De MySQL-events systemet använder sig av (cron-jobb som körs
  internt i databasen. Finns laddade i _EMPTY.sql_.

* _triggers.py_ Python-skript som skapar de MySQL-triggers systemet använder sig
  av. Finns laddade i _EMPTY.sql_.

## docs

Handskriven dokumentation av systemet formaterat med markdown. Bland annat
filen du läser nu.

## libs

De externa PHP-bibliotek systemet använder sig av. Hanteras av
[Composer](http://getcomposer.org/). Se **Serverkonfiguration**.

## src

Källkoden till systemet.

## tests

Unit-tester för koden i _/src/mreg_. Se **Tester och kodanalys**.

## var

Data systemet genererar eller använder sig av. Här finns exempelvis logg- och
cachefiler, översättningar och standardfiler.

## www

Den del av systemet som är synlig via http. Webbservern ska konfigureras att
använda denna katalog som rot. Här finns bland annat:

* _bootstrap.php_ Grundläggande steg som behöver tas för att köra systemet.
  Returnerar en Pimple dependecy injection container med de objekt systemet
  kräver. (Används av både http och cli.)

* _gateway.php_ Accesspunkt för all HTTP-åtkomst. Systemet använder
  [apache](http://httpd.apache.org/) med
  [mod_rewrite](http://httpd.apache.org/docs/2.0/mod/mod_rewrite.html) för att
  skicka alla url:er till gateway. Se *mreg.local*.

* _routes.php_ Returnerar en Aura route-map med definierade vägar för alla
  url:er systemet använder sig av.

* _jsclient_ JavaScript-klienten. Servern ska ej använda *mod_rewrite* för
  denna katalog.

* _static_ Statiska filer klienten kan hämta över HTTP. Servern ska ej använda
  *mod_rewrite* för denna katalog.



Serverkonfiguration
===================

## Grundläggande krav

1. Webbservern **Apache 2** med **mod_rewrite**

1. **MySQL >= 5.1.6** med följande inställningar:

    sql-mode="STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO"
    event_scheduler="ON"

1. **PHP >= 5.3.3** som apache-modul
    * **PDO** samt **PDO_MYSQL** för databaskopplingar
    * **HASH** med stöd för _MD5_, _sha512_ samt _crc32_
    * **bcmath** för flyttalsaritmetik

1. Önskade PHP-inställningar finns i *php.ini* i rot-katalogen.

1. Svensk lokalisering **sv_SE.utf8** eller motsvarande. Används bland annat
   för att formatera ekonomiska summor. Med en felaktig lokalisering finns
   risken att ekonomiska beräkningar avrundas felaktigt.

## Beroenden

Registret använder *composer* för att hantera PHP-bibliotek. För att
installera beroenden använd _Phing_ (se **Tester och kodanalys**)
eller composer direkt.

    curl -s http://getcomposer.org/installer | php
    php composer.phar install

Följande beroenden installeras:

1. [itbz/httpio](https://github.com/itbz/httpio)
1. [itbz/Cache](https://github.com/itbz/Cache)
1. [itbz/Utils](https://github.com/itbz/Utils)
1. [itbz/DataMapper](https://github.com/itbz/DataMapper)
1. [itbz/libmergepdf](https://github.com/itbz/libmergepdf)
1. [itbz/phplibphone](https://github.com/itbz/phplibphone)
1. [itbz/phplibaddress](https://github.com/itbz/phplibaddress)
1. [itbz/stb](https://github.com/itbz/STB)
1. [itbz/Mobilizr](https://github.com/itbz/Mobilizr)
1. [monolog/monolog](https://github.com/Seldaek/monolog)
1. [emberlabs/gravatarlib](https://github.com/emberlabs/gravatarlib)
1. [twig/twig](http://twig.sensiolabs.org/)
1. [swiftmailer/swiftmailer](http://swiftmailer.org/)
1. [Aura/Cli](https://github.com/auraphp/Aura.Cli)
1. [Aura/Router](https://github.com/auraphp/Aura.Router)
1. [AddedBytes/EmailAddressValidator](http://code.google.com/p/php-email-address-validation/)
1. [pimple/pimple](http://pimple.sensiolabs.org/)
1. [rych/phpass](https://github.com/rchouinard/phpass)



Tester och kodanalys
====================

## Unit-tester

Systemets unit-tester finns i _/tests_. 

[PHPUnit](http://www.phpunit.de) används för testerna. Ställ dig i _/tests_ och
skriv _phpunit_.

OBS! För att köra testerna krävs att de bibliotek registret använder finns
installerade. Se **Serverkonfiguration**.

Testerna kan även köras via _Phing_ för att automatiskt generera fina rapporter.
Då installeras även beroenden automatiskt.

## Installera Phing

Under utvecklingen har [Phing](http://www.phing.info/trac/) använts för att köra
unit-tester, generera dokumentation med mera. Phing installeras enklast med PEAR.

    pear config-set preferred_state alpha
    pear install --alldeps phing/phing
    pear config-set preferred_state stable

## Kör tester

Ställ dig i rot-katalogen och skriv _phing_ för att köra alla tester. En ny
katalog _build_ skapas med test-resultat, dokumentation och resultatet från en
del andra analysverktyg. De flesta rapporter är html-formaterade, så peka din
webbläsare mot _build_ för att granska resultatet.

## Deploy

Phing används även för deploy till testserver. Se _build.xml_. Deploy-rutinen är
lite rörig på grund av att testservern inte är dedikerad till projektet.


Loggar
======

Systemet använder [Monolog](https://github.com/Seldaek/monolog) för att logga
händelser.

* Intressanta händelser (loggnivå *info*) loggas till databastabellen
  _sys\_\_Log_. Det kan exempelvis vara att en användare loggar ut eller att
  ett felaktigt personnummer försökte sparas.
* Fel (loggnivå *error*) loggas även till fil med diverse extra information.
  _/var/log/mreg.log_
* Allvarliga fel (loggnivå *alert*) skickas även med mail.

Dessa loggar ska ses som komplement till PHPs vanliga errorlog och webbserverns
loggar.

