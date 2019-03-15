# iPublications Connector Class for AFAS Profit Webservices

By [iPublications Software](https://ipublications.net), 2013

## Purpose
Use this PHP class to communicate with the AFAS Profit webservices. NTLM (AFAS Online) and AppConnectors (using token) are supported. Error? There's an Exception for that :)

### Folders:
- `./iPublications/Profit` - The classes (in the namespace iPublications\Profit)
- `./sample` - The samples (using the classes, with some inline docs and samples)

### Requirements (Server-side)

- PHP 7.2+
- CURL (in PHP)
- OpenSSL for CURL (when using HTTPS calls)

### Requirements (User-side)
- Some knowledge of AFAS Profit (GetConnector / UpdateConnector)
- Common sense
- Coffee (?)

---

Developing integrations with AFAS Profit (web/app/background) or building integrations with AFAS InSite / AFAS OutSite? 

