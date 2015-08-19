var slider = document.getElementById('slider');
var Logo = function(item) {
  var logoBox = document.createElement('a');
  slider.appendChild(logoBox);
  logoBox.href = item.link;

  var logo = document.createElement('img');
  logoBox.appendChild(logo);
  logo.id = item.name;
  logo.src = item.image;
};

var initLogos = function() {
  if (screen.width > 480) {
    for (i = 0; i < 4; i++) {
      new Logo(items[i]);
    }
  } else {
    new Logo(items[0]);
  }
}();

var nextToChange = 0;
var nextToAdd = 4;
var changeLogo = function() {
  var outBox = slider.getElementsByTagName('a')[nextToChange];
  outBox.classList.add('hide');
  outBox.addEventListener('transitionend', function animationFoo(evn){
    if (evn.target.classList.contains('hide')) {
      evn.target.href = items[nextToAdd].link;
      var outLogo = evn.target.getElementsByTagName('img')[0];
      outLogo.id = items[nextToAdd].name;
      outLogo.src = items[nextToAdd].image;
      outBox.classList.remove('hide');
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

slider.addEventListener('mouseover', function(){
  clearInterval(interval);
});

slider.addEventListener('mouseout', function(){
  interval = setInterval(changeLogo, 3000);
});
