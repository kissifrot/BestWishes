{if $success}
{ldelim}"success": true, "message": "{$message|escape}"{rdelim}
{else}
{ldelim}"success": false, "message": "{$message|escape}"{rdelim}
{/if}