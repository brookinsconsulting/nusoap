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
}
?>