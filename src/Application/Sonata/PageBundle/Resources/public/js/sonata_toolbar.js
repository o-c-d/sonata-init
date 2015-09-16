$(function() {
    console.log( "ready!" );
	$( ".sonata-front-toolbar-toggler" ).click(function() {
	  $( ".sonata-front-toolbar" ).toggle( "slow", function() {
		// Animation complete.
	  });
	});

});
