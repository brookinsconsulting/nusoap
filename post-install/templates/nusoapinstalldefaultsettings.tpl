<form method="post" action={'package/install'|ezurl}>

    {include uri="design:package/install/error.tpl"}
    {include uri="design:package/install_header.tpl"}

    {if $writeable}
        <p>
        When you continue, the <em>nusoap.ini</em> default settings file will be installed.
        If you want to skip the automatic installlation, then enable the checkbox below.
        </p>
        <p>
        <input type="checkbox" name="Skip" /> Skip automatic installation of the default settings file
        </p>
        <p>You can manually install the file with the following commands on the UNIX command line:</p>
    {else}
        <p>We have detected that your eZ publish your webserver does not have the required write permissions.
        You will have to install <em>nusoap.ini</em> manually. You can use the following commands with a priveleged user account on the UNIX command line:</p>
    {/if}
<pre>
# cd {$root_dir}
# cp ./extension/nusoap/install/nusoap.ini ./settings/nusoap.ini
</pre>

{if $file_exists}
<div class="message-warning">
<h2>The target file already exists. Make sure you backup any changes you made to it.</h2>
</div>
{/if}

    {include uri="design:package/navigator.tpl"}

</form>