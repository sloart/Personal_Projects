particlesJS("particles-js", {"particles":{"number":{"value":24,
            "density":{"enable":false,"value_area":800}},"color":{"value":"#2a2226"},
        "shape":{"type":"circle","stroke":{"width":0,"color":"#2a2226"},"polygon":{"nb_sides":8},
            "image":{"src":"","width":100,"height":100}},"opacity":{"value":0.41825485136169543,
            "random":true,"anim":{"enable":true,"speed":1,"opacity_min":0,"sync":false}},
        "size":{"value":11.83740145363289,"random":true,"anim":{"enable":false,
                "speed":4,"size_min":0.3,"sync":false}},
        "line_linked":{"enable":true,"distance":284.0976348871894,"color":"#2a2226",
            "opacity":0.14994041841268327,"width":0.6313280775270874},
        "move":{"enable":true,"speed":0.5,"direction":"none","random":true,"straight":false,
            "out_mode":"bounce","bounce":false,
            "attract":{"enable":false,"rotateX":600,"rotateY":600}}},
    "interactivity":{"detect_on":"window","events":{"onhover":{"enable":true,"mode":"grab"},
            "onclick":{"enable":true,"mode":"repulse"},"resize":true},
        "modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},
            "bubble":{"distance":250,"size":0,"duration":2,"opacity":0,"speed":3},
            "repulse":{"distance":400,"duration":0.4},"push":{"particles_nb":4},
            "remove":{"particles_nb":2}}},"retina_detect":true});

var count_particles, stats, update; stats = new Stats; stats.setMode(0);
stats.domElement.style.position = 'absolute'; stats.domElement.style.left = '0px';
stats.domElement.style.top = '0px'; document.body.appendChild(stats.domElement);
count_particles = document.querySelector('.js-count-particles');
update = function() { stats.begin(); stats.end();
    if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) { count_particles.innerText = window.pJSDom[0].pJS.particles.array.length; } requestAnimationFrame(update); }; requestAnimationFrame(update);;