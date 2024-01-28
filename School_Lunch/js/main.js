function display_random_image() 
{
     var theImages = [{
        src: "./img/chicken_sandwich.png",
        width: "350"}, {
        src: "./img/chicken_sandwich_with_cookie.png",
        width: "350"}, {
        src: "./img/dumplings.png",
        width: "350"},{
        src: "./img/mashed_potatoes.png",
        width: "350"},{
        src: "./img/pizza.png",
        width: "350"},{
        src: "./img/more_pizza.png",
        width: "350"},{
        src: "./img/sesame_chicken.png",
        width: "350"},{
        src: "./img/turkey_sub.png",
        width: "350"},{
        src: "./img/turkey_wrap.png",
        width: "350"},{
        src: "./img/waffle.png",
        width: "350"}];
    
    var preBuffer = [];
    for (var i = 0, j = theImages.length; i < j; i++) {
        preBuffer[i] = new Image();
        preBuffer[i].src = theImages[i].src;
        preBuffer[i].width = theImages[i].width;
    }
   
// create random image number
  function getRandomInt(min,max) 
    {
      //  return Math.floor(Math.random() * (max - min + 1)) + min;
    
imn = Math.floor(Math.random() * (max - min + 1)) + min;
    return preBuffer[imn];
    }  

// 0 is first image, preBuffer.length - 1) is last image
  
var newImage = getRandomInt(0, preBuffer.length - 1);
 
// remove the previous images
var images = document.getElementsByTagName('img');
var l = images.length;
for (var p = 0; p < l; p++) {
    images[0].parentNode.removeChild(images[0]);
}
// display the image  
document.body.appendChild(newImage);
}
