<?php
class nusoapInfo
{
    function info()
    {
        return array(
            'Name' => "NuSOAP eZ publish integration",
            'Version' => "1.0",
            'Copyright' => "Copyright (C) 2006 SCK-CEN",
            'Author' => "Kristof Coomans",
            'License' => "GNU General Public License v2.0",
            'Includes the following third-party software' => array( 'Name' => 'NuSOAP',
                                                                    'Version' => '0.7.1',
                                                                    'License' => 'GNU Lesser General Public License',
                                                                    'More information' => 'http://sourceforge.net/projects/nusoap/'
                                                                  )
        );
    }
}
?>