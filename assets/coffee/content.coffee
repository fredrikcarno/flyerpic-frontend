this.content =

	data:
		user: null
		album: null
		photo: null

	load:

		user: (userID) ->

			frontend.api false, "getUser&userID=#{ userID }", (data) ->

				# Save data
				content.data.user = data

				# Set about
				$('header img#logo').attr 'src', data.avatar
				$('header #name').html data.name

				###
				# Set button
				###

				if	content.data.album?.description? and
					content.data.album.description.indexOf('payed') isnt -1

						# Show download button
						button.set 'download'
						return true

				else if content.data.album.description.indexOf('payed') is -1

					# Show buy button for album
					button.set 'album', data.priceperalbum, data.currencysymbol, data.currencyposition

				if	content.data.photo?.tags? and
					content.data.photo.tags.indexOf('payed') isnt -1

						# Show download button
						button.set 'download'
						return true

				else if content.data.photo.tags.indexOf('payed') is -1

					# Show buy button for album
					button.set 'photo', data.priceperphoto, data.currencysymbol, data.currencyposition


		album: (albumID) ->

			frontend.api false, "getAlbum&albumID=#{ albumID }&password=", (data) ->

				# Catch unknown albumID error
				if data is 'Error: Album title not found'

					# Redirect to redirect.html where the user can enter his mail
					window.location.href = "redirect.html#mail/#{ albumID }"

				# Save data
				content.data.album = data

				# Build content
				html = ''

				$.each data.content, (index, value) ->
					html += content.build.photo value

				# Add content
				$('#content').html html

				# Show album
				content.display.album()

				# Load user
				content.load.user data.userID

		photo: (albumID, photoID) ->

			frontend.api true, "getPhoto&photoID=#{ albumID }&albumID=#{ albumID }&password=", (data) ->

				# Save data
				content.data.photo = data

				# Build and add content
				$('#view').html content.build.image(data)

				# Show photo
				content.display.photo()

		payment: (albumID, photoID, status) ->

			switch status

				when 'unverified'

					###
					# Status:		unverified
					# Description:	The payment was not successful, because of a PayPal error or canceled payment.
					#				The customer still needs to purchase the album/photo.
					###

					modal.show
						body:	"""
								<p>Ups! Your purchase was not successful and we can not unlock your photos. Please contact the support with this message.</p>
								"""
						closable: true
						buttons:
							cancel:
								title: 'Cancel'
								fn: -> modal.close()
							action:
								title: 'Contact support'
								color: 'normal'
								icon: 'ion-help-circled'
								fn: ->
									window.location.href = "mailto:#{ content.data.user.helpmail }"
									modal.close()

				when 'locked'

					###
					# Status:		locked
					# Description:	The payment was successful, but the album/photos could not be marked as paid.
					#				The customer still sees the watermarked photos and should contact the support.
					###

					modal.show
						body:	"""
								<p>Ups! Your purchase was successful, but we could not unlock your photos. Please contact the support with this message.</p>
								"""
						closable: true
						buttons:
							cancel:
								title: 'Cancel'
								fn: -> modal.close()
							action:
								title: 'Contact support'
								color: 'normal'
								icon: 'ion-help-circled'
								fn: ->
									window.location.href = "mailto:#{ content.data.user.helpmail }"
									modal.close()

				when 'success'

					###
					# Status:		success
					# Description:	The payment was successful and the customer now sees the unwatermarked photos.
					#				A dialog will show up, prompting the customer to download the album/photo.
					###

					modal.show
						body:	"""
								<p>Congratulation! You can now download and use your photos wherever and how often you want.</p>
								"""
						closable: true
						buttons:
							cancel:
								title: 'Cancel'
								fn: -> modal.close()
							action:
								title: 'Download your photos'
								color: 'normal'
								icon: 'ion-arrow-down-a'
								fn: ->
									content.load.download albumID, photoID
									modal.close()

				else

					###
					# Status:		-
					# Description:	An unknown error happened.
					#				A dialog will show up, prompting the customer to contact the support.
					###

					modal.show
						body:	"""
								<p>Ups! Something went wrong and we do not know what it is. Please contact the support with this message.</p>
								"""
						closable: true
						buttons:
							cancel:
								title: 'Cancel'
								fn: -> modal.close()
							action:
								title: 'Contact support'
								color: 'normal'
								icon: 'ion-help-circled'
								fn: ->
									window.location.href = "mailto:#{ content.data.user.helpmail }"
									modal.close()

		download: (albumID, photoID) ->

			if	(albumID? and albumID isnt '') and
				(not photoID? or photoID is '')

					# User purchased album
					window.location.href = "php/api.php?function=getAlbumArchive&albumID=#{ albumID }"
					return true

			if	(albumID? and albumID isnt '') and
				(photoID? and photoID isnt '')

					# User purchased photo
					window.location.href = "php/api.php?function=getPhotoArchive&photoID=#{ photoID }"
					return true

			# Missing params -> Show error
			modal.show
				body:	"""
						<p>Ups! Something went wrong and we do not know what it is. Please contact the support with this message.</p>
						"""
				closable: true
				buttons:
					cancel:
						title: 'Cancel'
						fn: -> modal.close()
					action:
						title: 'Contact support'
						color: 'normal'
						icon: 'ion-help-circled'
						fn: ->
							window.location.href = "mailto:#{ content.data.user.helpmail }"
							modal.close()

	display:

		album: ->

			if	($('#view.fadeIn').length is 1 and $('#view.fadeOut').length is 0) or
				$('#view.fadeIn #view.fadeOut').length isnt 0

					# Hide view
					$('#view')
						.removeClass 'fadeIn'
						.addClass 'fadeOut'

					setTimeout ->
						$('#view').hide()
					, 300

			$('header #about, header menu').show()
			$('header #close').hide()

			# Change button
			if content.data.user?

				if	content.data.album?.description? and
					content.data.album.description.indexOf('payed') isnt -1

						# Show download button
						button.set 'download'

				else

					# Show buy button
					button.set 'album', content.data.user.priceperalbum, content.data.user.currencysymbol, content.data.user.currencyposition

		photo: ->

			if $('#view.fadeIn').length is 0

				# Show view
				$('#view')
					.show()
					.removeClass 'fadeOut'
					.addClass 'fadeIn'

			$('header #about, header menu').hide()
			$('header #close').show()

			# Change button
			if content.data.user?
				if	content.data.photo?.tags? and
					content.data.photo.tags.indexOf('payed') isnt -1

						# Show download button
						button.set 'download'

				else

					# Show buy button
					button.set 'photo', content.data.user.priceperphoto, content.data.user.currencysymbol, content.data.user.currencyposition

	build:

		photo: (data) ->

			"""
			<img class="photo fadeIn" src="#{ frontend.master }#{ data.thumbUrl }" width="200" height="200" onClick="window.content.load.photo(#{ data.id })">
			"""

		image: (data) ->

			"""
			<div id="image" style="background-image: url('#{ frontend.master }#{ data.url }')"></div>
			"""