{
    <{include file="status200.tpl" }>
    "data":{
        "total":"<{$total}>",
        "p" : "<{$p}>",
        "pn" : "<{$pn}>",
        <{if $extend}>
        "summary":{
        <{foreach key=name item=value from=$extend name=foo2}>
        "<{$name}>":"<{$value}>"<{if !$smarty.foreach.foo2.last}>,<{/if}>
        <{/foreach}>
        },
        <{/if}>
        "result" : [
        <{foreach item=data from=$result name=foo1}>
            <{include file="$tpl" data=$data}>
        <{if !$smarty.foreach.foo1.last}>,<{/if}>
        <{/foreach}>
        ]
    }
}



