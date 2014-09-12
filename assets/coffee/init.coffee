$(document).ready ->

	# Close button
	$('header #close').on 'click', ->
		content.data.photo.id = null
		content.display.album()

	# Close with ESC Key
	$(document).keyup (e) ->
		if e.keyCode is 27 then $('header a#close').click()

	# Init button
	button.init '#buy'

	# Get location of Lychee
	frontend.api false, 'getLychee', (data) ->

		frontend.master = data
		frontend.load document.location.hash