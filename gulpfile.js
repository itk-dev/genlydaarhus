#!/usr/bin/env node

// Plugin to handle parameters.
var argv = require('yargs')
  .alias('s', 'sync')
  .alias('t', 'theme')
  .alias('d', 'domain')
  .default('sync', false)
  .default('theme', ['devteam'])
  .default('domain', 'localhost:8080')
  .argv;

// Gulp basic.
var chalk = require('chalk');
var gulp = require('gulp-help')(require('gulp'), {
  'afterPrintCallback' : function() {
    var args = [];
    console.log(chalk.underline('Global parameters'));

    args.push('');
    args.push(chalk.cyan('--theme (-t)'));
    args.push('Limit the theme(s) that should be used. Use one --theme for each theme you want' + '\n');
    args.push(chalk.cyan('--sync (-s)'));
    args.push('Enabled browser-sync' + '\n');
    args.push(chalk.cyan('--domain (-d)'));
    args.push('Domain to use with browser-sync' + '\n\n');

    console.log.apply(console, args);
    console.log(chalk.underline('Usage examples:'));
    console.log(chalk.cyan('  *') + chalk.green(' gulp watch --theme base --theme blue --domain test.devteam.vm --sync'));
    console.log(chalk.cyan('  *') + chalk.green(' gulp watch --sync'));
    console.log(chalk.cyan('  *') + chalk.green(' gulp sass -t base'));
    console.log('\n' + 'It will always default to use all themes' + '\n');
  }
});

// Gulp plugins.
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var stylelint = require('gulp-stylelint');
var postcss = require('gulp-postcss');
var autoprefixer = require('autoprefixer');

// ESLint stuff.
var eslint = require('eslint/lib/cli');
var globby = require('globby');

// Browser-sync needs to be a globe variable.
var browserSync;

/**
 * Configuration object.
 */
var configuration = {
  // Base theme.
  'devteam': {
    "js": {
      "paths": ['./js/*.js', '!./devteam/js/*.min.*'],
      "dest": './js'
    },
    "sass": {
      "paths": './source/*.scss',
      'dest': './source/css'
    },
    "twig": {
      "paths": './templates/**/*.twig'
    }
  }
};

/**
 * Setup task for sass compilation.
 *
 * @param theme
 *    Name of the theme to setup the task.
 * @param config
 *    Selected theme configuration object.
 *
 * @return string
 *    The name of the new task.
 */
function sassTask(theme, config) {
  var taskName = 'sass_' + theme;

  // Process SCSS.
  gulp.task(taskName, false, function () {
    // Configure auto-prefixer.
    var processors = [
      autoprefixer({browsers: ['last 2 versions']})
    ];

    var pipe = gulp.src(config.sass.paths)
      .pipe(sourcemaps.init())
      .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
      .pipe(postcss(processors))
      .pipe(sourcemaps.write())
      .pipe(gulp.dest(config.sass.dest));

    // It's unknow why gulp-if don't work with browser-sync, so this if
    // statement is as little hack.
    if (argv.sync) {
      pipe.pipe(browserSync.stream());
    }
  });

  return taskName;
}

/**
 * Setup stylelint tasks.
 *
 * Note: Because stylelint task is async it returns it's gulp process.
 *
 * @param theme
 *    Name of the theme to setup the task.
 * @param config
 *    Selected theme configuration object.
 *
 * @return string
 *    The name of the new task.
 */
function stylelintTask(theme, config) {
  var taskName = 'stylelint_' + theme;

  gulp.task(taskName, false, function lintCssTask() {
    return gulp.src(config.sass.paths)
      .pipe(stylelint({
        reporters: [
          {formatter: 'string', console: true}
        ]
      }));
  });

  return taskName;
}

/**
 * Watch sass and stylelint tasks.
 *
 * @param theme
 *    Name of the theme to setup the task.
 * @param config
 *    Selected theme configuration object.
 *
 * @return string
 *    The name of the new task.
 */
