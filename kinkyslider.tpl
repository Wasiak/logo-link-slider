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
  if (screen.width >= 767) {
    for (i = 0; i < 4; i++) {
      new Logo(items[i]);
      manufacturers_slider.classList.remove('tablet');
      manufacturers_slider.classList.remove('mobile');
    }
  } else if (screen.width > 480 && screen.width < 768){
      for (i = 0; i < 2; i++) {
        new Logo(items[i]);
        manufacturers_slider.classList.remove('mobile');
        manufacturers_slider.classList.add('tablet');
      }
  } else {
    new Logo(items[0]);
    manufacturers_slider.classList.remove('tablet');
    manufacturers_slider.classList.add('mobile');
  }
};

var nextToChange = 0;
var nextToAdd = 4;
var mobileNextAdd = function() {
  if (manufacturers_slider.classList.contains('mobile')) {
    nextToAdd = 1;
  } else if (manufacturers_slider.classList.contains('tablet')) {
    nextToAdd = 2;
  } else {
    nextToAdd = 4;
  }
}();

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
      if (manufacturers_slider.classList.contains('mobile')) {
        nextToChange = 0;
      } else {
        nextToChange++;
      }
      nextToAdd++;
      if (nextToChange === 2 && manufacturers_slider.classList.contains('tablet')) {
        nextToChange = 0;
      }
      if (nextToChange === 4) {
        nextToChange = 0;
      }
      if (nextToAdd === items.length) {
        nextToAdd = 0;
      }
    }
  });
};

var addEvent = function() {
  window.addEventListener('resize', initLogos);
  window.addEventListener('resize', mobileNextAdd);
}();

var interval = setInterval(changeLogo, 3000);

manufacturers_slider.addEventListener('mouseover', function(){
  clearInterval(interval);
});

manufacturers_slider.addEventListener('mouseout', function(){
  interval = setInterval(changeLogo, 3000);
});

initLogos();
{/literal}
</script>
