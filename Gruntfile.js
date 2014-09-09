'use strict';

// # Globbing
// for performance reasons we're only matching one level down:
// 'test/spec/{,*/}*.js'
// use this if you want to recursively match all subfolders:
// 'test/spec/**/*.js'

module.exports = function (grunt) {

    // Load grunt tasks automatically
    require('load-grunt-tasks')(grunt);

    // Time how long tasks take. Can help when optimizing build times
    require('time-grunt')(grunt);

    var target = grunt.option('target') || "test";

    // Configurable paths
    var config = {};

    // Define the configuration for all the tasks
    grunt.initConfig({

        // Project settings
        config: config,
        target: target,

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
            src: {
                src: ['src/Nsm/Bundle/AppBundle/Resources/views/layout.html.twig'],
                ignorePath: '../../../../../../web',
                exclude: ['/src/web/bower_components/modernizr/modernizr.js']
            }
        },

        clean: {
            dist: {
                dot: true,
                src: [
                    '.tmp',
                    '<%= target %>'
                ]
            }
        },

        copy: {
            dist: {
                files: [
                    {
                        expand: true,
                        dot: true,
                        cwd: 'src',
                        dest: '<%= target %>',
                        src: [
                            'app/**',
                            'bin/console',
                            'src/**',
                            'var/bootstrap.php.cache',
                            'var/SymfonyRequirements.php',
                            'var/cache/.gitkeep',
                            'var/logs/.gitkeep',
                            'web/**',
                            'vendor/**',
                            '.gitignore',
                            'behat.yml',
                            'composer.json',
                            'composer.lock',
                            'LICENSE',
                            'README.md'
                        ]
                    }
                ]
            }
        },

        chmod: {
            dist: {
                options: {
                    mode: '755'
                },
                src: ['<%= target %>/bin/console']
            }
        },

        // https://github.com/yeoman/grunt-usemin#the-useminprepare-task
        useminPrepare: {
            options: {
                dest: '<%= target %>/web',
                root: '<%= target %>/web'
            },
            // https://github.com/yeoman/grunt-usemin#directories
            html: {
                src: ['<%= target %>/src/Nsm/Bundle/AppBundle/Resources/views/layout.html.twig']
            }
        },

        filerev: {
            dist: {
                src: [
                    '<%= target %>/web/themes/default/{,*/}*.js',
                    '<%= target %>/web/themes/default/{,*/}*.css',
                    '<%= target %>/web/*.ico',
                    '<%= target %>/web/*.png'
                ]
            }
        },

        filerev_assets: {
            dist: {
                options: {
                    dest: '<%= target %>/app/config/rev-manifest.json'
                    //cwd: 'web/'
                }
            }
        },

        usemin: {
            options: {
                assetsDirs: [
                    // This path is where the urls (relative or absolute)
                    // will search from
                    '<%= target %>/web'
                ],
                patterns: {
                    // Add custom patterns for JS replacements
                    js: [],
                    twig: [
                        [/asset\(['"]([^"']+)["']\)/gm, 'Replacing twig asset urls']
                    ]
                }
            },
            html: '<%= target %>/src/Nsm/Bundle/AppBundle/Resources/views/layout.html.twig',
            js: ['<%= target %>/web/themes/default/dist/scripts/{,*/}*.js'],
            css: '<%= target %>/web/themes/default/styles/{,*/}*.css',
            twig: '<%= target %>/src/Nsm/Bundle/AppBundle/Resources/views/layout.html.twig'
        },

        replace: {
            dist: {
                options: {
                    patterns: [
                        {
                            match: /(\/themes\/default\/styles\/[vendor|screen|print].*?\.css)/gm,
                            replacement: '{{ asset(\'$1\') }}'
                        },
                        {
                            match: /(\/themes\/default\/scripts\/[vendor|app].*?\.js)/gm,
                            replacement: '{{ asset(\'$1\') }}'
                        }
                    ]
                },
                files:  [{
                    expand: true,
                    src: ['<%= target %>/src/Nsm/Bundle/AppBundle/Resources/views/layout.html.twig']
                }]
            }
        }
    })
    ;

    grunt.registerTask('default', []);

    grunt.registerTask('build', [
        'wiredep',
        'clean',
        'copy',
        'useminPrepare',
        'concat:generated',
        'cssmin:generated',
        'uglify:generated',
        'filerev',
        'filerev_assets',
        'usemin',
        'replace:dist',
        'chmod:dist'
    ]);

}
;
