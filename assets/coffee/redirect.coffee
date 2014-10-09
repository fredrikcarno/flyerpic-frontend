this.frontend =

	data:
		helpmail: 'info@flyerpic.com'
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
			code? and
			type is 'mail'

				# Save to data
				frontend.data.type = type
				frontend.data.code = code

				# Show that session is not yet available
				frontend.mail.enter()
				return true

		else if	type? and
				code? and
				type is 'redirect'

					# Save to data
					frontend.data.type = type
					frontend.data.code = code

					# Redirect to store
					frontend.redirect code

		else

			# Show code input
			frontend.code.enter()
			return true

	code:

		enter: ->

			modal.show
				body:	"""
						<h1>Enter Code</h1>
						<p>Please enter the code from your flyer into the box below. <a href="mailto:#{ frontend.data.helpmail }">Need help?</a></p>
						<input class="text" type="text" placeholder="Your Code" data-name="code" autocapitalize="off" autocorrect="off" autofocus>
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

			code = data.code

			if	not code? or
				code is ''

					# Invalid code
					modal.error 'code'
					return false

			# Lookup code from database
			frontend.api 'getCode&code=' + encodeURI(code), (data) ->

				if	not data? or
					data is false

						# Invalid code
						modal.error 'code'
						return false

				if data is 'Warning: Album empty'

					# Code not found
					# Enter mail and get notified when the session is uploaded
					frontend.data.code = code
					frontend.mail.enter()
					return false

				window.location.href = "index.html##{ data }"

	redirect: (code) ->

		# Lookup code from database
		frontend.api 'getCode&code=' + encodeURI(code), (data) ->

			if	not data? or
				data is false

					# Show modal
					# Show that session is not yet available
					frontend.mail.enter()
					return true

			if data is 'Warning: Album empty'

				# Code not found
				# Enter mail and get notified when the session is uploaded
				frontend.data.code = code
				frontend.mail.enter()
				return false

			window.location.href = "index.html##{ data }"

	mail:

		enter: ->

			modal.show
				body:	"""
						<h1>Flyerpic</h1>
						<p>Your photos are not available, yet. The photographer may need some more time to process them. You can enter your e-mail below and we will notify you when your photos are ready! <a href="mailto:#{ frontend.data.helpmail }">Need help?</a></p>
						<input class="text" type="email" placeholder="Your E-Mail" data-name="mail" autofocus>
						"""
				closable: false
				class: 'login'
				buttons:
					action:
						title: 'Notify me'
						fn: frontend.mail.set

		set: (data) ->

			mail = data.mail

			if	not mail? or
				mail is ''

					# Invalid mail
					modal.error 'mail'
					return false

			# Store mail in database
			frontend.api 'setMail&mail=' + encodeURI(mail) + '&code='+ encodeURI(frontend.data.code), (data) ->

				if	not data? or
					data is false

						# Invalid mail
						modal.error 'mail'
						return false

				# Show dialog that the customer will be notified
				frontend.mail.confirm mail

		confirm: (mail) ->

			mail = mail.replace "'", "&apos;"

			modal.show
				body:	"""
						<p>Perfect! We will send a mail to '#{ mail }' when your photos are ready. <a href="mailto:#{ frontend.data.helpmail }">Need help?</a></p>
						"""
				closable: false
				buttons:
					action:
						title: 'Enter a new code'
						fn: -> window.location.href = 'redirect.html'

	error: (errorThrown, params, data) ->

		console.error "Error Description: #{ errorThrown }"
		console.error "Error Params: #{ params }"
		console.error "Server Response: #{ data }"

$(document).ready ->

	# Load content
	frontend.load document.location.hash

	# Keyboard shortcuts
	$(document).keyup (e) ->
		if e.keyCode is 13 then modal.action()