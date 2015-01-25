
Användarmanual webbklient
=========================

## Webbläsare

Det finns en mängd webbläsare som alla skiljer sig åt. Registret har testats
med _Google chrome_ och _Firefox_. Stöd för andra webbläsare kan inte
garanteras.


## Listor

Alla grundläggande typer av poster i registret (grupper, medlemmar,
arbetsplatser osv.) kan visas i listor. En lista kan vara en sökning, en
virtuell arbetslista, alla medlemmar i ett LS osv. Poster kan flyttas mellan
listor genom att kopiera till urklipp (mer om det nedan).

> Att lägga till en post till listan över ett LS medlemmar är semantiskt samma
> sak som att göra posten till medlem av LS.

Poster kan raderas från listor med hjälp av knappen _ta bort_.

> Att ta bort en post från listan över ett LS medlemmar är semantiskt samma
> sak som att avskriva medlemmen.

### Arbetslistor

Arbetslistor existerar endast i webbklienten. Det vill säga ingen motsvarande samling
existerar i registret. Med hjälp av arbetslistor kan en administratör samla
poster som en del i arbetsflödet. Exempelvis för att skriva ut etiketter,
kontroller betalningar osv.

### Att arbeta med urklipp

Urklipp är en global lista som alltid existerar i bakgrunden. Klicka på knappen
_visa urklipp_ för att arbeta med den direkt.

När en eller flera poster från vilken lista som helst _kopieras_ så hamnar de i
urklipp. 

När knappen _lägg till_ aktiveras på någon lista så _flyttas_ samtliga poster
från urklipp till denna lista. På detta sätt kan poster flyttas mellan listor.

Vissa listor kan bara innehålla vissa typer av poster. Det är exempelvis inte
möjligt att lägga till en grupp till en lista över ett LS medlemmar.


## Sökningar

Sökningar görs mot ett speciellt sök-index. För att ny eller uppdaterad data
ska vara sökbar måste det indexeras. Eftersom det är en tidskrävande uppgift
görs det inte efter varje uppdatering. Istället indexeras databasen automatiskt
med ett visst tidsintervall.

> Det betyder att ny eller ändrad data inte är sökbar innan en ny indexering
> utförts.

I nuläget omskapas indexet varje natt. Om det visar sig vara för sällan kan vi
korta intervallet.

För sökningar används följande syntax:

* Varje sökord eftersöks _i sin helhet_ i databasen. Det vill säga _malm_
  matchar INTE _malmö_.
* \* används som wildcard i slutet av sökord. Det matchar vad som helst. _malm*_
  matchar exempelvis _malmö_.
* \* kan inte användas i början av ord.
* \+ i början av ett sökord gör att endast sökningar som innehåller ordet
  returneras (sökord utan + gör att poster med detta ord värderas högre än andra
  poster, men de utesluts inte).
* \- i början av ett sökord gör att inga poster som matchar sökordet returneras.
* Parenteser kan användas för att logiskt gruppera sökord.
* Fraser inneslutna av " eftersöks i sin helhet, det vill säga ej som enskilda
  ord.

