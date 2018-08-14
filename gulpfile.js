var gulp = require("gulp"),
	sass = require("gulp-sass");
	
gulp.task("scss", function () {
  return gulp.src("./public/scss/**/*.scss")
    .pipe(sass.sync().on("error", sass.logError))
    .pipe(gulp.dest("./public/css"));
});
 
gulp.task("scss:watch", function () {
  gulp.watch("./public/scss/**/*.scss", gulp.series("scss"));
});