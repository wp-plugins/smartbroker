sb_blueimp.Gallery(
		document.getElementById("sb_links").getElementsByTagName("a"),
			{
			container: "#sb_blueimp-gallery-carousel",
			carousel: true,
			stretchImages: 'cover',
			transitionSpeed: 800,
			urlProperty: 'href',
			onslide: function (index, slide) {
				var text = this.list[index].getAttribute('data-description');
				var	node = this.container.find('.sb_featured_description');
				node.empty();
				if (text) {
					node[0].appendChild(document.createTextNode(text));
					}
				var text = this.list[index].getAttribute('data-link'), node = this.container.find('.sb_slide_title');
				node.empty();
				if (text) {
					node[0].setAttribute('data-link',text);
					}
				}
			}			
		);
		
 jQuery(document).ready(function($){
	$('body').on('click', '.sb_featured_description', function(){
		var link = $('.sb_slide_title').attr('data-link');
		window.location.href = link.replace(/&amp;/g, '&');;
		});
	
	});