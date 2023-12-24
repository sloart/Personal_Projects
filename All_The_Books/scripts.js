

// When the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get all images with the class 'book-cover'
    var covers = document.querySelectorAll('.book-cover');
    
    // Add event listeners to each cover
    // covers.forEach(function(cover) {
    //     cover.addEventListener('mouseover', function() { bigImg(this); });
    //     cover.addEventListener('mouseout', function() { normalImg(this); });
    // });
});

// function bigImg(x) {
//   x.style.transform = "scale(1.1,1.1)";

// }

// function normalImg(x) {
//   x.style.transform = "scale(1,1)";
// }