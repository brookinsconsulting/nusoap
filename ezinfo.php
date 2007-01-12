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
                                                                    'Version' => '0.7.2 (cvs rev. 1.95)',
                                                                    'License' => 'GNU Lesser General Public License',
                                                                    'More information' => 'http://sourceforge.net/projects/nusoap/',
                                                                    'Applied patches: ' => 'http://sourceforge.net/tracker/index.php?func=detail&aid=1008122&group_id=57663&atid=484965'
                                                                  )
        );
    }
}
?>