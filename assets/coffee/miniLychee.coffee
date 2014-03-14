this.miniLychee =

	lychee: ''

	load: (url) ->

		hash = url.replace('#', '').split('/')

		if hash[0]? then albumID = hash[0]
		if hash[1]? then photoID = hash[1]

		if albumID? and photoID?

			# Load
			content.load.album albumID
			content.load.photo photoID

		else if albumID?

			# Load album
			content.load.album albumID

		else

			# Show error
			alert 'NO CODE'

	api: (params, callback) ->

		$.ajax
			type: 'POST'
			url: "#{ miniLychee.lychee }php/api.php"
			data: "function=#{ params }"
			dataType: 'text'

			success: (data) ->

				if typeof data is 'string' and data.substring(0, 6) is 'Error:'
					miniLychee.error data.substring(7, data.length), params, data
					return false

				# Parse boolean
				if data is '1' then data = true
				if data is '' then data = false

				if	typeof data is 'string' and
					data.substring(0, 1) is '{' and
					data.substr(-1) is '}'

						# Parse JSON
						data = $.parseJSON data

				callback data
				return true

			error: (jqXHR, textStatus, errorThrown) ->

				miniLychee.error 'Server error or API not found.', params, errorThrown
				return false

	error: (errorThrown, params, data) ->

		console.error "Error Description: #{ errorThrown }"
		console.error "Error Params: #{ params }"
		console.error "Server Response: #{ data }"