
describe("OLCS.modal", function() {
  "use strict";

  beforeEach(function() {
    this.component = OLCS.modal;
  });

  it("should be defined", function() {
    expect(this.component).to.exist;
  });

  it("should declare the correct public interface", function() {
    expect(this.component.init).to.be.a("function");
  });

  describe("Given a stubbed DOM", function() {
    beforeEach(function() {
      var template = [
        '<div id="stub">',
          '<div class="overlay"></div>',
          '<div class="modal__wrapper">',
            '<div class="modal">',
              '<a href="" class="modal__close">Close</a>',
            '</div>',
          '</div>',
          '<a href="/foo" class="js-modal one">foo</a>',
          '<a href="/foo" class="js-modal two"></a>',
          '<a href="/bar" class="js-modal three"></a>',
        '</div>'
      ].join("\n");

      this.body = $("body");

      this.body.append(template);

      this.on = sinon.spy($.prototype, "on");
    });

    afterEach(function() {
      this.on.restore();
      this.body.remove("#stub");
    });

    describe("when initialised with valid options", function() {
      beforeEach(function() {
        this.options = {
          trigger: ".js-modal",
          selector: ".modal"
        };
        this.component.init(this.options);
      });

      afterEach(function() {
        // have to clean up our event handlers otherwise they'll stack up
        $(document).off("click");
      });

      it("binds the correct show listener", function() {
        var call = this.on.getCall(0);
        expect(call.args[0]).to.equal("click");
        expect(call.args[1]).to.equal(".js-modal");
      });

      it("binds the correct hide listener", function() {
        var call = this.on.getCall(1);
        expect(call.args[0]).to.equal("click");
        expect(call.args[1]).to.equal(".modal__close");
      });

      describe("Given a stubbed ajax mechanism", function() {
        beforeEach(function() {
          var self = this;

          this.ajax = sinon.stub($, "ajax", function(args) {
            self.args = args;
          });
        });

        afterEach(function() {
          this.ajax.restore();
        });

        describe("when triggering a modal", function() {
          beforeEach(function() {
            $(".js-modal:eq(0)").click();
          });

          it("makes an ajax request", function() {
            expect(this.ajax.calledOnce).to.equal(true);
          });

          describe("Given the request returns successfully", function() {
            beforeEach(function() {
              this.append = sinon.stub($.prototype, "append");
              this.show = sinon.stub($.prototype, "show");
            });

            afterEach(function() {
              this.append.restore();
              this.show.restore();
            });

            describe("with no content fragment identifier", function() {
              beforeEach(function() {
                this.args.success("dummy response data");
              });

              it("appends the response data correctly", function() {
                expect(this.append.calledWith("dummy response data")).to.be(true);
              });

              it("shows the modal wrapper", function() {
                expect(this.show.called).to.be(true);
              });

              describe("when dismissing the modal", function() {
                beforeEach(function() {
                  this.hide = sinon.stub($.prototype, "hide");
                  $(".modal__close").click();
                });

                afterEach(function() {
                  this.hide.restore();
                });

                it("hides the modal wrapper", function() {
                  expect(this.hide.called).to.be(true);
                });

                describe("when showing another modal with the same href", function() {
                  beforeEach(function() {
                    $(".js-modal:eq(1)").click();
                  });

                  it("does not make a new ajax request", function() {
                    expect(this.ajax.calledOnce).to.be(true);
                  });

                  it("appends the response data correctly", function() {
                    expect(this.append.calledWith("dummy response data")).to.be(true);
                  });

                  it("shows the modal wrapper", function() {
                    expect(this.show.called).to.be(true);
                  });
                });

                describe("when showing another modal with a different href", function() {
                  beforeEach(function() {
                    $(".js-modal:eq(2)").click();
                  });

                  it("makes a new ajax request", function() {
                    expect(this.ajax.calledTwice).to.be(true);
                  });
                });
              });
            });

            describe("with a content fragement identifier", function() {
              beforeEach(function() {
                // we get away with setting the content option so late here by virtue
                // of the fact it's an object so any changes are applied by reference
                this.options.content = "#main";
                this.args.success("<div>foo bar <div id='main'>dummy</div> response data</div>");
              });

              it("appends the response data correctly", function() {
                expect(this.append.calledWith("dummy")).to.be(true);
              });
            });
          });
        });
      });
    });
  });
});
