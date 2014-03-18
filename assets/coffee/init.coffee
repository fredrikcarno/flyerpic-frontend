$(document).ready ->

	$('header #close').on 'click', content.display.album

	# Get location of Lychee
	miniLychee.api false, 'getLychee', (data) ->

		miniLychee.master = data
		miniLychee.load document.location.hash