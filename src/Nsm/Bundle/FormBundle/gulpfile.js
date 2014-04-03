var gulp = require('gulp');
var autoprefixer = require('gulp-autoprefixer');
var minifycss = require('gulp-minify-css');
var jshint = require('gulp-jshint');
var uglify = require('gulp-uglify');
var sass = require('gulp-ruby-sass');
var imagemin = require('gulp-imagemin');
var rename = require('gulp-rename');
var clean = require('gulp-clean');
var concat = require('gulp-concat');
var livereload = require('gulp-livereload');
var replace = require('gulp-replace');
var crypto = require('crypto');
var fs = require('fs');
var spritesmith = require('gulp.spritesmith');
var compass = require('gulp-compass');
var hashmap = require('gulp-hashmap');
var sequence = require('run-sequence');

//var uncss = require('gulp-uncss');
//var rev = require('gulp-rev');
//var revMap = require('gulp-rev-map');
//var debug = require('gulp-debug');
//var gutil = require('gulp-util');
//var using = require('gulp-using');
//var path = require('path');
//var notify = require('gulp-notify');

var paths = {};

paths.theme = __dirname + '/public_html/themes/site_themes';
paths.themeSrc = paths.theme + '/src/default_site';
paths.themeProd = paths.theme + '/prod/default_site';

paths.views = __dirname + '/views';
paths.viewsSrc = paths.views + '/src/default_site';
paths.viewsProd = paths.views + '/prod/default_site';


/**
 * Simple function to return the hash of a string
 * @param str
 * @param algorithm
 * @param length
 * @returns {*|Array|string|Blob}
 */
function hash(str, algorithm, length) {

    algorithm = algorithm || 'md5';
    length = length || 10;

    return crypto.createHash(algorithm).update(str).digest('hex').slice(0, length);
}

/**
 * Watch and launch styleguide
 */
gulp.task('default', ['watch', 'styleguide'], function () {});

/**
 * Launch the kss styleguide
 */
gulp.task('styleguide', function () {

    var exec = require('child_process').exec,
        child;

    child = exec('bundle exec ruby app.rb', {
            cwd: paths.themeSrc + '/styleguide'
        },
        function (error, stdout, stderr) {
            console.log(stdout, stderr);
            if (error !== null) {
                console.log('exec error:' + error);
            }
        });
});

/**
 * Run compass compile using the npm-compass module.
 */
gulp.task('compass', function () {
    gulp
        .src(paths.themeSrc + '/scss/*.scss')
        // See: https://github.com/appleboy/gulp-compass#load-config-from-configrb
        .pipe(compass({
            'bundle_exec': true,
            'project': paths.themeSrc,
            'config_file': paths.themeSrc + '/config.rb',
            'sass': 'scss',
            'css': 'styles',
            'time': true
        }))
        .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'))
        .pipe(minifycss({outSourceMap: true}))
        .pipe(gulp.dest(paths.themeSrc + '/styles'))
    ;
});

/**
 * Compass compile using exec command
 */
gulp.task('compass:compile', function () {
    var exec = require('child_process').exec,
        child;

    child = exec('bundle exec compass compile --sourcemap', {
            cwd: paths.themeSrc
        },
        function (error, stdout, stderr) {
            console.log(stdout, stderr);
            if (error !== null) {
                console.log('Error: ' + error);
            }
        });
});

/**
 * Compass watch using exec command
 */
gulp.task('compass:watch', function () {
    var exec = require('child_process').exec,
        child;

    child = exec('bundle exec compass watch --sourcemap', {
            cwd: paths.themeSrc + ''
        },
        function (error, stdout, stderr) {
            console.log(stdout, stderr);
            if (error !== null) {
                console.log('Error: ' + error);
            }
        });
});

/**
 * Run sass on the theme dir
 */
gulp.task('sass', function () {
    gulp
        .src(paths.themeSrc + '/scss/*.scss')
        .pipe(sass({
            sourcemap: true
        }))
        .pipe(autoprefixer('last 3 versions', '> 1%', 'ie 8'))
        .pipe(gulp.dest(paths.themeSrc + '/styles'))
    ;
});