För den tekniskt intresserade finns mer information på
[mysql.com](http://dev.mysql.com/doc/refman/5.0/en/fulltext-boolean.html).

### Avancerade sökningar

Det finns för närvarande inte något gränssnitt för avancerade sökningar.
Följande syntax används tills vidare.

* **\+type_member** söker efter endast medlemmar
* **\+type\_faction\_\*** söker efter endast grupper
* **\+type\_faction\_ls** söker efter endast LS (alternativt använd *distrikt*,
  *ortssektion*, *federation*, *syndikat*, *driftssektion*, *avdelning*,
  *koncernfack*, *temp* eller *teknisk*).
* **\+id_1** söker efter alla poster med id 1 (av ospecificerad typ)

Det går även att söka efter länkad data.

* **\+xref\_faction\_1\_ok** söker efter alla poster som är medlemmar av grupp
  nummer 1 (Stockholms LS).
* **\+xref\_faction\_1\_\*** söker efter alla poster som är eller har varit
  medlemmar av grupp nummer 1.
* **\+xref\_faction\_1\_* \-xref\_faction\_1\_ok** söker efter alla poster som
  har varit men inte längre är medlemmar av grupp nummer 1.
* **\+xref\_member\_1\_ok** söker efter alla poster som medlem nummer 1 är
  medlem av.

Allt kan kombineras

* **\+type\_member \+xref\_faction\_1\_ok** söker efter alla medlemmar som är
  medlemmar av grupp nummer 1.

### Medlemssök

* **\+sex\_f** söker efter kvinnor (alternativt _m_ för män eller _o_ för annat
  kön).
* **\+debitclass_a** söker efter medlemmar i avgiftsklass A.
* **\+paymenttype_ag** söker efter medlemmar som betalar via autogiro.
* **\+personalid_NNNNNN-NNNN** söker efter personnummer.
* **\+workcondition_anstalld** söker efter medlemmar med arbetsvillkor anställd.
* **\+invoiceflag_1** söker efter medlemmar med förfallna fakturor.

### Söka efter fakturor

* **\+type\_invoice\_member** söker efter medlemsfakturor
* **\+invoice_1** söker efter fakturanummer 1
* **\+invoice\_ocr\_133** söker efter faktura med ocr-nummer 133
* **\+invoice\_paid\_\*** söker efter betalda fakturor
* **\+invoice\_paid\_pg** söker efter fakturor som betalats via plusgiro (även
  *ag*, *bg*, och *k* för autogiro, bankgiro och kontant).
* **\+invoice_printed** söker efter utskrivna fakturor
* **\+invoice_expired** söker efter förfallna fakturor
* **\+invoice_locked** söker efter låsta fakturor
* **\+invoice_blanco** söker efter blanco-fakturor
* **\+invoice_exported** söker efter fakturor som exporterats till bokföring
* **\+invoice_ag** söker efter autogiro-fakturor
* byt *\+* mot *\-* för att utesluta kriterier. Exempelvis
  **\+type\_invoice\_member \+xref\_faction\_1 \-invoice\_paid\_\*** söker
  efter obetalda fakturor till grupp nummer 1


## Poster

Poster öppnas på höger sida av arbetsytan. Längst ut till höger av den översta
knappraden finns en uppsättning knappar för att arbeta med poster. Dessa
knappar arbetar alltid på den post som för tillfället är öppen.

* **Redigera** öppnar en dialogruta för att redigera aktiv post.
* **Uppdatera** hämtar aktiv post från servern. Detta är användbart för att
  hämta ändringar som gjort på posten sedan den öppnades. Den är inte möjligt
  att spara en post om det finns ändringar som gjorts efter det att posten
  öppnades.
* **Kopiera** kopierar posten till urklipp.
* **Radera** tar bort posten från registret. Detta är betyder att posten
  verkligen raderas och inte kan återskapas. Var mycket restriktiv med detta.


## Kortkommandon

* **Ctrl+F** öppna ny sökning
* **Alt+Ctrl+N** skapa ny post
* **Ctrl+E** öppna aktiv post för redigering
* **Alt+Ctrl+R** hämta senaste versionen av aktiv post
* **Alt+Ctrl+B** öppnar bokhållare


## Struliga medlemmar

Medlemmar med förfallna fakturor flaggas för att underlätta uppföljning. Under
Ekonomi > Status visas om en medlem ligger efter med betalningar. En röd prick
betyder att förfallna fakturor finns, en grön prick betyder det motsatta.

Flaggningen uppdateras varje natt då systemet söker igenom fakturaregistret.
En medlem kan alltså under en kortare tid vara flaggad trots att betalning
registrerats. Listan över obetalda fakturor är alltid korrekt.

Det går även att söka efter flaggade medlemmar med **invoiceflag_1**.


## Typer av data i registret

### Personnummer

Ange personnummer med 10 siffror (6 siffror, avdelare, 4 siffror). För personer
utan svenskt personnummer ersätts de fyra sista siffrorna av xxxx, alternativt
xx1x för män eller xx2x för kvinnor. För personer över hundra år används
plustecken (+) som avdelare mellan datum och födelsenummer, annars används
bindestreck (-).

### Bankkontonummer

Används endast med autogiro. Ange konto med clearing- (alltid fyra siffror)
och löpnummer skiljda med ett kommatecken (,).

Clearingnummer ska alltid anges med fyra siffror. Vissa banker (Swedbank)
använder clearingnummer med fem siffror. Då är den sista siffran en intern
kontrollsiffra som inte ingår i det nationella banksystemet. Kontrollsiffran
ska vid inmatning i registret utelämnas.

Systemet visar bankens namn. Detta läses från clearingnumret. Om bankens namn
är okänt behöver det inte betyda att kontonumret är felaktigt ifyllt (systemet
känner inte till alla banker).

### Plusgirokonton

Ett Plusgironummer kan bestå av lägst två siffror och högst åtta siffror (_x-x_
till _xxx xx xx-x_). Den sista siffran är en kontrollsiffra.

### Bankgirokonton

Ett bankgironummer består av sju eller åtta siffror (_xxx-xxxx_ eller
_xxxx-xxxx_). Den sista siffran är en kontrollsiffra.

### Inkomst och avgiftsklass

Inkomst anges som månatlig sammanlagd inkomst före skatt (efter sociala
avgifter).

Om uppgift om inkomst finns kommer avgiftsklass att beräknas baserat på inkomst
(vid fakturering).

Om uppgift om inkomst saknas kan avgiftsklass anges explicit, och kommer i
förekommande fall att användas vid fakturering.

Om uppgift om både inkomst och avgiftsklass saknas kommer blanco-fakturor att
genereras.

För att en medlem ska övergå till blanco-fakturering måste alltså både uppgift
om inkomst och avgiftsklass tömmas.

### Betalningsvillkor

Systemet använder sig av följande betalningsvillkor:

* **AG** (autogiro). Medlemmen betalar via autogiro. Systemet prickar av
  betalningar automatiskt från autogirots rapportfiler.
* **BAG** (begär autogiro). Medlemmen önskar betala via autogiro. Generera
  autogirofil och skicka till banken för behandling. (Omvandlas automatiskt
  till BAG-V när autogirofil hämtats från systemet.)
* **BAG-V** (begär autogiro väntar på svar). Begäran om autogiro har skickats till
  banken. (Omvandlas automatiskt till AG när klartecken kommer från banken.)
* **MAG** (makulera autogiro). Medlemmen önskar inte längre betala via autogiro.
  (Omvandlas automatiskt till MAG-V när autogirofil hämtats från systemet.)
* **MAG-V** (makulera autogiro väntar på svar). Begäran om att makulera autogiro
  har skickats till banken. (Omvandlas automatiskt till LS när klartecken kommer
  från banken.)
* **PAG** (problem med autogiro).
* **LS** Ej autogiro utan betalning direkt till LS.


## Kontaktuppgifter

Varje post i registret kan ha ett obegränsat antal telefonnummer, mail- och
vanliga adresser. Varje kontaktuppgift identifieras av ett namn. Detta namn
fyller ingen teknisk funktion, utan visas endast för att det skall vara enkelt
att identifiera olika kontaktuppgifter. Det finns inget som hindrar att en
mailadress kallas för _Tele_, även om det såklart skulle vara olyckligt.

När en kontaktuppgift öppnas för redigering visas när och av vem uppgiften
senast sparades. Tidskoden ändras varje gång posten sparas, även om ingenting
faktiskt ändrades. Använd denna funktion för att markera att en kontaktuppgift
fortfarande är giltig, exempelvis efter ett telefonsamtal till en medlem.

### Telefonnummer

Telefonnummer kan matas in i registret i olika format. Följande telefonnummer
är identiska från systemets perspektiv:

* +468123456
* +46-8-123 456
* 08-123456
* 08123456

Om landskod fylls i måste den föregås av ett _+_. Om landskod ej fylls i är _46_
standard.

Till höger om telefonnummer visas antingen en grön eller en röd prick. Grön
prick betyder att numret är ett svenskt mobilnummer. När systemet skickar SMS
eller skriver ut listor över SMS-nummer tas endast dessa nummer med.

Röd prick betyder att numret inte är ett giltigt svenskt mobilnummer.

När telefonnummer öppnas för redigering visas ytterligare information om land,
område och operatör. Denna information läses från telefonnumret och är ett stöd
för att organisatören enkelt ska kunna se att allt ser korrekt ut.

### Mailadresser

Till höger om mailadresser visas antingen en grön eller en röd prick. Grön prick
betyder att mailadressen är giltig. När systemet skickar mail eller skriver ut
listor av mailadresser tas endast dessa adresser med. 

Röd prick betyder att adressen inte är en giltig mailadress.

### Postadresser

Postadresser sparas i registret nedbrutna i en mängd fält. Den kompletta
adressen byggs upp enligt prioriteringsregler från [SIS](http://www.sis.se). För
att det inte ska bli fel är det viktigt att informationen är ifylld på rätt
sätt. Kort beskrivning av mindre uppenbara fält:

* **Förmedlande mottagare** Även känt som care-of eller c/o. För
  care-of-adresser ska *mottagarrolluppgift* vara *c/o* och *mottagare*
  namnet på den förmedlande personen/institutionen.
* **Administrativ adress** Kan exempelvis vara en postbox eller poste restante.
  För en boxadress ska *Typ av administrativ adress* vara *box* och
  *administrativ adress* postboxens nummer.
* **Uppgång** Exempelvis *UH* för uppgång höger eller *U2* för uppgång
  nummer två.
* **Våningsplan** Exempelvis *NB* för nedre botten eller *2TR* för våning
  nummer två.
* **Lägenhetsnummer** Lägenhetsnummer som det skrivs i svensk folkbokföring.
  Endast siffror.
* **Extra uppgift** Ett väldigt fritt fält. Kan exempelvis vara *mittemot nummer
  50* eller *50 meter till vänster om huvudingång*.
* **Landskod** Kod enligt [ISO 3166-1](http://sv.wikipedia.org/wiki/Landskoder),
  där exempelvis *SE* står för Sverige.


## Kända problem

Följande begränsningar finns i alpha-versionen:

* Att ladda upp filer fungerar endast med Google Chrome.
* Möjligheterna att redigera bokhållare är begränsade
* Listan över medlemmar hos varje grupp kan endast visa 300 poster. För grupper
  med fler medlemmar är den kortsiktiga lösningen att söka efter
  **\+type\_member \+xref\_faction\_id\_ok**, där **id** ersätts med gruppens
  id.

