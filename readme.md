# Flyerpic Frontend

The Frontend works together with Lychee and allows to show and sell albums or photos.

## Installation

Make sure the following tools are installed on your system:

- `php` v5.4 or later with `curl` activated
- `node` [Node.js](http://nodejs.org) v0.10 or later
- `npm` [Node Packaged Modules](https://www.npmjs.org)

After [installing Node.js](http://nodejs.org) you can use the following commands to install the dependencies and build the Frontend:

	npm install -g bower coffee-script grunt-cli
	npm install
	
## Configuration

You can find and edit the configuration in `data/config.sample.php`. Duplicate and rename the copy to `config.php`.

The `redirect.html` dialogs are always including a link to contact the store-holder. You can edit the mail in `assets/coffee/redirect.coffee`.

## Data

The Frontend uses Lychee to receive its data. In order to work seamlessly together, the Frontend requires various information to be stored in the data of albums and photos in Lychee.

| Place | Description |
|:-----------|:------------|
| Album Title | The album title must start with the shorthand of the ID from the photographer which owns the album. The photographer with the ID `01` has the shorthand ID `ab`. The shorthand ID is documented in the documentation of the Backend. The shorthand must be 2 chars long. |
| Album Sharing | The album must be public, downloadable and not visible. |
| Album Description | The album description must be `payed` when the album was purchased by a customer. |
| Album Photo Count | The album must at least contain two photos (normal + watermarked version) to be detected as available for the customer. Otherwise he will be prompted to enter his email. |
| Photo Tags | 1) Photos which belong together (normal and watermarked) must have one tag in common to identify the togetherness. This must be the first tag.<br> 2) The watermarked photo must contain a `watermarked`-Tag.<br> 3) A bought photo (including the watermarked-version) must contain `payed`-Tag. |

## URL

The URL of the Frontend `index.html` has the following structure:

	http://frontend.example.com/index.html#albumID/photoID/status
	
| Field | Description |
|:-----------|:------------|
| albumID | Id of the album. This field is required. |
| photoID | This field is used when the customer purchased a photo. The URL will take him directly to a photo. |
| status | After the purchase, the customer gets redirected from PayPal back to the Frontend. This field contains the status of the payment which can be `success`, `locked`, `unverified` or empty for an unknown error. |

The URL of the Frontend `redirect.html` has the following structure:

	http://frontend.example.com/redirect.html#type/code
	
| Field | Description |
|:-----------|:------------|
| type | Which type of dialog should be displayed.<br>Empty: Show the dialog where the customer can enter his code.<br>`email`: Show the dialog where the customer can enter his email when the session isn't available, yet. |
| code | This field is required when `type` is `email`. The code will be saved along with the email into the database. |