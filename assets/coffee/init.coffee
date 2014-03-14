$(document).ready ->

	$('header #close').on 'click', content.display.album

	# Get location of Lychee
	miniLychee.api 'getLychee', (data) ->

		miniLychee.lychee = data
		miniLychee.load document.location.hash