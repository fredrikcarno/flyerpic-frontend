this.frontend =

	data:
		type: null
		code: null

	api: (params, callback) ->

		$.ajax
			type: 'POST'
			url: "php/api.php"
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
					return false

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

	load: (url) ->

		hash = url.replace('#', '').split('/')

		if hash[0]? then type = hash[0]
		if hash[1]? then code = hash[1]

		if	type? and
			hash? and
			type is 'mail'

				# Save to data
				frontend.data.type = type
				frontend.data.code = code

				# Show that session is not yet available
				frontend.mail.enter()
				return true

		else

			# Show code input
			frontend.code.enter()
			return true

	code:

		enter: ->

			modal.show
				body:	"""
						<h1>Enter Code</h1>
						<p>Please enter the code from your flyer into the box below. <a href="#">Need help?</a></p>
						<input class="text" type="text" placeholder="Your Code" data-name="code">
						"""
				closable: false
				class: 'login'
				buttons:
					action:
						title: 'Show my photos'
						color: 'normal'
						icon: ''
						fn: frontend.code.check

		check: (data) ->

			if	not data?.code? or
				data.code is ''

					# Invalid mail
					modal.error 'code'
					return false

			# Lookup code from database
			frontend.api 'getCode&code=' + encodeURI(data.code), (data) ->

				if	not data? or
					data is false

						# Invalid mail
						modal.error 'code'
						return false

				window.location.href = "index.html##{ data }"

	mail:

		enter: ->

			modal.show
				body:	"""
						<h1>Flyerpic</h1>
						<p>Your photos are not available, yet. The photographer may need some more time to process them. You can enter your e-mail below and we will notify you when your photos are ready!</p>
						<input class="text" type="text" placeholder="Your E-Mail" data-name="mail">
						"""
				closable: false
				class: 'login'
				buttons:
					action:
						title: 'Notify me'
						color: 'normal'
						icon: ''
						fn: frontend.mail.set

		set: (data) ->

			if	not data?.mail? or
				data.mail is ''

					# Invalid mail
					modal.error 'mail'
					return false

			# Store mail in database
			# TODO

	error: (errorThrown, params, data) ->

		console.error "Error Description: #{ errorThrown }"
		console.error "Error Params: #{ params }"
		console.error "Server Response: #{ data }"

$(document).ready ->

	# Load content
	frontend.load document.location.hash

	# Keyboard shortcuts
	$(document).keyup (e) ->
		if e.keyCode is 13 then $('.modalContainer #action').addClass('active').click()