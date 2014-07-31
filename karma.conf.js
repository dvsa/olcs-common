module.exports = function(config) {

  "use strict";

  config.set({
    basePath: "",
    frameworks: ["mocha", "expect"],
    files: [
      // test helpers
      "node_modules/sinon/lib/sinon.js",
      "node_modules/sinon/lib/sinon/spy.js",
      "node_modules/sinon/lib/sinon/**/*.js",

      // source files...
      "Common/assets/js/vendor/**/*.js",
      "Common/assets/js/src/**/*.js",

      // ... and test files
      "test/js/**/*.test.js"
    ],
    exclude: [],
    preprocessors: {
      "Common/assets/js/src/**/*.js": ["coverage"]
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
