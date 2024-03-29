this.frontend =

	master: ''

	load: (url) ->

		hash = url.replace('#', '').split('/')

		if hash[0]? and hash[0] isnt '' then albumID = hash[0]
		if hash[1]? then photoID = hash[1]
		if hash[2]? then status = hash[2]

		if albumID? and photoID? and status?

			###
			# After payment
			###

			# Show header
			$('header').addClass 'show'

			# Load
			content.load.album albumID

			# Show modal
			content.load.payment albumID, photoID, status

			return true

		else if albumID? and photoID? and photoID not ''

			# Show header
			$('header').addClass 'show'

			# Load
			content.load.album albumID
			content.load.photo albumID, photoID
			return true

		else if albumID?

			# Show header
			$('header').addClass 'show'

			# Load album
			content.load.album albumID
			return true

		else

			# AlbumID missing
			# Redirect to redirect.html where the user can enter his code
			window.location.href = 'redirect.html'
			return false

	api: (external, params, callback) ->

		root = if external is true then frontend.master else ''

		$.ajax
			type: 'POST'
			url: "#{ root }php/api.php"
			data: "function=#{ params }"
			dataType: 'text'

			success: (data) ->

				console.log data + ':' + params

				# Parse boolean
				if data is '1' then data = true
				if data is '' then data = false

				# Parse error
				if typeof data is 'string' and data.substring(0, 6) is 'Error:'
					frontend.error data.substring(7, data.length), params, data

				# Parse JSON
				if	typeof data is 'string' and
					data.substring(0, 1) is '{' and
					data.substr(-1) is '}'

						# Parse JSON
						data = $.parseJSON data

				callback data
				return true

			error: (jqXHR, textStatus, errorThrown) ->

				frontend.error 'Server error or API not found.', params, errorThrown
				return false

	error: (errorThrown, params, data) ->

		console.error "Error Description: #{ errorThrown }"
		console.error "Error Params: #{ params }"
		console.error "Server Response: #{ data }"