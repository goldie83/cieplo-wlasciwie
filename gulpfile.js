var path    = require('path'),
    fs      = require('fs'),
    rs      = require('randomstring'),
    del     = require('del'),
    gulp    = require('gulp'),
    compass = require('gulp-compass'),
    uglify  = require('gulp-uglify'),
    cssmin  = require('gulp-cssmin'),
    flatten = require('gulp-flatten'),
    concat  = require('gulp-concat');

gulp.task('version:bump', function(cb){
  var path = './app/config/parameters.yml';

  fs.readFile(path, 'utf8', function(err, data){
    if (err) {
      cb();
      return;
    }

    var result = data.replace(/assets_version: [a-zA-Z0-9]+\n/, 'assets_version: ' + rs.generate(16) + "\n");

    fs.writeFile(path, result, 'utf8', function(err){
      cb();
    });
  });
});

gulp.task('clean:js', function(){
  del.sync(['./web/assets/js/**']);
});

gulp.task('clean:css', function(){
  del.sync(['./web/assets/css/**']);
});

gulp.task('clean:fonts', function(){
  del.sync(['./web/assets/fonts/**']);
});

gulp.task('clean:images', function(){
  del.sync(['./web/assets/images/**']);
});

gulp.task('vendor:css', ['clean:css'], function(){
  return gulp.src([
    './bower/bootstrap/dist/css/bootstrap.css',
    './bower/bootstrap-material-design/dist/css/bootstrap-material-design.css',
    './bower/bootstrap-material-design/dist/css/ripples.css',
  ]).pipe(concat('vendor.css')).pipe(cssmin()).pipe(gulp.dest('./web/assets/css'));
});

gulp.task('vendor:ranking_css', [], function(){
  return gulp.src([
    './vendor/mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle/Resources/public/sass/mopabootstrapbundle.scss',
  ]).pipe(concat('ranking_vendor.css')).pipe(cssmin()).pipe(gulp.dest('./web/assets/css'));
});

gulp.task('vendor:js', ['clean:js'], function(){
  return gulp.src([
    './bower/jquery/dist/jquery.js',
    './bower/raphael/raphael.min.js',
    './bower/bootstrap/dist/js/bootstrap.js',
    './bower/bootstrap-material-design/dist/js/material.js',
    './bower/bootstrap-material-design/dist/js/ripples.js',
    './vendor/mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle/Resources/public/js/mopabootstrap-collection.js'
  ]).pipe(concat('vendor.js')).pipe(uglify()).pipe(gulp.dest('./web/assets/js'));
});

gulp.task('vendor:ranking_js', [], function(){
  return gulp.src([
    './vendor/mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle/Resources/public/bootstrap/js/tooltip.js',
    './vendor/mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle/Resources/public/bootstrap/js/*.js',
    './vendor/mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle/Resources/public/js/mopabootstrap-collection.js',
    './vendor/mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle/Resources/public/js/mopabootstrap-subnav.js',
  ]).pipe(concat('ranking_vendor.js')).pipe(uglify()).pipe(gulp.dest('./web/assets/js'));
});

gulp.task('vendor:fonts', ['clean:fonts'], function(){
  return gulp.src([
    './bower/bootstrap/dist/fonts/*'
  ]).pipe(flatten()).pipe(gulp.dest('./web/assets/fonts'));
});

gulp.task('app:css', ['clean:css'], function(){
  return gulp.src('./app/Resources/scss/app.scss').pipe(compass({
    project: path.join(__dirname),
    logging: true,
    css: 'web/assets/css',
    sass: 'app/Resources/scss',
    font: 'web/assets/fonts',
    style: 'compressed',
    import_path: [
      // Remember to add all paths
    ]
  }));
});

gulp.task('app:js', ['clean:css'], function(){
  return gulp.src([
    './app/Resources/js/app.js'
  ]).pipe(concat('app.js')).pipe(uglify()).pipe(gulp.dest('./web/assets/js'));
});

gulp.task('app:images', ['clean:images'], function(){
  return gulp.src([
    './app/Resources/images/*',
    './src/Kraken/WarmBundle/Resources/public/images/*'
  ]).pipe(flatten()).pipe(gulp.dest('./web/assets/images'));
});

gulp.task('app:ranking_css', [], function(){
  return gulp.src('./src/Kraken/RankingBundle/Resources/public/scss/ranking.scss').pipe(compass({
    project: path.join(__dirname),
    logging: true,
    css: 'web/assets/css',
    sass: 'src/Kraken/RankingBundle/Resources/public/scss',
    font: 'web/assets/fonts',
    style: 'compressed',
    import_path: [
      // Remember to add all paths
    ]
  }));
});

gulp.task('watch', function(){
  gulp.watch('./app/Resources/scss/**/*.scss', ['vendor:css', 'app:css', 'version:bump']);
  gulp.watch('./app/Resources/js/**/*.js', ['vendor.js', 'app:js', 'version:bump']);
});

gulp.task('clean', ['clean:js', 'clean:css', 'clean:fonts', 'clean:images']);
gulp.task('vendor', ['vendor:js', 'vendor:css', 'vendor:ranking_js', 'vendor:ranking_css', 'vendor:fonts']);
gulp.task('app', ['app:js', 'app:css', 'app:ranking_css', 'app:images']);
gulp.task('build', ['clean', 'vendor', 'app']);
gulp.task('default', ['build', 'version:bump']);
