this.button =

	_name: null

	init: (name) ->

		# Save name
		button._name = name

	set: (type, price, currencySymbol, currencyPosition) ->

		$(button._name).removeClass 'error'

		if currencyPosition is '0'
			preCurrencySymbol	= currencySymbol
			afterCurrencySymbol	= ''
		else
			preCurrencySymbol	= ''
			afterCurrencySymbol	= currencySymbol

		if type is 'download'

			# Build download button
			html =	"""
					<span class="icon ion-arrow-down-a"></span>Download
					"""

		else

			# Build buy button
			html =	"""
					<span class="icon ion-pricetag"></span>Buy #{ type } for #{ preCurrencySymbol }#{ price }#{ afterCurrencySymbol }
					"""

		# Add content
		$(button._name).html html

		# Set click
		$(button._name).off 'click'
		switch type
			when 'album' then $(button._name).on 'click', -> button.getLink('album', content.data.album, null)
			when 'photo' then $(button._name).on 'click', -> button.getLink('photo', null, content.data.photo)
			when 'download' then $(button._name).on 'click', -> button.getLink('download', content.data.album, content.data.photo)

		# Show button
		$(button._name).show()

	getLink: (type, album, photo) ->

		switch type

			when 'album'

				$(button._name).html 'Loading ...'
				frontend.api false, "getPayPalLink&albumID=#{ album.id }", (data) -> button.openLink data

			when 'photo'

				$(button._name).html 'Loading ...'
				frontend.api false, "getPayPalLink&photoID=#{ photo.id }", (data) -> button.openLink data

			when 'download'

				if album?.id? and photo?.id? then window.location.href = "php/api.php?function=getPhotoArchive&photoID=#{ photo.id }" # Download photo
				else if album?.id? then window.location.href = "php/api.php?function=getAlbumArchive&albumID=#{ album.id }" # Download album
				else

					# Missing albumID and photoID
					$(button._name)
						.addClass 'error'
						.html 'Error...'

	openLink: (data) ->

		expression	= /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi
		regex		= new RegExp(expression)

		if data.match(regex)

			# Open link
			window.location.href = data

		else

			# Not a link
			console.error "Returned data is not a link: #{ data }"
			$(button._name)
				.addClass 'error'
				.html 'Error...'