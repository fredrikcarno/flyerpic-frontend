this.content =

	load:

		user: (userID) ->

			miniLychee.api false, "getUser&userID=#{ userID }", (data) ->

				# Set about
				$('header img#logo').attr 'src', "data/user/#{ userID }.png"
				$('header #name').html data.name

				# Set price
				$('header #buy .price').html data.priceperalbum

		album: (albumID) ->

			miniLychee.api true, "getAlbum&albumID=#{ albumID }&password=", (data) ->

				# Build content
				html = ''

				$.each data.content, (index, value) ->
					html += content.build.photo value

				# Add content
				$('#content').html html

				# Retina
				$('img').each ->
					new RetinaImage this

				# Show album
				content.display.album()

				# Load user
				content.load.user data.title.substr(0, 1)

		photo: (albumID, photoID) ->

			miniLychee.api true, "getPhoto&photoID=#{ albumID }&albumID=#{ albumID }&password=", (data) ->

				# Build and add content
				$('#view').html content.build.image(data)

				# Show photo
				content.display.photo()

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

			$('header #buy .type').html 'album'
			$('header #about, header menu').show()
			$('header #close').hide()

		photo: ->

			if $('#view.fadeIn').length is 0

				# Show view
				$('#view')
					.show()
					.removeClass 'fadeOut'
					.addClass 'fadeIn'

			$('header #buy .type').html 'photo'
			$('header #about, header menu').hide()
			$('header #close').show()

	build:

		photo: (data) ->

			"""
			<img class="photo fadeIn" src="#{ miniLychee.master }uploads/thumb/#{ data.thumbUrl }" width="200" height="200" onClick="window.content.load.photo(#{ data.id })">
			"""

		image: (data) ->

			"""
			<div id="image" style="background-image: url('#{ miniLychee.master }uploads/big/#{ data.url }')"></div>
			"""