$(function() {
    console.log( "ready!" );
	$(".pyramid-wall").hover(function() {
		$(this).addClass('hovered');
	}, function() {
		$(this).removeClass('hovered');
	});
	/*front*/
	$(".pyramid_front_link").hover(function() {
		$(".pyramid-wall.front").addClass('hovered');
	}, function() {
		$(".pyramid-wall.front").removeClass('hovered');
	});
	/*back*/
	$(".pyramid_back_link").hover(function() {
		$(".pyramid-axis").css({ 'transform': 'rotateY(' + 180 + 'deg)'});
		$(".pyramid-wall.back").addClass('hovered');
	}, function() {
		$(".pyramid-axis").css({ 'transform': 'rotateY(' + 0 + 'deg)'});
		$(".pyramid-wall.back").removeClass('hovered');
	});
	/*bottom*/
	$(".pyramid_bottom_link").hover(function() {
		$(".gizeh").css({ 'top': '10px'});
		$(".pyramid").css({ 'transform': 'rotateX(' + 90 + 'deg)'});
		$(".pyramid-wall.bottom").addClass('hovered');
		$(".pyramid-shadow").css({ 'top': '260px'});
		console.log( "bottom in!" );
	}, function() {
		$(".gizeh").css({ 'top': '0px'});
		$(".pyramid").css({ 'transform': 'rotateX(' + 0 + 'deg)'});
		$(".pyramid-wall.bottom").removeClass('hovered');
		$(".pyramid-shadow").css({ 'top': '250px'});
		console.log( "bottom out!" );
	});
	/*left*/
	$(".pyramid_left_link").hover(function() {
		$(".pyramid-axis").css({ 'transform': 'rotateY(' + 90 + 'deg)'});
		$(".pyramid-wall.left").addClass('hovered');
	}, function() {
		$(".pyramid-axis").css({ 'transform': 'rotateY(' + 0 + 'deg)'});  
		$(".pyramid-wall.left").removeClass('hovered');

	});
	/*right*/
	$(".pyramid_right_link").hover(function() {
		$(".pyramid-axis").css({ 'transform': 'rotateY(' + -90 + 'deg)'});
		$(".pyramid-wall.right").addClass('hovered');
	}, function() {
		$(".pyramid-axis").css({ 'transform': 'rotateY(' + 0 + 'deg)'});
		$(".pyramid-wall.right").removeClass('hovered');
	});
});
