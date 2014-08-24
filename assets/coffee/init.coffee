$(document).ready ->

	$('header #close').on 'click', content.display.album

	# Init button
	button.init '#buy'

	# Close with ESC Key
	$(document).keyup (e) ->
		if e.keyCode is 27 then $('header a#close').click()

	# Get location of Lychee
	miniLychee.api false, 'getLychee', (data) ->

		miniLychee.master = data
		miniLychee.load document.location.hash