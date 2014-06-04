module.exports = function(sourcePath) {

  "use strict";

  return function(config) {

    config.set({
      basePath: "",
      frameworks: ["mocha", "expect"],
      files: Array.prototype.concat([
        // test helpers
        "node_modules/sinon/lib/sinon.js",
        "node_modules/sinon/lib/sinon/spy.js",
        "node_modules/sinon/lib/sinon/**/*.js",

        // common dependencies
        "vendor/olcs/OlcsCommon/Common/assets/js/vendor/**/*.js",
        "vendor/olcs/OlcsCommon/Common/assets/js/src/**/*.js"

      ], sourcePath, [

        // ... and test files
        "test/js/**/*.test.js"
      ]),
      exclude: [],
      preprocessors: {
        sourcePath: ["coverage"]
      },
      reporters: ["mocha", "coverage", "junit"],
      port: 9876,
      colors: true,
      logLevel: config.LOG_INFO,
      autoWatch: true,
      captureTimeout: 60000,

      coverageReporter: {
        type: "lcov",
        dir: "test/js/coverage"
      },

      junitReporter: {
        outputFile: "test/js/reports/results.xml"
      }
    });
  };
};
