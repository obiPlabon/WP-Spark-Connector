var gulp = require('gulp');
var zip = require('gulp-zip');

gulp.task('package', async function () {
  gulp.src([
      './**',
      './*/**',
      '!./bower_components/**',
      '!./node_modules/**',
      '!./bower_components',
      '!./node_modules',
      '!gulpfile.js',
      '!package.json',
      '!./assets',
      '!*.json',
      '!*.config.js',
      '!*.lock',
      '!*.phar',
      '!*.xml',
  ])
    .pipe(zip('wp-spark-connector.zip'))
    .pipe(gulp.dest('.'));
});