# iPublications Connector Class for AFAS Profit Webservices

By [iPublications Software](https://ipublications.net), 2013

## Purpose
Use this PHP 5 (PHP 7 tested, working fine) class to communicate with the AFAS Profit webservices. NTLM (AFAS Online) and AppConnectors (using token) are supported. Error? There's an Exception for that :)

Do you want to save lots and lots of time? Try **[nodum.io](https://nodum.io)** or have **[The Integrators](https://www.theintegrators.nl/)** build it for you.

### Folders:
- `./iPublications/Profit` - The classes (in the namespace iPublications\Profit)
- `./sample` - The samples (using the classes, with some inline docs and samples)

### Requirements (Server-side)

- PHP 5.4+
- CURL (in PHP)
- OpenSSL for CURL (when using HTTPS calls)

### Requirements (User-side)
- Some knowledge of AFAS Profit (GetConnector / UpdateConnector)
- Common sense
- Coffee (?)

---

Developing integrations with AFAS Profit (web/app/background) or building integrations with AFAS InSite / AFAS OutSite? Save a lot of time using **[nodum.io](https://nodum.io)**

![nodum.io logo](https://nodum.io/images/logo-nodum.svg)