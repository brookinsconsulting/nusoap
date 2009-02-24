<?php
class nusoapInfo
{
    function info()
    {
        return array(
            'Name' => "NuSOAP eZ publish integration",
            'Version' => "2.0",
            'Copyright' => "Copyright (C) 2006-2008 SCK-CEN, 2009 Kristof Coomans",
            'Author' => "Kristof Coomans",
            'License' => "GNU General Public License v2.0",
            'Includes the following third-party software' => array( 'Name' => 'NuSOAP',
                                                                    'Version' => '1.0rc1 (cvs rev. 1.83)',
                                                                    'License' => 'GNU Lesser General Public License',
                                                                    'More information' => 'http://sourceforge.net/projects/nusoap/',
                                                                  )
        );
    }
}
?>