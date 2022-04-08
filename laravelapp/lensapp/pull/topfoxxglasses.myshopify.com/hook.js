var script = document.createElement('script');
script.setAttribute('src', '//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js');
document.head.appendChild(script);
			  var t = $.noConflict(true);
 var o = void 0;
function change(e, o) {
  if (t('[data-step="'+ e +'"]').addClass("active-step").removeClass("done-step"), t(".modal-prescription .header-prescription .prev-arrow").removeClass("d-none"), t(".modal-prescription #optical--footer #prev-button").removeClass("d-none"), t('[data-step="' + o + '"]').addClass("done-step"), t('[data-step="' + o + '"]').removeClass("active-step"), 5 == o) {
    var n = t(".right-sph").val(),
        i = t(".left-sph").val();
    if (n < parseInt("-3.25") || i < parseInt("-3.25")) {
      var a = t("#optical--footer .product-info .product-price-footer").attr("data-lence-price");
      setTimeout((function() {
        "" == a && t("body").find(".rx-glass1").trigger("click")
      }), 300)
    }
  }
  5 == e && "0" == t(".pupillary-cyl").val() && "0.00" == t(".left-sph").val() && "0.00" == t(".right-sph").val() && "0.00" == t(".right-cyl").val() && "000" == t(".right-axis").val() && "0.00" == t(".left-cyl").val() && "000" == t(".left-axis").val() && "0" == t(".pupillary-sph").val() && 0 == t("#customCheck2").prop("checked") && "0" == t(".pupillary-sph").val() && t("#optical--footer .next-button #next-button").prop("disabled", !0), 7 == e && (0 == t("#rx-material1").is(":checked") ? t(".sucessfuly-msg").addClass("d-none") : t(".sucessfuly-msg").removeClass("d-none"), setTimeout((function() {
    t("#optical--footer .next-button").addClass("d-none"), t("#optical--footer .rx-add-to-cart").removeClass("d-none")
  }), 100))
}
t(".prescription-btn").on("click", function(e) {
  e.preventDefault(), 
    t(".prescription-step-popup").show(), 
    t("body").addClass("overflowHidden"), 
    setTimeout((function() {
    var e = t(".header-prescription").outerHeight(),
        o = t(".footer-prescription").outerHeight();
    t(".content-prescription").css("padding-bottom", o), 
      t(".content-prescription").css("padding-top", e)
  }), 100);
}),
t("body").on("click", ".modal-prescription .header-prescription .close", function(e) {
  t(".prescription-step-popup").hide(),
    t("body").removeClass("overflowHidden"), 
    t('[data-step="1"]').addClass("active-step"),
    t('[data-step="1"]').removeClass("done-step"),
    t(".slide-page-dots li").removeClass("done-step active-step"), 
    t(".slide-page-dots li").eq(0).addClass("active-step"),
    t(".step-wrapper").removeClass("done-step active-step"), 
    t(".step-wrapper").eq(0).addClass("active-step"), 
    t(".prev-arrow,.prev-button button").addClass("d-none")
}),
t("body").on("change", 'input[type="file"]', function(o) {
  if (o.preventDefault(), "" != t(this).val()) {
    t(".upload-button").text("Uploading..."), 
      t(".upload-button").css("pointer-events", "none"), 
      t("#optical--footer .next-button #next-button").prop("disabled", !1);
    var n = t("#fileUploads").prop("files")[0],
        i = new FormData;
    i.append("attachment", n), t.ajax({
      url: "https://prescription.installmultiplepixel.com/lensapp/uploadPrescriptionFile",
      method: "POST",
      contentType: !1,
      cache: !1,
      processData: !1,
      data: i
    }).done((function(o) {
      t(".prescription-upload").val(o.full_url), 
        t(".upload-button").text("complete!"), 
        t(".prescription-method").val("Upload File")
       // _learnq.push(["track", "Prescription"])
    }))
  }
}),
t(".shopify-product-form .tt-input-counter .minus-btn").on("click", function() {
  t(".shopify-product-form .tt-input-counter input").val(), t(".product-price").attr("data-main-pro-price")
}),
  t(".shopify-product-form .tt-input-counter .plus-btn").on("click", function() {
  var e = t(".shopify-product-form .tt-input-counter input").val(),
      o = t(".product-price").attr("data-normal-pro-price") * e;
  t(".product-price").attr("data-main-pro-price", o)
}),
t("body").on("click", ".content-prescription .select-prescription .select-options label", function() {
  var e = t(this);
  setTimeout((function() {
    if ("radio" == e.closest(".select-options").find("input").attr("type") && !0 === e.closest(".select-options").find("input").prop("checked"))
      if ("single-vision-clear-without-blue-light" == (o = e.closest(".select-options").find("input").attr("data-lens")) ? t(".warning-msg").removeClass("d-none") : t(".warning-msg").addClass("d-none"), "non-prescription" == o) {
        t(".product-addToCart-overlay").removeClass("d-none");
        var n = t(".product-main").attr("main-proVariantID"),
            i = e.closest(".select-options").find("input").attr("data-variantID"),
            a = t(".shopify-product-form .tt-input-counter input").val(),
            r = t(".random-number").val(),
            s = e.closest(".select-options").find("input").attr("data-lens-price"),
            l = t(".product-price").attr("data-main-pro-price"),
            d = "$" + ((parseInt(s) + parseInt(l)) / 100).toFixed(2);
        Shopify.queue = [], Shopify.queue.push({
          variantId: n,
          qty: a
        }), Shopify.moveAlong = function() {
          if (Shopify.queue.length) {
            var e = Shopify.queue.shift(),
                o = {
                  id: e.variantId,
                  quantity: e.qty,
                  properties: {
                    "Group ID": r,
                    "Prescription Total Price": d
                  }
                };
            t.ajax({
              type: "POST",
              url: "/cart/add.js",
              dataType: "json",
              data: o,
              async: !1,
              success: function(e) {
                Shopify.moveAlong(), t(".prescription-qty").val(), setTimeout((function() {
                  Shopify.queue = [], Shopify.queue.push({
                    variantId: i
                  }), Shopify.moveAlong = function() {
                    if (Shopify.queue.length) {
                      var e = {
                        id: Shopify.queue.shift().variantId,
                        quantity: 1,
                        properties: {
                          "Group ID": r
                        }
                      };
                      t.ajax({
                        type: "POST",
                        url: "/cart/add.js",
                        dataType: "json",
                        data: e,
                        async: !1,
                        success: function(e) {
                          Shopify.moveAlong(), setTimeout((function() {
                            window.location.href = "/cart"
                          }), 500)
                        },
                        error: function() {
                          Shopify.queue.length && Shopify.moveAlong()
                        }
                      })
                    }
                  }, Shopify.moveAlong()
                }), 1500)
              },
              error: function() {
                Shopify.queue.length && Shopify.moveAlong()
              }
            })
          }
        }, Shopify.moveAlong()
      } else {
        t("#optical--footer .next-button #next-button").prop("disabled", !1), e.closest(".modal-prescription").find("#optical--footer .footer-group .next-button #next-button").removeAttr("disabled"), e.closest(".step-wrapper").attr("data-step");
        var c = e.closest(".step-wrapper").attr("data-next-step");
        7 == c ? (t("#optical--footer .next-button").addClass("d-none"), t("#optical--footer .rx-add-to-cart").removeClass("d-none")) : 6 == c ? t("#optical--footer .next-button #next-button").prop("disabled", !0) : (t("#optical--footer .next-button").removeClass("d-none"), t("#optical--footer .rx-add-to-cart").addClass("d-none")), "reading" == o && "4" == c && c++
      }
  }), 100)
}),
  t("body").on("click", ".content-prescription .modal--doctor-prescription .select-options label", function() {
  var o = t(this);
  setTimeout((function() {
    if ("radio" == o.closest(".select-options").find("input").attr("type")) {
      var n = o.closest(".select-options").find("input").prop("checked"),
          i = o.closest(".select-options").find("input").val("checked");
      if (!0 === n && "Upload" != i) {
        t("#optical--footer .next-button #next-button").prop("disabled", !1);
        var a = o.closest(".select-options").find("[data-per-method]").attr("data-value");
        t(".prescription-method").val(a), o.closest(".modal-prescription").find("#optical--footer .footer-group .next-button #next-button").removeAttr("disabled");
        var r = o.closest(".step-wrapper").attr("data-step"),
            s = o.closest(".step-wrapper").attr("data-next-step");
        7 == s ? (t("#optical--footer .next-button").addClass("d-none"), t("#optical--footer .rx-add-to-cart").removeClass("d-none")) : 6 == s ? t("#optical--footer .next-button #next-button").prop("disabled", !0) : (t("#optical--footer .next-button").removeClass("d-none"), t("#optical--footer .rx-add-to-cart").addClass("d-none")), e(s, r)
      }
      "Upload" == i && t("#optical--footer .next-button #next-button").prop("disabled", !0)
    }
  }), 100)
}),
  t("body").on("click", ".content-prescription .modal--reading-power .select-options label", function() {
  var o = t(this);
  setTimeout((function() {
    if ("radio" == o.closest(".select-options").find("input").attr("type") && !0 === o.closest(".select-options").find("input").prop("checked")) {
      t("#optical--footer .next-button #next-button").prop("disabled", !1);
      var n = o.closest(".select-options").find("input").attr("data-reading-power");
      t(".prescription-reading_power").val(n), o.closest(".modal-prescription").find("#optical--footer .footer-group .next-button #next-button").removeAttr("disabled");
      var i = o.closest(".step-wrapper").attr("data-step"),
          a = o.closest(".step-wrapper").attr("data-next-step");
      6 == a ? (t("#optical--footer .next-button").addClass("d-none"), t("#optical--footer .rx-add-to-cart").removeClass("d-none")) : 5 == a || (t("#optical--footer .next-button").removeClass("d-none"), t("#optical--footer .rx-add-to-cart").addClass("d-none")), e(a, i)
    }
  }), 100)
}),   
  t("body").on("click", "#optical--footer .next-button #next-button", function() {
  if (t(this).closest(".modal-prescription").find(".content-prescription").find(".step-wrapper").hasClass("active-step")) {
    var n = t(".active-step").attr("data-step"),
        i = t(".active-step").attr("data-next-step");
    if ("single-vision-with-clear-anti-blue-light-blocking-technology" == o && 5 == n && (i = 7), console.log(i), 7 == i ? (t("#optical--footer .next-button").addClass("d-none"), t("#optical--footer .rx-add-to-cart").removeClass("d-none")) : 5 == i ? "0" == t(".pupillary-cyl").val() && "0.00" == t(".left-sph").val() && "0.00" == t(".right-sph").val() && "0.00" == t(".right-cyl").val() && "000" == t(".right-axis").val() && "0.00" == t(".left-cyl").val() && "000" == t(".left-axis").val() && "0" == t(".pupillary-sph").val() && 0 == t("#customCheck2").prop("checked") && t("#optical--footer .next-button #next-button").prop("disabled", !0) : (t("#optical--footer .next-button").removeClass("d-none"), t("#optical--footer .rx-add-to-cart").addClass("d-none")), "5" == n) {
      var a = "";
      t(".active-step").find("[data-title]").each((function(e, o) {
        if (t(this).attr("data-title").indexOf("right") > -1) {
          var n = t(this).val();
          a += n + ","
        }
      })), a = a.slice(0, a.length - 1), t(".prescription-od").val(a);
      var r = "";
      t(".active-step").find("[data-title]").each((function(e, o) {
        if (t(this).attr("data-title").indexOf("left") > -1) {
          var n = t(this).val();
          r += n + ","
        }
      })), r = r.slice(0, r.length - 1), t(".prescription-os").val(r);
      var s = t('[data-title="pupillary-sph"]').val();
      null != s && (t(".prescription-pd").val(s), t(".prescription-pd-table").text(":" + s));
      var l = t('[data-title="pupillary-cyl"]').val();
      null != l && (t(".prescription-pd2").val(l), t(".prescription-pd2-table").text(":" + l))
    }
    change(i, n);
  }
}),
  t("body").on("click", ".header-prescription .prev-arrow, #optical--footer #prev-button", function() {
  var e = t(this);
  if (e.closest(".modal-prescription").find(".content-prescription").find(".step-wrapper").hasClass("active-step")) {
    var n = t(".active-step").attr("data-step"),
        i = t(".active-step").attr("data-prev-step");
    4 == n && e.addClass("d-none"), 7 == n && (t("#optical--footer .next-button").removeClass("d-none"), t("#optical--footer .rx-add-to-cart").addClass("d-none")), "single-vision-with-clear-anti-blue-light-blocking-technology" == o && 7 == n && i--,
      function(e, o) {
      t('[data-step="' + e + '"]').addClass("active-step"), t('[data-step="' + e + '"]').removeClass("done-step"), t('[data-step="' + o + '"]').removeClass("active-step"), 1 == e ? (t(".prev-arrow,.prev-button button").addClass("d-none"), t("#rx-material2,#rx-material1").is(":checked") && t("#next-button").removeAttr("disabled")) : t(".prev-arrow,.prev-button button").removeClass("d-none")
    }(i, n)
  }
}),
  t("body").on("change", ".lense-number select", function() {
  "0" == t(".pupillary-sph").val() && "0" == t(".pupillary-cyl").val() ? t("#optical--footer .next-button #next-button").prop("disabled", !0) : t("#optical--footer .next-button #next-button").prop("disabled", !1);
  var e = t(this).val();
  "pupillary-sph" == t(this).attr("data-title") && (e < parseInt("58") || e > parseInt("63") ? t(".update-desc").removeClass("d-none") : t(".update-desc").addClass("d-none"));
  var f = t(this).val();
  "pupillary-sph" == t(this).attr("data-title") && (f > parseInt("58") ? t(".update-desc.pd-info-kids").removeClass("d-none") : t(".update-desc.pd-info-kids").addClass("d-none"));
  var o = t(".right-sph").val(),
      n = t(".left-sph").val();
  o < parseInt("-3.25") || n < parseInt("-3.25") ? (t(".add-blue-glasses").removeClass("d-none"), t(".rx--lens-message").addClass("d-none"), t("[data-lens-price]").html("$50.00")) : (t(".add-blue-glasses").addClass("d-none"), t(".rx--lens-message").removeClass("d-none"), t("[data-lens-price]").html("$0.00")), "0.00" == t(".left-sph").val() && "0.00" == t(".right-sph").val() && "0.00" == t(".right-cyl").val() && "000" == t(".right-axis").val() && "0.00" == t(".left-cyl").val() && "000" == t(".left-axis").val() && "0" == t(".pupillary-sph").val() && t("#optical--footer .next-button #next-button").prop("disabled", !0)
}), 
  t("body").on("click", ".content-prescription .modal--slide-container .lens-type label", function() {
  var e = t(this);
  setTimeout((function() {
    if (!0 === e.closest(".select-options").find("input").prop("checked")) {
      t(".lensClass").removeClass("product-selected"), e.closest(".select-options").find("input").addClass("product-selected");
      var o = e.closest(".select-options").find("input").attr("data-producttitle"),
          n = e.closest(".select-options").find("input").attr("data-lens-price");
      t(".prescription-material").val(o), t(".lens-price-prop,.prescription-material-price").val(n);
      var i = e.closest(".select-options").find("input").attr("data-lens"),
          a = e.closest(".select-options").find(".lens-price").text(),
          r = t("[data-main-pro-price]").attr("data-main-pro-price"),
          s = t("#optical--footer .product-info .product-price-footer").attr("data-lence-price");
      if ("" != s) var l = parseInt(a) + parseInt(r) + parseInt(s);
      else l = parseInt(a) + parseInt(r);
      t("[data-total-price]").attr("data-total-price", l);
      var d = (l / 100).toFixed(2);
      t(".rx-product-details .product-price").attr("data-frame-price", a), t("#optical--footer .product-info .product-price-footer").attr("data-frame-price", a), t(".rx-product-details .product-price").attr("data-total-price", l), t("#optical--footer .product-info .product-price-footer").attr("data-total-price", l), t(".rx-product-details .product-price,#optical--footer .product-info .product-price-footer").text("$" + d), t(".prescription-total-price").val("$" + d), t(".product-upgrades-lens").removeClass("d-none"), t(".product-upgrades-lens .product-upgrade li").text(i + " / 1.6")
    } else e.closest(".select-options").find("input").removeClass("product-selected")
      }), 500)
}),  
  t("body").on("click", ".content-prescription .modal--slide-container .glasses-type label", function() {
  var e = t(this);
  setTimeout((function() {
    e.closest(".select-options").find("input").prop("checked");
    var o = [];
    o.push(".prescription-lens"), o.length > 0 ? (t(".product-upgrades-glass").removeClass("d-none"), t(".product-upgrades-glass .product-upgrade li").text(o)) : t(".product-upgrades-glass").addClass("d-none"), e.closest(".select-options").find("input").addClass("product-selected");
    var n = t("[data-total-price]").attr("data-total-price"),
        i = e.closest(".select-options").find(".glass-price").text(),
        a = e.closest(".select-options").find("input").attr("data-glass");
    t(".prescription-lence").val(a), t("#optical--footer .product-info .product-price-footer").attr("data-lence-price", i), t(".prescription-glass-price,.prescription-lence-price").val(i);
    var r = parseInt(i) + parseInt(n);
    t("[data-total-price]").attr("data-total-price", r);
    var s = (r / 100).toFixed(2);
    t(".rx-product-details .product-price,#optical--footer .product-info .product-price-footer").text("$" + s), t(".prescription-total-price").val("$" + s)
  }), 500)
}),
  t("body").on("click", ".two-pds .custom-control label", function() {
  var e = t(this);
  setTimeout((function() {
    !0 === e.closest(".custom-control").find("input").prop("checked") ? (t(".dist2").removeClass("d-none"), t(".no-pd .custom-control input").prop("checked", !1), t("#optical--footer .next-button #next-button").prop("disabled", !1)) : t(".dist2").addClass("d-none")
  }), 500)
}),
  t("body").on("click", ".no-pd .custom-control label", function() {
  var e = t(this);
  setTimeout((function() {
    !0 === e.closest(".custom-control").find("input").prop("checked") && (t(".two-pds .custom-control input").prop("checked", !1), t(".dist2").addClass("hide"))
  }), 500)
}),  
  t("body").on("change", "select.form-control", function(e) {
  var o = t(this),
      n = t(this).val(),
      i = o.attr("data-title");
  t('.product-summary td[data-title="' + i + '"]').html(n)
});
 var n = t(".shopify-product-form .tt-input-counter input").val();
