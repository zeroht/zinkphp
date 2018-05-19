{
    <{include file="status200.tpl" }>
    "data": [
        <{foreach item=dt from=$data name=tk}>
        {
            "id": "<{$dt.id}>",
            "mobile": "<{$dt.mobile}>",
            "content":"<{$dt.content}>"
        }
        <{if !$smarty.foreach.tk.last}>,<{/if}>
        <{/foreach}>
    ]
}