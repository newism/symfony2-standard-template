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

    var target = grunt.option('target') || "test";

    // Configurable paths
    var config = {
    };

    // Define the configuration for all the tasks
    grunt.initConfig({

        // Project settings
        config: config,

        // Watches files for changes and runs tasks based on the changed files
        watch: {
            gruntfile: {
                files: ['Gruntfile.js']
            },
            bower: {
                files: ['bower.json'],
                tasks: ['wiredep']
            }
        },

        // Automatically inject Bower components into the HTML file
        wiredep: {
            app: {
                src: ['src/Nsm/Bundle/AppBundle/Resources/views/layout.html.twig'],
                ignorePath: '../../../../../../web'
            }
        },

        useminPrepare: {
            options: {
                dest: 'web',
                root: 'web',
                patterns: {

                }
            },
            html: ['src/Nsm/Bundle/AppBundle/Resources/views/layout.html.twig'],
            css: ['web/themes/default/styles/{,*/}*.css'],
            js: ['web/themes/default/scripts/{,*/}*.js']
        },

        filerev: {
            files: {
                src: [
                    'web/themes/default/{,*/}*.js',
                    'web/themes/default/{,*/}*.css'
                ]
            }
        },

        filerev_assets: {
            dist: {
                options: {
                    dest: 'app/config/rev-manifest.json',
                    cwd: 'web/'
                }
            }
        }

    });

    grunt.registerTask('default', [

    ]);

    grunt.registerTask('build', [
        'useminPrepare',
        'concat',
        'cssmin',
        'uglify',
        'filerev',
        'filerev_assets'
    ]);

};
