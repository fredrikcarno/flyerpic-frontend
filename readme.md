# Flyerpic Frontend

The Frontend works together with Lychee and allows to show and sell albums or photos.

## Installation

Make sure the following tools are installed on your system:

- `node` [Node.js](http://nodejs.org) v0.10 or later
- `npm` [Node Packaged Modules](https://www.npmjs.org)

After [installing Node.js](http://nodejs.org) you can use the following commands to install the dependencies and build the Frontend:

	npm install -g bower coffee-script grunt-cli
	npm install
	
## Configuration

You can find and edit the configuration in `data/config.sample.php`. Duplicate and rename the copy to `config.php`.

## Data

The Frontend uses Lychee to receive its data. In order to work seamlessly together, the Frontend requires various information to be stored in the data of albums and photos in Lychee.

| Place | Description |
|:-----------|:------------|
| Album Title | The album title must start with the shorthand of the ID from the photographer which owns the album. The photographer with the ID `01` has the shorthand ID `ab`. The shorthand ID is documented in the documentation of the Backend. The shorthand must be 2 chars long. |
| Album Sharing | The album must be public, downloadable and not visible. |
| Photo Tags | 1) The watermarked photo must contain a `watermarked`-Tag. 2) A bought photo (including the watermarked-version) must contain `payed`-Tag. 3) Photos which belong together (normal and watermarked) must have one tag in common to identify the togetherness. |