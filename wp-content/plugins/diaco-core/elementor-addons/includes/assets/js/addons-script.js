(function ($) {
    "use strict";
    var diacoserviceslider = function ($scope, $) {
        // Four Item Carousel
            if ($('.four-item-carousel').length) {
                $('.four-item-carousel').owlCarousel({
                    loop:true,
                    margin:0,
                    nav:true,
                    autoHeight: true,
                    smartSpeed: 500,
                    autoplay: 5000,
                    navText: [ '<span class="flaticon-slim-left"></span>', '<span class="flaticon-slim-right"></span>' ],
                    responsive:{
                        0:{
                            items:1
                        },
                        600:{
                            items:2
                        },
                        800:{
                            items:2
                        },
                        1024:{
                            items:3
                        },
                        1200:{
                            items:4
                        }
                    }
                });    		
            }


        //Three Item Carousel
            if ($('.three-item-carousel').length) {
                $('.three-item-carousel').owlCarousel({
                    loop:true,
                    margin:0,
                    nav:true,
                    smartSpeed: 500,
                    autoplay: 5000,
                    navText: [ '<span class="flaticon-slim-left"></span>', '<span class="flaticon-slim-right"></span>' ],
                    responsive:{
                        0:{
                            items:1
                        },
                        600:{
                            items:1
                        },
                        800:{
                            items:2
                        },
                        1024:{
                            items:2
                        },
                        1200:{
                            items:3
                        }
                    }
                });    		
            }

    }

    var WorkTabSlider = function ($scope, $) {

        // work-carousel
            if ($('.work-slider').length) {
                $('.work-slider').owlCarousel({
                    loop:true,
                    margin:6,
                    items:1,
                    autoplayTimeout: 5000,
                    autoplay: true,
                    navText: [ '<span class="flaticon-slim-left"></span>', '<span class="flaticon-slim-right"></span>' ],
                    navSpeed: 500,
                });    		
            }
    }

    var diacoprojectslider = function ($scope, $) {
        // project-carousel
            if ($('.project-carousel').length) {
                $('.project-carousel').owlCarousel({
                    loop:true,
                    margin:0,
                    nav:true,
                    smartSpeed: 500,
                    autoplay: 5000,
                    navText: [ '<span class="flaticon-left-arrow-2"></span>', '<span class="flaticon-right-arrow-4"></span>' ],
                    responsive:{
                        0:{
                            items:1
                        },
                        600:{
                            items:2
                        },
                        800:{
                            items:3
                        },
                        1024:{
                            items:4
                        },
                        1200:{
                            items:4
                        },
                        1600:{
                            items:5
                        },
                        1920:{
                            items:6
                        }
                    }
                });  		
            }
    }

    var Testimonial = function ($scope, $) {
            if ($('.two-item-carousel').length) {
                $('.two-item-carousel').owlCarousel({
                    loop:true,
                    margin:30,
                    nav:true,
                    autoHeight: true,
                    smartSpeed: 500,
                    autoplay: 5000,
                    navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
                    responsive:{
                        0:{
                            items:1
                        },
                        600:{
                            items:2
                        },
                        800:{
                            items:2
                        },
                        1024:{
                            items:2
                        },
                        1200:{
                            items:2
                        }
                    }
                });    		
            }
    }

    var Team = function ($scope, $) {
        // team-carousel
            if ($('.team-carousel').length) {
                $('.team-carousel').owlCarousel({
                    loop:true,
                    margin:30,
                    nav:true,
                    autoHeight: true,
                    smartSpeed: 500,
                    autoplay: 5000,
                    navText: [ '<span class="flaticon-slim-left"></span>', '<span class="flaticon-slim-right"></span>' ],
                    responsive:{
                        0:{
                            items:1
                        },
                        600:{
                            items:2
                        },
                        800:{
                            items:3
                        },
                        1024:{
                            items:3
                        },
                        1200:{
                            items:4
                        }
                    }
                });    		
            }
    }

    var diacopartnerslogo = function ($scope, $) {
        // clients-carousel
            if ($('.clients-carousel').length) {
                $('.clients-carousel').owlCarousel({
                    loop:true,
                    margin:30,
                    nav:false,
                    smartSpeed: 3000,
                    autoplay: true,
                    navText: [ '<span class="fa fa-angle-left"></span>', '<span class="fa fa-angle-right"></span>' ],
                    responsive:{
                        0:{
                            items:1
                        },
                        480:{
                            items:2
                        },
                        600:{
                            items:3
                        },
                        800:{
                            items:4
                        },			
                        1200:{
                            items:5
                        }

                    }
                });    		
            }
    }

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/diacoserviceslider.default', diacoserviceslider);
        elementorFrontend.hooks.addAction('frontend/element_ready/WorkTabSlider.default', WorkTabSlider);
        elementorFrontend.hooks.addAction('frontend/element_ready/diacoprojectslider.default', diacoprojectslider);
        elementorFrontend.hooks.addAction('frontend/element_ready/Testimonial.default', Testimonial);
        elementorFrontend.hooks.addAction('frontend/element_ready/Team.default', Team);
        elementorFrontend.hooks.addAction('frontend/element_ready/diacopartnerslogo.default', diacopartnerslogo);
    });
})(jQuery);