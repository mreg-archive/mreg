
Databasen
=========

## Lagrade funktioner

Databasen använder lagrade funktioner för att hantera rättigheter. Tabeller med
åtkomst-restriktioner definierar följande kolumner:

* _owner_: namn på användare som äger raden
* _group_: namn på användargrupp som äger raden
* _mode_: åtkomstregler för ägare, grupp samt övriga

När servern läser data från dessa tabeller utvärderas åtkomst direkt i databasen
med hjälp av lagrade funktioner. Detta betyder att stora mängder av data en
användare inte har rätt att se ej behöver kopieras till applikationen för
utvärdering. Det betyder också att applikationen ej har tillgång till information
om hur många rader åtkomst nekades för. Dessa rader behandlas helt enkelt inte.


## Events

Events är SQL funktioner som automatiskt körs av databasservern vid givna
tidpunkter. Liknar cron-jobb i *nix system. Mreg kör följande events:

*   Sessioner
    
    Var femte minut scannar servern alla aktiva användarsessioner. Sessioner som varit
    inaktiva över 30 minuter loggas ut. Sessioner som varit inaktiva över 90 minuter
    raderas.

## Triggers

Triggers (utlösare) är funktioner som automatiskt körs av databasservern när
data ändras. Registret använder ett stort antal utlösare för att automatisera
vissa typer av data-underhåll. Dessa redovisas under respektive tabell.


## Prefix i kolumnnamn

* _n_ används som prefix för nummer i fall där det kan vara tvetydigt. _groups_
  skulle till exempel kunna vara en lista över grupper, men _nGroups_ är alltid
  en räknare av antal grupper.

* _t_ används som prefix för unix timestamps. Tider sparas generellt som
  timestamps i vanligt heltalskolumner. _t_ används i dessa fall för att
  förtydliga att det inte är en siffra utan en tidsangivelse.


## Tabeller

Databasen innehåller tabeller av olika art, varje tabells art kan härledas från
dess prefix:

Prefix        Beskrivning
------------- ------------------------------------------------------------------
**aux_**      Information relaterad till annat objekt: Adresser, ändringar osv.
              Ska läsas tillsammans med respektive huvudrad.
**dir_**      Grundläggande objekt i registret: Medlemmar, grupper, osv.
**eco_**      Ekonomisk data: Fakturor, avgiftstabeller osv.
**lookup_**   Statisk data registret använder för att genomföra olika uppgifter.
**search_**   Sök-tabeller
**sys_**      Information systemet behöver för att fungera: Användare, loggar..
**xref_**     Referens-data. Länkar poster från olika tabeller med varandra.


### dir__Member
Varje medlem motsvaras av en post i denna tabell.

#### Icke-normaliserat innehåll
För att snabba upp läsningar från databasen cachas namn på medlemmens nuvarande
LS. Kopierat från **xref__Faction_Member**.

Observera att detta inte i alla lägen är uppdaterat. Om en medlem har lämnat
ett LS, men inte gått med i något nytt, kommer det cachade värdet ej att ändras.
Detta är endast kosmetiska problem då dessa värden ej används av systemet.

#### Triggers
* ETags beräknas innan inserts och updates.
* Ändringar sparas efter updates.


### dir__Faction
Varje organisatorisk grupp motsvaras av en post i denna tabell.

#### Typer av grupper
Följande typer finns definierade: 'ANNAN', 'DISTRIKT', 'LS', 'ORTSSEKTION',
'FEDERATION', 'SYNDIKAT', 'DRIFTSSEKTION', 'AVDELNING', 'KONCERNFACK', 'TEMP'
samt 'TEKNISK'. Här följer några korta kommentarer för några av de mindre
uppenbara grupperna:

* 'ANNAN' Detta är en slaskgrupp att använda när ingen annan passar. Undvik att
använda den i största möjligaste mån.

* 'TEMP' Temporära grupper. Grupper som skapas för ett specifikt, ofta kortsiktigt,
ändamål. För att sedan slängas när arbetet är slutfört.

* 'TEKNISK' Tekniska grupper för exempelvis de som vill ha Syndikalisten hemskickad,
eller de som inte fått ett första utskick från LS.

#### Triggers
* ETags beräknas innan inserts och updates.
* Ändringar sparas efter updates.


### dir__Workplace
Varje arbetsplats motsvaras av en post i denna tabell.

#### Triggers
* ETags beräknas innan inserts och updates.
* Ändringar sparas efter updates.



### 'xref__' tabeller

Alla xref tabeller har samma struktur. De innehåller:

* Datum när relationen skapades.

* Datum för då relationen avslutades. Detta fält ska endast läsas ifall postens
  status ej är OK.

* Relationens status. Olika xref tabeller definierar olika möjliga status-värden.
  Gemensamt är att OK alltid betyder en aktiv (ej avslutad) relation, samt att
  annat värde än OK betyder att relationen ej är aktiv (är avslutad).
  **xref__Faction_Member** definierar exempelvis _OK_, _UTTRÄDD_, _UTESLUTEN_,
  _ÖVERGÅNG_, _AVLIDEN_, _PENSION_ samt _ANNAT_ som möjliga status-värden.

* Då det är nödvändigt kan även en kommentar för att närmare beskriva
  relationens status sparas. För **xref__Faction_Member** sparas här anledning
  till utträde eller uteslutning, nytt LS vid övergång samt beskrivning för
  status _ANNAT_.

Registret kan innehålla flera relationer mellan samma objekt, men endast en
relation med status OK för varje objektspar.

#### Triggers

* Datum för avslutad relation sätts efter update om status ändras till icke OK
  och datum ej är satt.
* **xref__Faction_Member** uppdaterar **dir__Member** cache för LS efter update
  och insert.

