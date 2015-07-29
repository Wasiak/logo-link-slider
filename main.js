var slider = document.getElementById('slider');

var Logo = function(item) {
  var logoBox = document.createElement('a');
  slider.appendChild(logoBox);
  logoBox.href = item.link;

  var logo = document.createElement('img');
  logoBox.appendChild(logo);
  logo.id = item.name;
  logo.src = item.image;
}

var items = [
  {'name' : 'name1', 'image' : 'images/01.png', 'link' : 'http://www.legionisci.com'},
  {'name' : 'name2', 'image' : 'images/02.png', 'link' : 'http://www.legionisci.com'},
  {'name' : 'name3', 'image' : 'images/03.png', 'link' : 'http://www.legionisci.com'},
  {'name' : 'name4', 'image' : 'images/04.png', 'link' : 'http://www.legionisci.com'},
  {'name' : 'name5', 'image' : 'images/05.png', 'link' : 'http://www.legionisci.com'},
  {'name' : 'name6', 'image' : 'images/06.png', 'link' : 'http://www.legionisci.com'},
  {'name' : 'name7', 'image' : 'images/07.png', 'link' : 'http://www.legionisci.com'},
  {'name' : 'name8', 'image' : 'images/08.png', 'link' : 'http://www.legionisci.com'},
  {'name' : 'name9', 'image' : 'images/09.png', 'link' : 'http://www.legionisci.com'},
  {'name' : 'name10', 'image' : 'images/10.png', 'link' : 'http://www.legionisci.com'}
];

var initLogos = function() {
  for (i = 0; i < 4; i++) {
    new Logo(items[i]);
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
})
slider.addEventListener('mouseout', function(){
  interval = setInterval(changeLogo, 3000);
})