/**
 * Generate Icon Sprites
 */
gulp.task('sprite:icons', function () {

    var spriteData = gulp.src(paths.themeSrc + '/images/icons/*.png')
        .pipe(spritesmith({
            imgName: 'icon-sprite.png',
            cssName: '_iconSprite.scss',
            engine: 'pngsmith',
            padding: 100,
            cssTemplate: paths.themeSrc + '/scss/scssMap.template.mustache',
            cssOpts: {
                cssClass: function (item) {
                    return'.Icon-' + item.name;
                }
            }
        }));

    spriteData.img.pipe(gulp.dest(paths.themeSrc + '/images/'));

    spriteData.css
        .pipe(replace(/icon-sprite\.png/g, 'icon-sprite.hash-' + hash(fs.readFileSync(paths.themeSrc + '/images/icon-sprite.png', 'utf8')) + '.png'))
        .pipe(gulp.dest(paths.themeSrc + '/scss/screen/'));
});

/**
 * Generate an asset revision json map
 */
gulp.task('assetRevMap', function () {
    gulp.src(paths.themeSrc + '/{**,*.*}')
        .pipe(hashmap('assets.json'))
        .pipe(gulp.dest(paths.themeProd));
});

/**
 * Lint JS
 */
gulp.task('lint', function () {
    gulp.src(paths.themeSrc + '/scripts/{**,*.*}')
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'));
});

/**
 * Compress Images
 */
gulp.task('compressImages', function () {
    gulp.src(paths.themeSrc + '/images/**/*')
        .pipe(imagemin({ optimizationLevel: 3, progressive: true, interlaced: true }))
        .pipe(gulp.dest(paths.themeProd + '/images'))
    ;
});

/**
 * Rev Asset Paths
 */
gulp.task('revFilePaths', function () {

    gulp.src([paths.viewsSrc + '/site.group/*'])
        .pipe(replace(/screen.min.hash-[a-z0-9]+.css/g, 'screen.min.hash-' + hash(fs.readFileSync(paths.themeProd + '/styles/screen.min.css', 'utf8')) + '.css'))
        .pipe(replace(/main.min.hash-[a-z0-9]+.js/g, 'main.min.hash-' + hash(fs.readFileSync(paths.themeProd + '/scripts/main.min.js', 'utf8')) + '.css'))
        .pipe(gulp.dest(paths.viewsSrc + '/site.group'));

});

/**
 * Clean build dir
 */
gulp.task('build:clean', function () {
    return gulp.src([paths.themeProd], {read: false})
        .pipe(clean());
});

/**
 * Build Styles
 */
gulp.task('build:styles', ['compass'], function () {
    return gulp.src(paths.themeSrc + '/styles/*.css')
        .pipe(gulp.dest(paths.themeProd + '/styles'))
        .pipe(rename({suffix: '.min'}))
        .pipe(minifycss({outSourceMap: true}))
        .pipe(gulp.dest(paths.themeProd + '/styles'))
        ;
});

/**
 * Build Scripts
 */
gulp.task('build:scripts', ['lint'], function () {
    return gulp.src(paths.themeSrc + '/scripts/*.js')
        .pipe(concat('main.js'))
        .pipe(gulp.dest(paths.themeProd + '/scripts'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify({outSourceMap: true}))
        .pipe(gulp.dest(paths.themeProd + '/scripts'))
        ;
});

/**
 * Build
 */
gulp.task('build', function (callback) {
    sequence(
        'build:clean',
        [
            'build:styles',
            'build:scripts',
            'compressImages'
        ],
        'revFilePaths',
        callback
    );
});

/**
 * Watch Sass, JS and icons
 */
gulp.task('watch', function () {
    var server = livereload();

    gulp.watch(paths.themeSrc + '/scripts/*.js', ['lint']).on('change', function (file) {
        server.changed(file.path);
    });

    gulp.watch(paths.themeSrc + '/scss/**/*.scss', ['compass']).on('change', function (file) {
        server.changed(file.path);
    });

    gulp.watch(paths.themeSrc + '/images/icons/*.png', ['sprite:icons']).on('change', function (file) {
        server.changed(file.path);
    });
});
