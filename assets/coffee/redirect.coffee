frontend =

	data:
		type: null
		code: null

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
						<p>Please enter the code from your flyer below:</p>
						<input class="text" type="text" placeholder="Your Code" data-name="code">
						"""
				closable: false
				class: 'login'
				buttons:
					action:
						title: 'Notify me'
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
			# TODO

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

$(document).ready ->

	frontend.load document.location.hash