<?php

class NuSOAPInstallScript extends eZInstallScriptPackageInstaller
{
    function NuSOAPInstallScript( &$package, $type, $installItem )
    {
        $steps = array();
        $steps[] = array( 'id' => 'nusoap_frontcontroller',
                          'name' => 'NuSOAP front controller installation',
                          'methods' => array( 'initialize' => 'initializeFrontControllerStep',
                                              'validate' => 'validateFrontControllerStep',
                                              'commit' => 'commitFrontControllerStep' ),
                          'template' => 'nusoapinstallfrontcontroller.tpl' );
        $steps[] = array( 'id' => 'nusoap_settings',
                          'name' => 'NuSOAP default settings installation',
                          'methods' => array( 'initialize' => 'initializeDefaultSettingsStep',
                                              'validate' => 'validateDefaultSettingsStep',
                                              'commit' => 'commitDefaultSettingsStep' ),
                          'template' => 'nusoapinstalldefaultsettings.tpl' );
        $steps[] = array( 'id' => 'nusoap_test',
                          'name' => 'NuSOAP test',
                          'methods' => array( 'initialize' => 'initializeTestStep',
                                              'validate' => 'validateTestStep',
                                              'commit' => 'commitTestStep' ),
                          'template' => 'nusoapinstalltest.tpl' );
        $this->eZPackageInstallationHandler( $package,
                                             $type,
                                             $installItem,
                                             'NuSOAP installation',
                                             $steps );
    }

    function fileCopyTest( $dir, $fileName, &$tpl, &$persistentData )
    {
        include_once( 'lib/ezutils/classes/ezsys.php' );
        include_once( 'lib/ezfile/classes/ezdir.php' );
        include_once( 'lib/ezfile/classes/ezfile.php' );

        $sys =& eZSys::instance();
        $siteDir = $sys->siteDir();

        // check if dir is writeable
        $dirWriteable = eZDir::isWriteable( $siteDir . $dir );

        // check if file exists and if it is writeable
        $filePath = $siteDir . $dir . $fileName;
        $fileWriteable = false;
        $fileExists = file_exists( $filePath );
        if ( $fileExists )
        {
            $fileWriteable = eZFile::isWriteable( $filePath );
        }
        else
        {
            // if file doesn't exist than we can write to it if dir is writeable
            $fileWriteable = $dirWriteable;
        }

        $tpl->setVariable( 'root_dir', $siteDir );
        $tpl->setVariable( 'file_exists', $fileExists );
        $tpl->setVariable( 'writeable', $fileWriteable );

        $persistentData['writeable'] = $fileWriteable;

        return true;
    }

    function initializeFrontControllerStep( &$package, &$http, $step, &$persistentData, &$tpl, &$module )
    {
        return NuSOAPInstallScript::fileCopyTest( '', 'nusoap.php', $tpl, $persistentData );
    }

    function validateFrontControllerStep( &$package, &$http, $currentStepID, &$stepMap, &$persistentData, &$errorList )
    {
        $writeable = $persistentData['writeable'];

        if ( $writeable && !$http->hasPostVariable( 'Skip' ) )
        {
            $success = @copy( 'extension/nusoap/install/nusoap.php', 'nusoap.php' );

            if ( !$success )
            {
                $errorList[] = array( 'field' => false,
                                      'description' => 'Unable to copy nusoap.php' );
            }

            if ( count( $errorList ) > 0 )
            {
                return false;
            }
        }

        return true;
    }


    function commitFrontControllerStep( &$package, &$http, $step, &$persistentData, &$tpl )
    {
        return true;
    }

    function initializeDefaultSettingsStep( &$package, &$http, $step, &$persistentData, &$tpl, &$module )
    {
        return NuSOAPInstallScript::fileCopyTest( 'settings/', 'nusoap.ini', $tpl, $persistentData );
    }

