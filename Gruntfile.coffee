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

		sass:

			assets:
				files: [{
					expand: true
					cwd: 'assets/scss/'
					src: ['*.scss']
					dest: 'assets/css/'
					ext: '.css'
				}]

		watch:

			coffee:
				files: 'assets/coffee/*.coffee'
				tasks: ['coffee']
				options:
					spawn: false
					interrupt: true

			scss:
				files: 'assets/scss/*.scss'
				tasks: ['sass']
				options:
					spawn: false
					interrupt: true

	require('load-grunt-tasks')(grunt)

	grunt.registerTask 'default', ['coffee', 'sass']
	grunt.registerTask 'dev', ['watch']