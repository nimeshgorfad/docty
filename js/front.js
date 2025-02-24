jQuery(document).ready(function($){
	
	//alert('hiii');
	/*
	navContainer: '.main-content-owl .custom-nav-owl',
			navText: [
				'<i class="fa fa-angle-left" aria-hidden="true"></i>',
				'<i class="fa fa-angle-right" aria-hidden="true"></i>'
			],
	*/
	
	if( jQuery('#doctor-carousel').length > 0 ){
		
		jQuery('#doctor-carousel').owlCarousel({
			loop: true,
			margin: 30,
			dots: true,
			nav: true,
			items: 4,
			autoplay:true,
			autoplayTimeout:5000,
			responsive:{
				0:{
					items: 1
				},
				600:{
					items: 3
				},
				1000:{
					items: 4
				}
			}
		})

	}
	
});