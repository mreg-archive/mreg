
REST-gränssnittet
=================

## Media-typer

* _application/x-sie_ är bokföringsdata i [SIE-formatet](http://www.sie.se).
  Detta är inte en standardiserad mediatyp.
* _application/x-kml_ är konteringsmallar i VISMAS kml-format. Detta är en inte
  standardiserad mediatyp.
* Om inte annat anges är typ i fortsättningen en subtyp av _application_


## Resurser

HTTP-metoder: G = GET, PU = PUT, D = DELETE, PO = POST

Namn                     Mediatyp Metoder Beskrivning
------------------------ -------- ------- ----------------------------
**Adress**               json     G,PU,D  En adress
**Telefonnummer**        json     G,PU,D  Ett telefonnummer
**Mail**                 json     G,PU,D  En mailadress
**Medlem**               json     G,PU,D  En medlem
**Grupp**                json     G,PU,D  En organisatorisk grupp,
                                          exempelvis ett LS
**Arbetsplats**          json     G,PU,D  En arbetsplats
**Användare**            json     G,PU,D  En användare av registret
**Systemgrupp**          json     G,PU,D  En systemgrupp *användare*
                                          kan vara medlemmar av
**Faktura**              json     G,PU,D  En faktura
**Verifikat**            x-sie    G       0 eller flera verifikat för
                                          bokföring
**Konteringsmall**       x-kml    G,PO    En eller flera konteringsmallar
**Kontoplan**            x-sie    G,PO    Kontoplan
**Bokhållare**           json     G,PU    En sammansatt resurs av
                                          konteringsmallar, kontoplan,
                                          avgiftstabell och
                                          bokföringskanaler
**Ändringar**            json     G       Beskriver ändringar
                                          utförda på originalresurs 

Namn                     Mediatyp Metoder Beskrivning
------------------------ -------- ------- ----------------------------
**Samling av telefon.**  json     G,PO    0 eller fler *telefonnummer*
**Samling av mail**      json     G,PO    0 eller fler *mail*
**Samling av adresser**  json     G,PO    0 eller fler *adresser*
**Samling av medlemmar** json     G,PO    0 eller fler *medlemmar*
**Samling av grupper**   json     G,PO    0 eller fler *grupper*
**Samling av arbetspl.** json     G,PO    0 eller fler *arbetsplatser*
**Samling av systemgrp** json     G,PO    0 eller fler *systemgrupper*
**Samling av användare** json     G,PO    0 eller fler *användare*
**Samling av fakturor**  json     G,PO    0 eller fler *fakturor*
**Relaterad grupp**      json     D,PU    En relation till en *grupp*
**Relaterad medlem**     json     D,PU    En relation till en *medlem*

## URLer

### Resurser

Resurs                                  URL
--------------------------------------  ----------------------------------------
Sökning                                 /search?q={query}&startPage={sida}&itemsPerPage={antal}
Samling av användare                    /users
Användare                               /users/{namn}
Samling av systemgrupper                /sys_groups
Systemgrupp                             /sys_groups/{namn}

Resurs                                  URL
--------------------------------------  ----------------------------------------
Samling av grupper                      /factions
Grupp                                   /factions/{id}
Samling av adresser                     /factions/{id}/addresses
Adress                                  /factions/{id}/addresses/{addrId}
Samling av telefon.                     /factions/{id}/phones
Telefonnummer                           /factions/{id}/phones/{phoneId}
Samling av mail                         /factions/{id}/mails
Mail                                    /factions/{id}/mails/{mailId}
Samling av grupper                      /factions/{id}/factions
Relaterad grupp                         /factions/{id}/factions/{facId}
Samling av grupper (medlemmar av)       /factions/{id}/member-factions
Relaterad grupp                         /factions/{id}/member-factions/{facId}
Samling av grupper (avslutade)          /factions/{id}/history
Samling av medlemmar                    /factions/{id}/members
Relaterad medlem                        /factions/{id}/members/{memId}

Resurs                                  URL
--------------------------------------  ----------------------------------------
Samling av medlemmar                    /members
Medlem                                  /members/{id}
Samling av adresser                     /members/{id}/addresses
Adress                                  /members/{id}/addresses/{addrId}
Samling av telefon.                     /members/{id}/phones
Telefonnummer                           /members/{id}/phones/{phoneId}
Samling av mail                         /members/{id}/mails
Mail                                    /members/{id}/mails/{mailId}
Samling av grupper                      /members/{id}/factions
Relaterad grupp                         /members/{id}/factions/{facId}
Samling av grupper (avslutade)          /members/{id}/history
Faktura                                 /member-invoices/{id}
Bokhållare                              /accountant
Konteringsmall                          /accountant/kml
Kontoplan                               /accountant/accounts



### Kontrollers

Om inte annat anges används HTTP POST

Utför                                   URL
--------------------------------------  ----------------------------------------
Fakturera medlemmar                     /accountant/bill
Skriv ut medlemsfakturor                /accountant/print
Exportera verifikationer                /accoutnant/export
Ladda upp en fil för behandling         /files/upload
Ladda ner fil (GET)                     /files/download/{file}
Logga ut session                        /controllers/service-logout
Töm servers cache-minne                 /controllers/clear-cache
Skapa bokhållare                        /controllers/create-accountant


## Länktyper

Namn                            Beskrivning
------------------------------  ------------------------------------------------
**/mreg/rels/members**          *Samling av medlemmar* som är medlemmar av
                                originalresursen (en *grupp*)
**/mreg/rels/factions**         *Samling av grupper* originalresursen
                                är medlem av
**/mreg/rels/member-factions**  *Samling av grupper* som är medlemmar av
                                originalresursen (en *grupp*)
**/mreg/rels/history**          *Samling av grupper* originalresursen
                                har varit, men ej längre är, medlem av
**/mreg/rels/workplaces**       *Samling av arbetsplatser*
                                originalresursen är länkad till
**/mreg/rels/sys_groups**       *Samling av systemgrupper* originalresurs
                                (en *användare*) är medlem av
**/mreg/rels/invoices**         *Samling av fakturor* knutna till
                                originalresursen
**/mreg/rels/mails**            *Samling av mail*, lägg till mail
**/mreg/rels/addresses**        *Samling av adresser*, lägg till adress
**/mreg/rels/phones**           *Samling av telefon.*, lägg till nummer

