<!-- Module KinkySlider -->

<link href="{$base_dir}modules/kinkyslider/css/style.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="{$base_dir}modules/kinkyslider/js/jquery.transform-0.9.3.min.js" charset="utf-8"></script>
{foreach from=$kinkyslider_data key=myId item=kinkyitem}

    {if $kinkyitem.image neq ''}

            <li>
            	<a href="{$kinkyitem.link}">
                <img src="{$base_dir}{$kinkyitem.image}" class="kinkySliderImage" />
                </a>
                <div class="makeRelative">
                {if $kinkyitem.header neq ''}<div class="bazingaHeader"><a href="{$kinkyitem.link}">{$kinkyitem.header}</a></div>{/if}
                {if $kinkyitem.price neq ''}<div class="bazingaPrice"><a href="{$kinkyitem.link}">{$kinkyitem.price}</a></div>{/if}
                </div>
            </li>

    {/if}

{/foreach}
