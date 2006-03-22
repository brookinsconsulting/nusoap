NuSOAP extension for eZ publish

Copyright (C) 2005-2006 SCK-CEN
Written by Kristof Coomans ( http://blog.kristofcoomans.be )

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.


 Features
************
The NuSOAP extension allows you to create and consume web services based on SOAP 1.1, WSDL 1.1 and HTTP 1.0/1.1.
It is build upon the NuSOAP PHP library ( http://sourceforge.net/projects/nusoap/ ).


 Usage
************

Take a look in the nusoap.ini settings file to learn how to enable your own web services.
Take a look at the NuSOAP documentation in the docs directory of this extension to learn how to write 
NuSOAP webservices.

To consume webservices, you can write your own command line client or module and 
use the NuSOAP library. There's also an example client in this extension (bin/php/helloworldclient.php).