function watchTasks(theme, config) {
  var taskName = 'watch_' + theme;

  gulp.task(taskName, false, function() {
    gulp.watch(config.sass.paths, ['sass']);
    gulp.watch(config.sass.paths, ['stylelint']);

    if (config.hasOwnProperty('js')) {
      gulp.watch(config.js.paths, ['eslint']);
    }

    if (argv.sync && config.hasOwnProperty('twig')) {
      gulp.watch(config.twig.paths).on('change', browserSync.reload);
    }
  });

  return taskName;
}

/**
 * ESLint tasks (JavaScript)
 *
 * Note: We don't use gulp-eslint as that package is out-dated.
 *
 * @param theme
 *    Name of the theme to setup the task.
 * @param config
 *    Selected theme configuration object.
 *
 * @return string
 *    The name of the new task.
 */
function ESLintTasks(theme, config) {
  var taskName = 'eslint_' + theme;

  gulp.task(taskName, false, function(done) {
    globby(config.js.paths).then(
      function(paths) {
        // Additional CLI options can be added here.
        var code = eslint.execute(paths.join(' '));
        done();
      },
      function(err) {
        // Unexpected failure, include stack.
        done(err);
      }
    );
  });

  return taskName;
}

/**
 * Minify JavaScript.
 *
 * @param theme
 *    Name of the theme to setup the task.
 * @param config
 *    Selected theme configuration object.
 *
 * @return string
 *    The name of the new task.
 */
function minifyJSTasks(theme, config) {
  var taskName = 'eslint_' + theme;

  gulp.task(taskName, false, function () {
      gulp.src(config.js.paths)
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(sourcemaps.write('/maps'))
        .pipe(rename({extname: ".min.js"}))
        .pipe(gulp.dest(config.js.dest));
    }
  );

  return taskName;
}

/**
 * Dynamically setup tasks base on the selected theme.
 *
 * @param themes
 *   Theme name as an string array. Used as index in the
 *   configuration pobject.
 */
function setupTasks(themes) {
  // Start browser sync.
  if (argv.sync) {
    browserSync = require('browser-sync').create();
    browserSync.init({
      proxy: argv.domain,
      host: argv.domain
    });
  }

  // Define task arrays.
  var sassTaskNames = [];
  var stylelintTaskNames = [];
  var eslintTaskNames = [];
  var jsMinifyTaskNames = [];
  var watchTasksNames = [];

  // Ensure themes is an array and if not convert it.
  if (Object.prototype.toString.call(themes) !== '[object Array]') {
    themes = [ themes ];
  }

  // Loop over the selected themes.
  for (var i in themes) {
    var theme = themes[i];
    var config = configuration[theme];

    // SASS tasks.
    sassTaskNames.push(sassTask(theme, config));

    // Style-lint tasks.
    stylelintTaskNames.push(stylelintTask(theme, config));

    // JavaScript tasks.
    if (config.hasOwnProperty('js')) {
      // Lint JavaScript.
      eslintTaskNames.push(ESLintTasks(theme, config));

      // Minify JavaScript
      jsMinifyTaskNames.push(minifyJSTasks(theme, config));
    }

    // Watch tasks.
    watchTasksNames.push(watchTasks(theme, config));
  }

  // Define tasks.
  gulp.task('sass', 'Compile SCSS into CSS', sassTaskNames);
  gulp.task('stylelint', 'Use style-lint to check SCSS (using stylelintrc.json rules)', stylelintTaskNames);
  gulp.task('eslint', 'Lint JavaScript with ESLint (using .eslintrc rules)', eslintTaskNames);
  gulp.task('jsMinify', 'Minify JavaScript files', jsMinifyTaskNames);
  gulp.task('watch', 'Watch for changes in files', watchTasksNames);

  // Default task;
  gulp.task('default', ['sass', 'jsMinify', 'stylelint', 'eslint']);
}

/**
 * ------- Run task's -------
 */
setupTasks(argv.theme);
