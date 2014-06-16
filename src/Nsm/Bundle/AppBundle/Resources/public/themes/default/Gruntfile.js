// Generated on 2014-06-05 using generator-webapp 0.4.9
'use strict';

// # Globbing
// for performance reasons we're only matching one level down:
// 'test/spec/{,*/}*.js'
// use this if you want to recursively match all subfolders:
// 'test/spec/**/*.js'

module.exports = function(grunt) {

    // Load grunt tasks automatically
    require('load-grunt-tasks')(grunt);

    // Time how long tasks take. Can help when optimizing build times
    require('time-grunt')(grunt);

    // Configurable paths
    var config = {
        src: 'src',
        dist: 'dist',
        styleguide: 'styleguide'
    };

    // Define the configuration for all the tasks
    grunt.initConfig({

        // Project settings
        config: config,

        clean: {
            dist: {
                dot: true,
                src: [
                    '.tmp',
                    '<%= config.dist %>/**/*',
                    '!<%= config.dist %>/.git*'
                ]
            },
        },

        // Watches files for changes and runs tasks based on the changed files
        watch: {
            gruntfile: {
                files: ['Gruntfile.js']
            },
            bower: {
                files: ['bower.json'],
                tasks: ['wiredep']
            },
            js: {
                files: ['<%= config.src %>/scripts/{,*/}*.js'],
                tasks: ['jshint'],
                options: {
                    livereload: true
                }
            },
            scss: {
                files: ['<%= config.src %>/scss/{,*/}*.scss'],
                tasks: ['sass', 'autoprefixer']
            }
        },

        // Automatically inject Bower components into the HTML file
        bowerInstall: {
            app: {
                src: ['<%= config.styleguide %>/views/layout.erb'],
                ignorePath: '../../src'
            }
        },

        // Make sure code styles are up to par and there are no obvious mistakes
        jshint: {
            options: {
                jshintrc: '.jshintrc',
                reporter: require('jshint-stylish')
            },
            src: [
                'Gruntfile.js',
                '<%= config.src %>/scripts/{,*/}*.js',
                '!<%= config.src %>/scripts/vendor/*',
                'test/spec/{,*/}*.js'
            ]
        },

        // Automatically inject Bower components into the HTML file
        wiredep: {
            app: {
                src: ['<%= config.styleguide %>/views/layout.erb'],
                ignorePath: '../../src'
            },
            sass: {
                src: ['<%= config.src %>/styles/{,*/}*.{scss,sass}']
            }
        },

        // Compile Scss
        sass: {
            src: {
                options: {
                    style: 'expanded'
                },
                files: [{
                    expand: true,
                    cwd: '<%= config.src %>/scss',
                    src: ['{,*/}*.scss'],
                    dest: '<%= config.src %>/styles',
                    ext: '.css'
                }]
            }
        },

        // Autoprefixer
        autoprefixer: {
            options: {
                browsers: ['last 3 version']
            },
            src: {
                files: [{
                    expand: true,
                    cwd: '<%= config.theme_src %>/styles',
                    src: '{,*/}*.css',
                    dest: '<%= config.theme_src %>/styles'
                }]
            }
        },

        // Shell commands
        shell: {
            options: {
                stdout: true
            },
            kss: {
                options: {
                    execOptions: {
                        cwd: 'styleguide'
                    }
                },
                command: 'open http://localhost:4567 && bundle exec ruby app.rb'
            }
        }
    });

    grunt.registerTask('styleguide', ['shell:kss']);
};
