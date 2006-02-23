<?php

function hello( $name )
{
    return 'Hello, ' . $name;
}

$server->register(  'hello', 
                    array( 'name' => 'xsd:string' ),
                    array('greeting' => 'xsd:string'),
                    'urn:hellowsdl', 
                    'urn:hellowsdl#hello',
                    'rpc',
                    'encoded',
                    'Says hello to the caller'
                );

?>