t(".prescription-qty").val(n), t("body").on("change", ".shopify-product-form .tt-input-counter input", function() {
  var e = t(this).val();
  t(".prescription-qty").val(e)
}),
  t("body").on("click", ".prescription-addBtn", function(e) {
  e.preventDefault(), t(".loader-btn").removeClass("d-none"), t(this).addClass("d-none"), t(this).prop("disabled", !0);
  var o = {},
      n = t(".prescription-variantId").val(),
      i = t(".prescription-qty").val();
  Shopify.queue = [], t.each(t('input[name*="properties"]').serializeArray(), (function() {
    var e = this.name.replace("properties[", "").replace("]", "");
    o[e] = this.value
  })), Shopify.queue.push({
    variantId: n,
    qty: i
  }), Shopify.moveAlong = function() {
    if (Shopify.queue.length) {
      var e = {
        id: Shopify.queue.shift().variantId,
        quantity: 1,
        properties: o
      };
      t.ajax({
        type: "POST",
        url: "/cart/add.js",
        dataType: "json",
        data: e,
        success: function(e) {
          Shopify.queue = [];
          var o = t(".random-number").val();
          t(".product-selected").each((function() {
            var e = t(this).attr("data-variantid");
            Shopify.queue.push({
              variantId: e
            })
          })), Shopify.moveAlong = function() {
            if (Shopify.queue.length) {
              var e = {
                id: Shopify.queue.shift().variantId,
                quantity: 1,
                properties: {
                  "Group ID": o
                }
              };
              t.ajax({
                type: "POST",
                url: "/cart/add.js",
                dataType: "json",
                data: e,
                async: !1,
                success: function(e) {
                  Shopify.moveAlong(), window.location.href = "/cart", t(".loader-btn").addClass("d-none"), t(".prescription-addBtn").removeClass("d-none"), t(".prescription-addBtn").prop("disabled", !1)
                },
                error: function() {
                  Shopify.queue.length && Shopify.moveAlong()
                }
              })
            }
          }, Shopify.moveAlong()
        },
        error: function() {
          Shopify.queue.length && Shopify.moveAlong()
        }
      })
    }
  }, Shopify.moveAlong()
}),
  t("body").on("click", ".no-thank", function(t) {
  t.preventDefault(), e(7, 6)
}),
t("body").on("click", ".yes-please", function(o) {
  o.preventDefault(), t(".rx-material1").trigger("click"), t(".warning-msg").addClass("d-none"), t('[data-step="6"]').removeClass("done-step"), t("#optical--footer .next-button").addClass("d-none"), t("#optical--footer .rx-add-to-cart").removeClass("d-none"), e(7, 6)
}),
//  t("body").on("click", ".footer-block .footer-title", function(t){
//    t.preventDefault();
// t(this).hasClass("active")
// })
$(document).ready(function(){
let isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
 if (isMobile) {
	$(".footer-sect .footer_link").hide();
	$(".footer-sect h4").click(function(){
		$(this).next(".footer_link").slideToggle("slow")
		.siblings(".footer_link:visible").slideUp("slow");
		$(this).toggleClass("active");
		$(this).siblings("h4").removeClass("active");
	});
    }	
});