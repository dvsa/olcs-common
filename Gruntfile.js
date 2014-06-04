module.exports = function(grunt) {
  "use strict";

  grunt.initConfig({
    jshint: {
      options: {
        jshintrc: ".jshintrc"
      },
      all: ["Common/assets/js/src/**/*.js"]
    },

    karma: {
      unit: {
        configFile: "karma.conf.js",
        singleRun: true,
        browsers: ["PhantomJS"]
      }
    }
  });

  grunt.loadNpmTasks("grunt-contrib-jshint");
  grunt.loadNpmTasks("grunt-karma");

  grunt.registerTask("default", ["jshint", "karma"]);

  grunt.registerTask("build:selfserve", "uglify:selfserve");
  grunt.registerTask("build:internal", "uglify:internal");
};
