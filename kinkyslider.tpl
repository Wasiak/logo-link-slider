<div class='main-container' id='manufacturers_slider'>
</div>

<link href="{$base_dir}modules/ed02d314199b8b0/class.css" rel="stylesheet" type="text/css" media="all" />
<script>
var items = [
  {foreach from=$kinkyslider_data key=myId item=kinkyitem}
    {literal}{{/literal}
      'image' : '{$base_dir}{$kinkyitem.image}', 'link' : '{$kinkyitem.link}'
    {literal}}{/literal},
  {/foreach}
];

{literal}

var manufacturers_slider = document.getElementById('manufacturers_slider');
var Logo = function(item) {
  var logoBox = document.createElement('a');
  manufacturers_slider.appendChild(logoBox);
  logoBox.href = item.link;

  var logo = document.createElement('img');
  logoBox.appendChild(logo);
  logo.id = item.name;
  logo.src = item.image;
};

var initLogos = function() {
  for (i = 0; i < 4; i++) {
    new Logo(items[i]);
  }
}();

var nextToChange = 0;
var nextToAdd = 4;
var changeLogo = function() {
  var outBox = manufacturers_slider.getElementsByTagName('a')[nextToChange];
  outBox.classList.add('ed2-hide');
  outBox.addEventListener('transitionend', function animationFoo(evn){
    if (evn.target.classList.contains('ed2-hide')) {
      evn.target.href = items[nextToAdd].link;
      var outLogo = evn.target.getElementsByTagName('img')[0];
      outLogo.id = items[nextToAdd].name;
      outLogo.src = items[nextToAdd].image;
      outBox.classList.remove('ed2-hide');
      evn.target.removeEventListener('transitionend', animationFoo);
      nextToChange++;
      nextToAdd++;
      if (nextToChange === 4) {
        nextToChange = 0;
      }
      if (nextToAdd === items.length) {
        nextToAdd = 0;
      }
    }
  });
};

var interval = setInterval(changeLogo, 3000);

manufacturers_slider.addEventListener('mouseover', function(){
  clearInterval(interval);
});

manufacturers_slider.addEventListener('mouseout', function(){
  interval = setInterval(changeLogo, 3000);
});

{/literal}
</script>
