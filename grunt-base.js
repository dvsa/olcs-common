module.exports = function(sourcePaths, target) {

  "use strict";

  var commonPaths = [
    "vendor/olcs/OlcsCommon/Common/assets/js/vendor/**/*.js",
    "vendor/olcs/OlcsCommon/Common/assets/js/src/**/*.js"
  ];

  var buildFiles = {
    "public/static/js/common.js": commonPaths
  };
  buildFiles["public/static/js/" + target + ".js"] = sourcePaths;

  return function(grunt) {

    grunt.initConfig({
      uglify: {
        options: {
          sourceMap: true
        },
        build: {
          files: buildFiles
        }
      },

      jshint: {
        options: {
          jshintrc: "vendor/olcs/OlcsCommon/.jshintrc"
        },
        all: sourcePaths
      },

      karma: {
        unit: {
          configFile: "karma.conf.js",
          singleRun: true,
          browsers: ["PhantomJS"]
        }
      },

      watch: {
        scripts: {
          files: sourcePaths,
          tasks: ["uglify:build"]
        }
      }
    });

    grunt.loadNpmTasks("grunt-contrib-uglify");
    grunt.loadNpmTasks("grunt-contrib-jshint");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-karma");

    grunt.registerTask("default", ["jshint", "karma", "build"]);

    grunt.registerTask("build", "uglify:build");
  };
};
