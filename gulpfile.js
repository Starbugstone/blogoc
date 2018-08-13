var gulp = require('gulp'),
	browserSync = require('browser-sync').create(),
	sass = require('gulp-sass');
	
gulp.task('sass', function () {
  return gulp.src('./public/sass/**/*.scss')
    .pipe(sass.sync().on('error', sass.logError))
    .pipe(gulp.dest('./public/css'));
});
 
gulp.task('sass:watch', function () {
  gulp.watch('./public/sass/**/*.scss', ['sass']);
});