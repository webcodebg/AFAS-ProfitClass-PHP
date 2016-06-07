# iPublications Connector Class (Profit Class) V3

	iPublications BV, 2013
	Wietse Wind <w.wind@ipublications.net>
	Ready for iPublications Autoloader :)

## Classes (objects):
	- Connection	  [ Connection object ]
	- Connector		  [ Abstract Class    ], Exception: 1-6
	- ConnectorFilter [ Filter object     ], Exception: 1000-1002

## Endpoint Usable classes (connectors) so far:
	- GetConnector
	    -> Containing LIMIT and SORT since 2013/04/27
	    -> Sort for Profit 2016 (AFAS SIDE) is default,
	       Use '$g->SetDeprecatedSortingMethod();' before
	       sorting to use old 2014 method.
	- SubjectConnector
	- ReportConnector
	- DataConnector
	- UpdateConnector
	- OSCConnector (OfficeConnector) containing multiple methods

## Folders:
	./iPublications/Profit		-> The classes
	./sample					-> The samples (using the classes)

## Requirements (Server-side)

	- PHP
	- CURL lib in PHP
	- OpenSSL for CURL (when using HTTPS calls)

## Requirements (User-side)
	- Common sense
	- Coffee
