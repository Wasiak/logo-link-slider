<!-- Module KinkySlider -->
<link href="{$base_dir}modules/kinkyslider/css/style.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="{$base_dir}modules/kinkyslider/js/jquery.transform-0.9.3.min.js" charset="utf-8"></script>

{foreach from=$kinkyslider_data key=myId item=kinkyitem}
  {if $kinkyitem.image neq ''}
    <a href="{$kinkyitem.link}">
      <img id="{$id}" src="{$base_dir}{$kinkyitem.image}">
    </a>
  {/if}
{/foreach}
