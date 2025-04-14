'use strict';
let gulp = require('gulp'),
    sass = require('gulp-sass'),
    minCSS = require('gulp-clean-css'),
    pathToSassFiles = 'scss/*.scss';

// Таск
gulp.task('compileSass', function () {
    gulp.src(pathToSassFiles)
        .pipe(sass())
        .pipe(minCSS({compatibility: 'ie8'}))
        .pipe(gulp.dest('css/'));
});


// Наблюдение
gulp.task('watcher', ['compileSass'], function () {
    gulp.watch(pathToSassFiles, ['compileSass']);
});


// Дефолтная команда gulp
gulp.task('default', ['watcher']);