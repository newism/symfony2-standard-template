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

        srcDir: './',
        buildDir: target,

        appDir: 'app',
        templateDir: 'templates',
        publicDir: 'public',
        themeDir: 'theme',

        imageDir: 'images',
        scriptDir: 'scripts',
        fontDir: 'fonts',
        stylesDir: 'styles',
        scssDir: 'scss',

        appSrcDir: '<%= config.srcDir %>/<%= config.appDir %>',
        templateSrcDir: '<%= config.appSrcDir %>/<%= config.templateDir %>',
        publicSrcDir: '<%= config.srcDir %>/<%= config.publicDir %>',
        themeSrcDir: '<%= config.publicSrcDir %>/<%= config.themeDir %>',

        appBuildDir: '<%= config.buildDir %>/<%= config.appDir %>',
        templateBuildDir: '<%= config.appBuildDir %>/<%= config.templateDir %>',
        publicBuildDir: '<%= config.buildDir %>/<%= config.publicDir %>',
        themeBuildDir: '<%= config.publicBuildDir %>/<%= config.themeDir %>'

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
            },
            sass: {
                src: ['<%= config.templateSrcDir %>/<%= config.stylesDir %>/{,*/}*.{scss,sass}']
            }
        }
    });

};
