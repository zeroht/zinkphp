{
    "errcode":"<{$errcode|default:200}>",
    "errmsg":"<{$errmsg|default:'success'}>",
    "errtime":"<{$smarty.now}>"<{if $data}>,
    "data":{
        <{foreach key=key item=value from=$data name=foo1}>
            "<{$key}>":"<{$value}>"
            <{if !$smarty.foreach.foo1.last}>,<{/if}>
        <{/foreach}>
    }
    <{/if}>
}