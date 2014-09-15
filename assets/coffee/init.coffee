$(document).ready ->

	# Close button
	$('header #close').on 'click', ->
		content.data.photo.id = null
		content.display.album()

	# Keyboard shortcuts
	$(document).keyup (e) ->
		if e.keyCode is 27 then $('header a#close').click()
		else if e.keyCode is 13 then $('.modalContainer #action').addClass('active').click()

	# Init button
	button.init '#buy'

	# Get location of Lychee
	frontend.api false, 'getLychee', (data) ->

		frontend.master = data
		frontend.load document.location.hash