    function validateDefaultSettingsStep( &$package, &$http, $currentStepID, &$stepMap, &$persistentData, &$errorList )
    {
        $writeable = $persistentData['writeable'];

        if ( $writeable && !$http->hasPostVariable( 'Skip' ) )
        {
            $success = @copy( 'extension/nusoap/install/nusoap.ini', 'settings/nusoap.ini' );

            if ( !$success )
            {
                $errorList[] = array( 'field' => false,
                                      'description' => 'Unable to copy nusoap.ini' );
            }

            if ( count( $errorList ) > 0 )
            {
                return false;
            }
        }

        return true;
    }


    function commitDefaultSettingsStep( &$package, &$http, $step, &$persistentData, &$tpl )
    {
        return true;
    }

    function initializeTestStep( &$package, &$http, $step, &$persistentData, &$tpl, &$module )
    {
        if ( array_key_exists( 'name', $persistentData ) )
        {
            $name = $persistentData['name'];
        }
        else
        {
            $name = "eZ celeb";
        }

        if ( array_key_exists( 'wsdl_url', $persistentData ) )
        {
            $WSDLurl = $persistentData['wsdl_url'];
        }
        else
        {
            $WSDLurl = "http://soap.example.com/helloworld?wsdl";
        }

        if ( array_key_exists( 'soap_result', $persistentData ) )
        {
            $tpl->setVariable( 'result', $persistentData['soap_result'] );
        }

        $persistentData['wsdl_url'] = $WSDLurl;
        $persistentData['name'] = $name;
        $tpl->setVariable( 'wsdl_url', $WSDLurl );
        $tpl->setVariable( 'name', $name );

        return true;
    }

    function validateTestStep( &$package, &$http, $currentStepID, &$stepMap, &$persistentData, &$errorList )
    {
        unset( $persistentData['soap_result'] );

        if ( $http->hasPostVariable( 'TestingDone' ) )
        {
            return true;
        }

        $WSDLurl = $persistentData['wsdl_url'];
        $name = $persistentData['name'];

        if ( $http->hasPostVariable( 'WSDLurl' ) )
        {
            $WSDLurl = $http->postVariable( 'WSDLurl' );
        }

        if ( $http->hasPostVariable( 'Name' ) )
        {
            $name = $http->postVariable( 'Name' );
        }

        $persistentData['wsdl_url'] = $WSDLurl;
        $persistentData['name'] = $name;

        ext_class( 'nusoap', 'nusoap' );
        $client = new soapclient( $WSDLurl, true );

        /*
            \todo Replace INI checking of charset with API call charset checking
        */
        include_once( 'lib/ezutils/classes/ezini.php' );
        $intIni =& eZINI::instance( 'i18n.ini' );
        $charset = strtoupper( $intIni->variable( 'CharacterSettings', 'Charset' ) );

        eZDebug::writeDebug( $charset, 'internal charset' );
        switch ( $charset )
        {
            case 'ISO-8859-1':
            case 'LATIN-1':
            {
                // NuSOAP uses ISO-8859-1 by default, so we do not have to do anything special
            } break;

            case 'UTF-8':
            case 'UTF8':
            {
                $client->decodeUTF8( false );
                $client->soap_defencoding = 'UTF-8';
            } break;

            default:
            {
                /*
                    \todo Maybe add some kind of error handling, NuSOAP unsupported charset.
                    The service plugins probably should decode values to the eZ charset themselves.
                */
            }
        }

        $err = $client->getError( );

        if ( $err )
        {
            $errorList[] = array( 'field' => 'SOAP client', 'description' => $err );
            return false;
        }

        $result = $client->call( 'hello', array( 'name' => $name ) );

        eZDebug::writeDebug( $client->request );
        eZDebug::writeDebug( $client->response );

        if ( $client->fault )
        {
            $errorList[] = array( 'field' => 'SOAP client', 'description' => $result );
            return false;
        }

        $err = $client->getError();
        if ( $err )
        {
            $errorList[] = array( 'field' => 'SOAP client', 'description' => $error );
            return false;
        }

        $persistentData['soap_result'] = $result;

        return false;
    }

    function commitTestStep( &$package, &$http, $step, &$persistentData, &$tpl )
    {
        return true;
    }
}
?>
