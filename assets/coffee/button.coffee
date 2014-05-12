this.button =

	_name: null

	init: (name) ->

		# Save name
		button._name = name

	set: (type, price, currencySymbol, currencyPosition) ->

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
			when 'album' then $(button._name).on 'click', -> button.getLink('album', content.data.album.id)
			when 'photo' then $(button._name).on 'click', -> button.getLink('photo', content.data.photo.id)
			when 'download' then $(button._name).on 'click', -> button.getLink('download', content.data.album.id)

		# Show button
		$(button._name).show()

	getLink: (type, id) ->

		switch type

			when 'album'

				$(button._name).html 'Loading ...'
				miniLychee.api false, "getPayPalLink&albumID=#{ id }", (data) -> window.location.href = data

			when 'photo'

				$(button._name).html 'Loading ...'
				miniLychee.api false, "getPayPalLink&photoID=#{ id }", (data) -> window.location.href = data

			when 'download'

				link = 'http://google.de'
				window.location.href = link