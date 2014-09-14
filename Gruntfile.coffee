module.exports = (grunt) ->

	grunt.initConfig

		pkg: grunt.file.readJSON 'package.json'

		coffee:

			assets:
				expand: true,
				flatten: true,
				cwd: 'assets/coffee/',
				src: ['*.coffee'],
				dest: 'assets/js/',
				ext: '.js'

		uglify:

			assets:
				files:
					'assets/min/main.js': 'assets/js/!(redirect).js'

			redirect:
				files:
					'assets/min/redirect.js': 'assets/js/redirect.js'

		sass:

			assets:
				files:
					'assets/css/main.css': 'assets/scss/main.scss'

			redirect:
				files:
					'assets/css/redirect.css': 'assets/scss/redirect.scss'

		cssmin:

			assets:
				files:
					'assets/min/main.css': 'assets/css/main.css'

			redirect:
				files:
					'assets/min/redirect.css': 'assets/css/redirect.css'

		watch:

			coffee:
				files: 'assets/coffee/*.coffee'
				tasks: ['coffee', 'uglify', 'clean']
				options:
					spawn: false
					interrupt: true

			scss:
				files: 'assets/scss/*.scss'
				tasks: ['sass', 'cssmin', 'clean']
				options:
					spawn: false
					interrupt: true

		clean: ['assets/css', 'assets/js']

	require('load-grunt-tasks')(grunt)

	grunt.registerTask 'default', ['coffee', 'uglify', 'sass', 'cssmin', 'clean']