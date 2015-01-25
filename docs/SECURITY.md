
Webbsäkerhet
============

## Konfiguration av webbserver

Ett grundläggande säkerhetskrav är att all trafik använder krypterad http
(https). Vilka algoritmer och rutiner som används för att säkerställa detta krav
är en fråga för systemadministratören. Jag kommer inte att säga mer om detta
här.

Det samma gäller hur Apache webbserver installeras och konfigureras.


## Lösenord

Lösenord måste vara starka för att godkännas av registret. För att beräkna
styrka används en algoritm från [Wolfram Alpha](http://www.wolframalpha.com/input/?i=password+strength+for+qwerty2345#).

För att godkännas krävs 100 poäng (kan enkelt ändras), vilket motsvarar
cirka 90 bitars entropi.

Utöver detta måste lösenord bytas minst var 6:e månad.


## Spärrade användare

Användare kommer att (mot bättre vetande)

* skriva sina lösenord på lappar som de glömmer på bussen
* glömma bort att logga ut när de går från kontoret
* låta gamla inloggningsuppgifter som egentligen inte behövs vara kvar

För att minimera dessa problem har följande säkerhetsåtgärder vidtagits:

* En användare som försöker logga in efter att inte ha loggat in på över tre
  månader spärras och uppmanas att kontakta admin.
* En användare som skrivit fel lösenord tre gånger på rad spärras och uppmanas
   att kontakta admin.
* En användare som inte kommunicerat med servern på 30 min loggas ut.
* Varje användare kan endast ha en aktiv session åt gången. Det är meningen
  att användare ska vara personliga. Om samma användare försöker starta två
  sessioner betyder det antingen att samma person öppnat två olika webbläsare,
  eller att någon utomstående kommit över inloggningsuppgifter. I detta läge
  loggas alla redan inloggade användare ut, och uppmanas kontakta admin om
  de misstänker att deras lösenord stulits.


## Cross-Site Scripting (XSS)

XSS attacker innebär att en användare av systemet lyckas skriva data
till systemet som när det laddas ner av andra användare körs som ett
script i webbläsaren. Om detta lyckas kan cookies och annan data som
identifierar den legitima användaren görs tillgänglig för attackeraren.

XSS attacker är möjliga först då en webbapplikation vill att användare
ska kunna fylla i olika former av HTML eller HTML-liknande taggar. I all
data som skickas till registret filtreras därför alla taggar bort
(alla tecken inneslutna av ett < och ett > tecken).


## Cross-Site Request Forgeries (CSRF)

CSRF är när en inloggad användare surfar till en helt annan site, vilken
i sin tur innehåller en länk till registret. Registret använder
cookies med speciella sessions-nycklar för att identifiera inloggade
användare. När användarens webbläsare ber om en resurs från vår server
kommer vår identifierande cookie automatiskt att inkluderas i transaktionen.
När den inloggade användaren surfar till tredje-parts sidan, som innehåller
en länk till registret (en attackeraren skulle till exempel kunna skapa en
tråd på ett forum och lägga in en fejkad bild som egentligen är en länk till
en viss resurs hos oss), kommer webbläsaren att inkludera rätt cookie
automatiskt och vi riskerar att lämna ut känslig information.

Följande åtgärder har vidtagits för att försvara mot CSRF attacker:

* Auktoriserade sessioner identifieras ej enbart med sessions-nycklar
  i cookies. En extra nyckel (fingerprint) genereras och måste skickas med
  alla förfrågningar för att de ska gå igenom. För att en CSRF attack ska
  fungera måste attackeraren på något sätt komma över både cookie och
  fingerprint.

* Registret nekar alla förfrågningar med en HTTP referer header från
  någon annan domän. 


## Session hijacking

Session hijacking är när en attackerare på något sätt kommit över ett
giltigt sessions-id, och därför kan använda siten som om hon loggat
in. Sessions-id kan kommas över på en mängd olika sätt, bland annat genom
att utnyttja säkerhetshål i webbläsare eller genom att tjuvlyssna på
trafik till siten.

Följande åtgärder har vidtagits för att stänga ute även de attackerare som
lyckats komma över ett giltigt sessions-id: (Vissa av åtgärderna kan
tyckas vara svaga, förhoppningen är att de sammantaget ska utgöra ett
starkt försvar.)

* Alla sessions-nycklar (sessions-id) skickas med http cookies, vilket
  anses vara säkrare än att skicka nycklar på andra sätt.

* Förutom att kräva en giltig sessions-nyckel för att identifiera en
  användare kräver systemet även ett giltigt fingerprint (se ovan).
  Fingerprints skickas ej i cookies.

* Vid varje lyckad autentisering genereras en ny sessions-nyckel samt
  fingerprint.

* Vilken webbläsare (User-Agent) som används sparas vid autentisering och måste
  vara konstant över hela sessionen.


## Säkerhet hos användaren

Slutligen är det självfallet viktigt att registret används i en
generellt säker miljö. Det vill säga att våra användare

* sitter bakom brandväggar
* ej sitter på nätverk det går att ansluta till via öppet WLAN
* eller via trådlösa nätverk med WEP kryptering
* använder senaste versionen av sin webbläsare och kontinuerligt installerar
  säkerhetsuppdateringar

