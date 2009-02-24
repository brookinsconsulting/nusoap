<?php

error_reporting( 0 );

require_once 'autoload.php';
include_once( 'kernel/common/ezincludefunctions.php' );

/*!
 Reads settings from site.ini and passes them to eZDebug.
*/
function eZUpdateDebugSettings()
{
    $ini = eZINI::instance();

    list( $debugSettings['debug-enabled'], $debugSettings['debug-by-ip'], $debugSettings['debug-ip-list'] ) =
        $ini->variableMulti( 'DebugSettings', array( 'DebugOutput', 'DebugByIP', 'DebugIPList' ), array ( 'enabled', 'enabled' ) );
    eZDebug::updateSettings( $debugSettings );
}

$ini = eZINI::instance();

// Initialize/set the index file.
eZSys::init( 'nusoap.php', $ini->variable( 'SiteAccessSettings', 'ForceVirtualHost' ) == 'true' );

eZExtension::activateExtensions( 'default' );
// Extension check end


// Activate correct siteaccess
require_once( 'access.php' );
$access = array( 'name' => $ini->variable( 'SiteSettings', 'DefaultAccess' ),
                 'type' => EZ_ACCESS_TYPE_DEFAULT );
$access = changeAccess( $access );
$GLOBALS['eZCurrentAccess'] = $access;
// Siteaccess activation end

/*!
 Reads settings from i18n.ini and passes them to eZTextCodec.
*/
function eZUpdateTextCodecSettings()
{
    $ini = eZINI::instance( 'i18n.ini' );

    list( $i18nSettings['internal-charset'], $i18nSettings['http-charset'], $i18nSettings['mbstring-extension'] ) =
        $ini->variableMulti( 'CharacterSettings', array( 'Charset', 'HTTPCharset', 'MBStringExtension' ), array( false, false, 'enabled' ) );

    eZTextCodec::updateSettings( $i18nSettings );
}

// Initialize text codec settings
eZUpdateTextCodecSettings();

// Load modules
$moduleRepositories = array();
$moduleINI = eZINI::instance( 'module.ini' );
$globalModuleRepositories = $moduleINI->variable( 'ModuleSettings', 'ModuleRepositories' );
$extensionRepositories = $moduleINI->variable( 'ModuleSettings', 'ExtensionRepositories' );
$extensionDirectory = eZExtension::baseDirectory();
$globalExtensionRepositories = array();
foreach ( $extensionRepositories as $extensionRepository )
{
    $modulePath = $extensionDirectory . '/' . $extensionRepository . '/modules';
    if ( file_exists( $modulePath ) )
    {
        $globalExtensionRepositories[] = $modulePath;
    }
}
$moduleRepositories = array_merge( $moduleRepositories, $globalModuleRepositories, $globalExtensionRepositories );
eZModule::setGlobalPathList( $moduleRepositories );
// Load modules end

$db = eZDB::instance();

$sessionRequired = false;

if ( $sessionRequired and
     $db->isConnected() )
{
    eZSessionStart();
}

$uri = eZURI::instance( eZSys::requestURI() );

$serviceIdentifier = $uri->element( 0 );

$soapINI = eZINI::instance( 'nusoap.ini' );
$enableSOAP = $soapINI->variable( 'GeneralSettings', 'EnableSOAP' );
$availableServices = $soapINI->variable( 'GeneralSettings', 'AvailableServices' );

if ( $serviceIdentifier and $enableSOAP == 'true' and in_array( $serviceIdentifier, $availableServices ) )
{
    $serviceBlock = 'Service_' . $serviceIdentifier;

    eZSys::init( 'nusoap.php' );

    // Login if we have username and password.
    if ( isset( $_SERVER['PHP_AUTH_USER'] ) and isset( $_SERVER['PHP_AUTH_PW'] ) )
    {
        eZUser::loginUser( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] );
    }

    include_once( 'extension/nusoap/classes/nusoap.php' );

    $server = new soap_server( );

    /*
        \todo Replace INI checking of charset with API call charset checking
    */
    $intIni = eZINI::instance( 'i18n.ini' );
    $charset = strtoupper( $intIni->variable( 'CharacterSettings', 'Charset' ) );

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
            $server->soap_defencoding = 'UTF-8';
            $server->decode_utf8 = false;
        } break;

        default:
        {
            /*
                \todo Maybe add some kind of error handling, NuSOAP unsupported charset.
                NuSOAP does not support automatic encoding conversion of return messages.
                The service plugins probably should encode values in the SOAP charset themselves.
            */
        }
    }

    $server->configureWSDL( $soapINI->variable( $serviceBlock, 'ServiceName' ), $soapINI->variable( $serviceBlock, 'ServiceNamespace' ) );

    foreach ( $soapINI->variable( $serviceBlock, 'SOAPExtensions' ) as $extension => $soapExtension )
    {
        include_once( eZExtension::baseDirectory() . '/' . $extension . '/nusoap/' . $soapExtension . '.php' );
    }

    $HTTP_RAW_POST_DATA = isset( $HTTP_RAW_POST_DATA ) ? $HTTP_RAW_POST_DATA : '';

    $server->service( $HTTP_RAW_POST_DATA );

    $enableLog = $soapINI->variable( 'GeneralSettings', 'EnableLog' );

    if ( $enableLog == 'true' )
    {
        $log = fopen( 'var/log/nusoap.log', 'a+' );
        fwrite( $log, $server->debug_str );
        fclose( $log );
    }
}
else
{
    header( 'HTTP/1.x 404 Not Found' );

    require_once( 'kernel/common/template.php' );
    $tpl = templateInit( );

    $services = array( );

    foreach ( $availableServices as $service )
    {
        $services[$service] = $soapINI->variable( 'Service_' . $service, 'ServiceName' );
    }

    $tpl->setVariable( 'services', $services );

    $result = $tpl->fetch( 'design:nusoap/404.tpl' );

    print( $result );
}

?>
