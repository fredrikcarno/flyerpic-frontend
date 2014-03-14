this.content =

	load:

		album: (id) ->

			miniLychee.api "getAlbum&albumID=#{ id }", (data) ->

				html = ''

				$.each data.content, (index, value) ->
					html += content.build.photo value

				$('#content').html html

				content.display.album()

		photo: (id) ->

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
			$('header #about').show()
			$('header #close').hide()

		photo: ->

			if $('#view.fadeIn').length is 0

				# Show view
				$('#view')
					.show()
					.removeClass 'fadeOut'
					.addClass 'fadeIn'

			$('header #buy .type').html 'photo'
			$('header #about').hide()
			$('header #close').show()

	build:

		photo: (data) ->

			"""
			<img class="photo fadeIn" src="#{ miniLychee.lychee }uploads/thumb/#{ data.thumbUrl }" width="200" height="200" onClick="window.content.load.photo(#{ data.id })">
			"""