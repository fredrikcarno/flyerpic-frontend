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
					'assets/min/main.js': 'assets/js/*.js'

		sass:

			assets:
				files: [{
					expand: true
					cwd: 'assets/scss/'
					src: ['*.scss']
					dest: 'assets/css/'
					ext: '.css'
				}]

		cssmin:

			assets:
				files:
					'assets/min/main.css': 'assets/css/*.css'

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