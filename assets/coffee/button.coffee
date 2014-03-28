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

		# Build
		html =	"""
				<span class="icon ion-pricetag"></span>Buy #{ type } for #{ preCurrencySymbol }#{ price }#{ afterCurrencySymbol }
				"""

		# Add content
		$(button._name).html html

		# Set click
		$(button._name).off 'click'
		if type is 'album' then $(button._name).on 'click', -> button.getLink('album', content.data.album.id)
		if type is 'photo' then $(button._name).on 'click', -> button.getLink('photo', content.data.photo.id)

		# Show button
		$(button._name).show()

	getLink: (type, id) ->

		$(button._name).html 'Loading ...'

		if type is 'album'

			miniLychee.api false, "getPayPalLink&albumID=#{ id }", (data) -> window.location.href = data

		else if type is 'photo'

			miniLychee.api false, "getPayPalLink&photoID=#{ id }", (data) -> window.location.href = data