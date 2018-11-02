var path   = require('path'),
    gulp   = require('gulp'),
    clean  = require('gulp-clean'),
    concat = require('gulp-concat'),
    concss = require('gulp-concat-css'),
    clscss = require('gulp-clean-css'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    less   = require('gulp-less'),
    watch  = require('gulp-watch');


gulp.task('Clean Build Directory', function () {
    return gulp.src('build', {read: false})
        .pipe(clean());
});

gulp.task('Compile LESS', function () {
    return gulp.src([
        './src/css/flexboxgrid.css',
        './src/css/font-awesome.css',
        './src/less/*.less'])
        .pipe(less())
        .pipe(concss('style.css', {rebaseUrls: false}))
        .pipe(gulp.dest('./www/css'))
        .pipe(clscss({compatibility: 'ie8', level: {1: {specialComments: 'none'}}}))
        .pipe(rename({basename: 'style', suffix: '.min'}))
        .pipe(gulp.dest('./www/css'));
});

gulp.task('Build jQuery', function () {
    return gulp.src(['./node_modules/jquery/dist/jquery.js'])
        .pipe(gulp.dest('./www/js/vendor'))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./www/js/vendor'));
});

gulp.task('Build Font Awesome', function () {
    return gulp.src(['./node_modules/font-awesome/fonts/fontawesome-webfont.*'])
        .pipe(rename({basename: 'font-awesome'}))
        .pipe(gulp.dest('./www/fonts/'));
});

gulp.task('Build Dropdown JS Bundle', function () {
    return gulp.src(['./src/js/dropdown.js'])
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./www/js'));
});

gulp.task('Build Dropdown CSS Bundle', function () {
    return gulp.src(['./src/css/dropdown.css'])
        .pipe(clscss({compatibility: 'ie8', level: {1: {specialComments: 'none'}}}))
        .pipe(gulp.dest('./www/css'));
});

gulp.task('Build Rower JS Bundle', function () {
    return gulp.src(['./src/js/rower.js'])
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./www/js'));
});

gulp.task('Build Slide Menu JS Bundle', function () {
    return gulp.src(['./src/js/slide-menu.js'])
        //.pipe(uglify())
        .pipe(uglify().on('error', function(e){
            console.log(e);
        }))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./www/js'));
});

gulp.task('Build Ion Range Slider JS Bundle', function () {
    return gulp.src(['./node_modules/ion-rangeslider/js/ion.rangeSlider.js'])
        .pipe(uglify())
        .pipe(rename({basename: 'range-slider', suffix: '.min'}))
        .pipe(gulp.dest('./www/js/vendor'));
});

gulp.task('Build Ion Range Slider CSS Bundle', function () {
    return gulp.src(['./node_modules/ion-rangeslider/css/ion.rangeSlider.css',
        //'./node_modules/ion-rangeslider/css/ion.rangeSlider.skinFlat.css',
        //'./node_modules/ion-rangeslider/css/ion.rangeSlider.skinNice.css',
        './node_modules/ion-rangeslider/css/ion.rangeSlider.skinModern.css'
    ])
        .pipe(concss('range-slider.css', {rebaseUrls: false}))
        .pipe(clscss({compatibility: 'ie8', level: {1: {specialComments: 'none'}}}))
        .pipe(rename({basename: 'range-slider'}))
        .pipe(gulp.dest('./www/css'));
});

gulp.task('Build Ion Range Slider Image Bundle', function () {
    return gulp.src([
        './node_modules/ion-rangeslider/img/sprite-skin-flat.png',
        './node_modules/ion-rangeslider/img/sprite-skin-nice.png',
        './node_modules/ion-rangeslider/img/sprite-skin-modern.png'])
        .pipe(gulp.dest('./www/img'));
});


gulp.task('Build Nice Select JS Bundle', function () {
    return gulp.src(['./src/js/nice-select.js'])
        .pipe(uglify())
        .pipe(rename({basename: 'nice-select', suffix: '.min'}))
        .pipe(gulp.dest('./www/js/vendor'));
});


gulp.task('Build Nice Select CSS Bundle', function () {
    return gulp.src(['./src/css/nice-select.css'])
        .pipe(clscss({compatibility: 'ie8', level: {1: {specialComments: 'none'}}}))
        .pipe(rename({basename: 'nice-select'}))
        .pipe(gulp.dest('./www/css/'));
});

gulp.task('Air DatePicker JS', function () {
    return gulp.src([
        './node_modules/air-datepicker/dist/js/datepicker.js',
        './node_modules/air-datepicker/dist/js/i18n/datepicker.en.js'])
        .pipe(concat('datepicker.js'))
        .pipe(gulp.dest('./www/js/vendor'))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./www/js/vendor'));
});

gulp.task('Air DatePicker CSS', function () {
    return gulp.src('./node_modules/air-datepicker/dist/css/datepicker.css')
        .pipe(gulp.dest('./www/css'))
        .pipe(clscss({compatibility: 'ie8', level: {1: {specialComments: 'none'}}}))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./www/css'));
});

gulp.task('Modal JS', function () {
    return gulp.src(['./src/js/modal.js'])
        .pipe(gulp.dest('./www/js'))
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./www/js'));
});

gulp.task('Modal Less', function () {
    return gulp.src(['./src/less/modal.less'])
        .pipe(less())
        .pipe(gulp.dest('./www/css'))
        .pipe(clscss({compatibility: 'ie8', level: {1: {specialComments: 'none'}}}))
        .pipe(rename({basename: 'style', suffix: '.min'}))
        .pipe(gulp.dest('./www/css'));
});