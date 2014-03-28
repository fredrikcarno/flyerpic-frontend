$(document).ready ->

	$('header #close').on 'click', content.display.album

	# Init button
	button.init '#buy'

	# Get location of Lychee
	miniLychee.api false, 'getLychee', (data) ->

		miniLychee.master = data
		miniLychee.load document.location.hash