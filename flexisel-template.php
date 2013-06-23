<ul id="flexisel"> 
<?php if ($post_imagesL) :
	foreach ($post_imagesL as $a) : ?>
		<li><img src="<?php echo wp_get_attachment_url($a->ID) ?>" /></li>
	<?php endforeach;
endif; ?>                                                        
</ul>
<div class="clearout"></div>
<script type="text/javascript">
jQuery(window).load(function() {
jQuery("#flexisel").flexisel({
        visibleItems: 5,
        animationSpeed: 1000,
        autoPlay: true,
        autoPlaySpeed: 3000,            
        pauseOnHover: true,
        enableResponsiveBreakpoints: true,
        responsiveBreakpoints: { 
            portrait: { 
                changePoint:480,
                visibleItems: 1
            }, 
            landscape: { 
                changePoint:640,
                visibleItems: 2
            },
            tablet: { 
                changePoint:768,
                visibleItems: 3
            }
        }
    });
});
</script>