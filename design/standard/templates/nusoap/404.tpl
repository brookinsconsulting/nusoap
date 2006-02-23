<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>404 Not Found</title>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    </head>
    <body>
        <p>The following web services are available:</p>
        <ul>
            {section loop=$services var=service}
                <li><a href={$service.key|ezurl}>{$service.item|wash}</a></li>
            {/section}
        </ul>
    </body>
</html>
