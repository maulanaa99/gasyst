'use strict'

var gulp = require('gulp');
var replace = require('gulp-replace');
var merge = require('merge-stream');

//  copy all assets to the dist folder 
function CopyAssetsToTheme() {
    let copyImages = gulp.src('./assets/images/**/*').pipe(gulp.dest(`../dist/themes/assets/images`))
    let copyJs = gulp.src('./assets/js/*').pipe(gulp.dest(`../dist/themes/assets/js`))
    let copyVendors = gulp.src('./assets/vendors/**/*').pipe(gulp.dest(`../dist/themes/assets/vendors`));
    let copyFonts = gulp.src('./assets/fonts/**/*').pipe(gulp.dest(`../dist/themes/assets/fonts`));
    let copyThemeCss = gulp.src('./assets/css/**/*').pipe(gulp.dest('../dist/themes/assets/css'))
    let copyEntry = gulp.src('./index.html').pipe(gulp.dest('../dist/'));
    let copyDocs = gulp.src('./docs/**/*').pipe(gulp.dest('../dist/docs/'));
    return merge(copyImages, copyJs, copyVendors, copyFonts, copyEntry, copyThemeCss, copyDocs)
}

// copy all html files to related themes in dist 
function CopyHtmlToTheme(path) {
    let copyPages = gulp.src(`./themes/${path}/pages/**/*`).pipe(gulp.dest(`../dist/themes/${path}/pages`));
    let copyMain = gulp.src(`./themes/${path}/*.html`).pipe(gulp.dest(`../dist/themes/${path}/`));
    return merge(copyPages, copyMain)
}

// replace path in dist folder
gulp.task('replacePathDist', function () {
    var replacePath1 = gulp.src(['../dist/themes/**/pages/*/*.html'], { base: "../dist" })
        .pipe(replace('="../../../../assets/images', '="../../../assets/images'))
        .pipe(replace('="../../../../assets/vendors', '="../../../assets/vendors'))
        .pipe(replace('="../../../../assets/js', '="../../../assets/js'))
        .pipe(replace('="../../../../assets/css', '="../../../assets/css'))
        .pipe(replace('="../../../docs/', '="../../../../docs/'))
        .pipe(replace('href="../../index.html"', 'href="../../index.html"'))
        .pipe(gulp.dest('../dist'));
    var replacePath2 = gulp.src(['../dist/themes/**/index.html'], { base: "../dist" })
        .pipe(replace('="../../assets/images', '="../assets/images'))
        .pipe(replace('="../../assets/vendors', '="../assets/vendors'))
        .pipe(replace('="../../assets/js', '="../assets/js'))
        .pipe(replace('="../../../docs/', '="../../docs/'))
        .pipe(replace('="../../assets/css', '="../assets/css'))
        .pipe(gulp.dest('../dist'));
    let replaceEntryPage = gulp.src(['../dist/index.html'], { base: '../dist' })
        .pipe(replace('="assets/css/', '="themes/assets/css/'))
        .pipe(replace('="assets/images/', '="themes/assets/images/'))
        // .pipe(replace('="assets/images/favicon.png"', '="themes/assets/images/favicon.png"'))
        // .pipe(replace('="assets/images/logo.svg"', '="themes/assets/images/logo.svg"'))
        .pipe(gulp.dest('../dist'))
    let replaceDashJs = gulp.src(['../dist/themes/assets/js/dashboard.js'], { base: '../dist' })
        .pipe(replace('"ajax": "../../assets/js/data.txt"', '"ajax": "../assets/js/data.txt"'))
        .pipe(gulp.dest('../dist'))
    // let replaceDashDarkJs = gulp.src(['../dist/themes/assets/js/dashboard-dark.js'], { base: '../dist' })
    //     .pipe(replace('"ajax": "../../assets/js/data.txt"', '"ajax": "../assets/js/data.txt"'))
    //     .pipe(gulp.dest('../dist'))
    let replaceDoc = gulp.src(['../dist/docs/documentation.html'], { base: '../dist' })
        .pipe(replace('="../assets/', '="../themes/assets/'))
        .pipe(gulp.dest('../dist'))
    return merge(replacePath1, replacePath2, replaceEntryPage, replaceDashJs, replaceDoc);
});

const themePaths = [
    'horizontal-default',
    'vertical-boxed',
    'vertical-compact',
    'vertical-default-dark',
    'vertical-default-light',
    'vertical-fixed',
    'vertical-hidden-toggle',
    'vertical-icon-menu',
    'vertical-light-sidebar',
    'vertical-toggle-overlay'
]

const copyHtml = themePaths.map((theme) => {
    const taskName = `copy html to ${theme}`
    gulp.task(taskName, () => CopyHtmlToTheme(theme))
    return taskName
})


gulp.task('build', gulp.series(CopyAssetsToTheme, copyHtml, 'replacePathDist'));