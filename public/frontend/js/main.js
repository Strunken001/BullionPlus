(function ($) {
    "user strict";

    //preloder
    $(window).on('load', function () {
        $(".preloader").delay(1500).animate({
            "opacity": "0"
        }, 1500, function () {
            $(".preloader").css("display", "none");
        });
    });

    //Create Background Image
    (function background() {
        let img = $('.bg_img');
        img.css('background-image', function () {
            var bg = ('url(' + $(this).data('background') + ')');
            return bg;
        });
    })();

    // aos
    AOS.init();



    // nice-select
    $(".nice-select").niceSelect(),

        // lightcase
        $(window).on('load', function () {
            $("a[data-rel^=lightcase]").lightcase();
        })

    // navbar-click
    $(".navbar li a").on("click", function () {
        var element = $(this).parent("li");
        if (element.hasClass("show")) {
            element.removeClass("show");
            element.children("ul").slideUp(500);
        }
        else {
            element.siblings("li").removeClass('show');
            element.addClass("show");
            element.siblings("li").find("ul").slideUp(500);
            element.children('ul').slideDown(500);
        }
    });


    // scroll-to-top
    var ScrollTop = $(".scrollToTop");
    $(window).on('scroll', function () {
        if ($(this).scrollTop() < 100) {
            ScrollTop.removeClass("active");
        } else {
            ScrollTop.addClass("active");
        }
    });

    // banner slider
    var swiper = new Swiper(".banner-slider", {
        slidesPerView: 1,
        spaceBetween: 0,
        loop: true,
        autoplay: {
            speed: 1000,
            delay: 6000,
        },
        speed: 1000,
        navigation: {
            nextEl: '.slider-next',
            prevEl: '.slider-prev',
        },
    });

    // Plan Slider
    var swiper = new Swiper(".plan-slider", {
        slidesPerView: 5,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            speed: 1000,
            delay: 3000,
        },
        speed: 1000,
        navigation: {
            nextEl: '.slider-prev',
            prevEl: '.slider-next',
        },
        breakpoints: {
            1199: {
                slidesPerView: 5,
            },
            991: {
                slidesPerView: 4,
            },
            767: {
                slidesPerView: 3,
            },
            575: {
                slidesPerView: 2,
            },
        }
    });

    //Odometer
    if ($(".counter").length) {
        $(".counter").each(function () {
            $(this).isInViewport(function (status) {
                if (status === "entered") {
                    for (var i = 0; i < document.querySelectorAll(".odometer").length; i++) {
                        var el = document.querySelectorAll('.odometer')[i];
                        el.innerHTML = el.getAttribute("data-odometer-final");
                    }
                }
            });
        });
    }

    /*Clients Review*/



    //toggle password

    $(".toggle-password").click(function () {

        $(this).toggleClass("la-eye la-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
    //toggle password
    $(document).ready(function () {
        $(".show_hide_password a").on('click', function (event) {
            event.preventDefault();
            if ($('.show_hide_password input').attr("type") == "text") {
                $('.show_hide_password input').attr('type', 'password');
                $('.show_hide_password i').addClass("fa-eye-slash");
                $('.show_hide_password i').removeClass("fa-eye");
            } else if ($('.show_hide_password input').attr("type") == "password") {
                $('.show_hide_password input').attr('type', 'text');
                $('.show_hide_password i').removeClass("fa-eye-slash");
                $('.show_hide_password i').addClass("fa-eye");
            }
        });
    });
    $(document).ready(function () {
        $(".show_hide_password-2 a").on('click', function (event) {
            event.preventDefault();
            if ($('.show_hide_password-2 input').attr("type") == "text") {
                $('.show_hide_password-2 input').attr('type', 'password');
                $('.show_hide_password-2 i').addClass("fa-eye-slash");
                $('.show_hide_password-2 i').removeClass("fa-eye");
            } else if ($('.show_hide_password-2 input').attr("type") == "password") {
                $('.show_hide_password-2 input').attr('type', 'text');
                $('.show_hide_password-2 i').removeClass("fa-eye-slash");
                $('.show_hide_password-2 i').addClass("fa-eye");
            }
        });
    });
    $(document).ready(function () {
        $(".show_hide_password-3 a").on('click', function (event) {
            event.preventDefault();
            if ($('.show_hide_password-3 input').attr("type") == "text") {
                $('.show_hide_password-3 input').attr('type', 'password');
                $('.show_hide_password-3 i').addClass("fa-eye-slash");
                $('.show_hide_password-3 i').removeClass("fa-eye");
            } else if ($('.show_hide_password-3 input').attr("type") == "password") {
                $('.show_hide_password-3 input').attr('type', 'text');
                $('.show_hide_password-3 i').removeClass("fa-eye-slash");
                $('.show_hide_password-3 i').addClass("fa-eye");
            }
        });
    });

    // faq
    $('.faq-wrapper .faq-title').on('click', function (e) {
        var element = $(this).parent('.faq-item');
        if (element.hasClass('open')) {
            element.removeClass('open');
            element.find('.faq-content').removeClass('open');
            element.find('.faq-content').slideUp(300, "swing");
        } else {
            element.addClass('open');
            element.children('.faq-content').slideDown(300, "swing");
            element.siblings('.faq-item').children('.faq-content').slideUp(300, "swing");
            element.siblings('.faq-item').removeClass('open');
            element.siblings('.faq-item').find('.faq-title').removeClass('open');
            element.siblings('.taq-item').find('.faq-content').slideUp(300, "swing");
        }
    });



    $(document).ready(function () {
        var AFFIX_TOP_LIMIT = 300;
        var AFFIX_OFFSET = 110;

        var $menu = $("#menu"),
            $btn = $("#menu-toggle");

        $("#menu-toggle").on("click", function () {
            $menu.toggleClass("open");
            return false;
        });


        $(".docs-nav").each(function () {
            var $affixNav = $(this),
                $container = $affixNav.parent(),
                affixNavfixed = false,
                originalClassName = this.className,
                current = null,
                $links = $affixNav.find("a");

            function getClosestHeader(top) {
                var last = $links.first();

                if (top < AFFIX_TOP_LIMIT) {
                    return last;
                }

                for (var i = 0; i < $links.length; i++) {
                    var $link = $links.eq(i),
                        href = $link.attr("href");

                    if (href.charAt(0) === "#" && href.length > 1) {
                        var $anchor = $(href).first();

                        if ($anchor.length > 0) {
                            var offset = $anchor.offset();

                            if (top < offset.top - AFFIX_OFFSET) {
                                return last;
                            }

                            last = $link;
                        }
                    }
                }
                return last;
            }


            $(window).on("scroll", function (evt) {
                var top = window.scrollY,
                    height = $affixNav.outerHeight(),
                    max_bottom = $container.offset().top + $container.outerHeight(),
                    bottom = top + height + AFFIX_OFFSET;

                if (affixNavfixed) {
                    if (top <= AFFIX_TOP_LIMIT) {
                        $affixNav.removeClass("fixed");
                        $affixNav.css("top", 0);
                        affixNavfixed = false;
                    } else if (bottom > max_bottom) {
                        $affixNav.css("top", (max_bottom - height) - top);
                    } else {
                        $affixNav.css("top", AFFIX_OFFSET);
                    }
                } else if (top > AFFIX_TOP_LIMIT) {
                    $affixNav.addClass("fixed");
                    affixNavfixed = true;
                }

                var $current = getClosestHeader(top);

                if (current !== $current) {
                    $affixNav.find(".active").removeClass("active");
                    $current.addClass("active");
                    current = $current;
                }
            });
        });
    });



    // active menu JS
    function splitSlash(data) {
        return data.split('/').pop();
    }
    function splitQuestion(data) {
        return data.split('?').shift().trim();
    }
    var pageNavLis = $('.sidebar-menu a');
    var dividePath = splitSlash(window.location.href);
    var divideGetData = splitQuestion(dividePath);
    var currentPageUrl = divideGetData;




    // dashboard-list
    $('.dashboard-list-item').on('click', function (e) {
        var element = $(this).parent('.dashboard-list-item-wrapper');
        if (element.hasClass('show')) {
            element.removeClass('show');
            element.find('.preview-list-wrapper').removeClass('show');
            element.find('.preview-list-wrapper').slideUp(300, "swing");
        } else {
            element.addClass('show');
            element.children('.preview-list-wrapper').slideDown(300, "swing");
            element.siblings('.dashboard-list-item-wrapper').children('.preview-list-wrapper').slideUp(300, "swing");
            element.siblings('.dashboard-list-item-wrapper').removeClass('show');
            element.siblings('.dashboard-list-item-wrapper').find('.dashboard-list-item').removeClass('show');
            element.siblings('.dashboard-list-item-wrapper').find('.preview-list-wrapper').slideUp(300, "swing");
        }
    });

    // invoice-form
    $('.invoice-form').on('click', '.add-row-btn', function () {
        $('.add-row-btn').closest('.invoice-form').find('.add-row-wrapper').last().clone().show().appendTo('.results');
    });

    $(document).on('click', '.invoice-cross-btn', function (e) {
        e.preventDefault();
        $(this).parent().parent().hide(300);
    });

    //pdf
    $('.pdf').on('click', function (e) {
        e.preventDefault();
        $('.pdf-area').addClass('active');
        $('.body-overlay').addClass('active');
    });
    $('#body-overlay, #pdf-area').on('click', function (e) {
        e.preventDefault();
        $('.pdf-area').removeClass('active');
        $('.body-overlay').removeClass('active');
    })



    //Profile Upload
    function proPicURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var preview = $(input).parents('.preview-thumb').find('.profilePicPreview');
                $(preview).css('background-image', 'url(' + e.target.result + ')');
                $(preview).addClass('has-image');
                $(preview).hide();
                $(preview).fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $(".profilePicUpload").on('change', function () {
        proPicURL(this);
    });

    $(".remove-image").on('click', function () {
        $(".profilePicPreview").css('background-image', 'none');
        $(".profilePicPreview").removeClass('has-image');
    });


    //info-btn
    $(document).on('click', '.info-btn', function () {
        $('.support-profile-wrapper').addClass('active');
    });
    $(document).on('click', '.chat-cross-btn', function () {
        $('.support-profile-wrapper').removeClass('active');
    });


    $(document).on("click", ".card-custom", function () {
        $(this).toggleClass("active");
    });

    //account-toggle
    $('.header-account-btn').on('click', function (e) {
        e.preventDefault();
        $('.account-section').addClass('active');
        $('.body-overlay').addClass('active');
    });
    $('#body-overlay').on('click', function (e) {
        e.preventDefault();
        $('.account-section').removeClass('active');
        $('.body-overlay').removeClass('active');
    });
    $('.account-close').on('click', function (e) {
        e.preventDefault();
        $('.account-section').removeClass('active');
        $('.body-overlay').removeClass('active');
    });
    $('.remove-account').on('click', function (e) {
        e.preventDefault();
        $(this).parent().parent().hide(300);
    });
    $('.account-control-btn').on('click', function () {
        $('.account-area').toggleClass('change-form');
    })


    $(".account-control-btn").click(function () {
        var source = $(this).attr("data-block");
        $(".account-wrapper").hide();
        $(".account-wrapper." + source).show();
    });



    // select-2 init
    $('.select2-basic').select2();
    $('.select2-multi-select').select2();
    $(".select2-auto-tokenize").select2({
        tags: true,
        tokenSeparators: [',']
    });

    // Checkbox
    $(document).on("change", ".dependency-checkbox", function () {
        dependencyCheckboxHandle($(this));
    });

    $(document).ready(function () {
        let dependencyCheckbox = $(".dependency-checkbox");
        $.each(dependencyCheckbox, function (index, item) {
            dependencyCheckboxHandle($(item));
        });
    });


    function dependencyCheckboxHandle(targetCheckbox) {
        let target = $(targetCheckbox).attr("data-target");
        if ($(targetCheckbox).is(":checked")) {
            $("." + target).slideDown(300);
        } else {
            $("." + target).slideUp(300);
        }
    }

    // Dasboard menu btn
    function toggleDropdown() {
        var dropdownMenu = document.getElementById('dropdown-menu');
        if (dropdownMenu.style.display === 'block') {
            dropdownMenu.style.display = 'none';
        } else {
            dropdownMenu.style.display = 'block';
        }
    }

})(jQuery);


/**
 * Function For Get All Country list by AJAX Request
 * @param {HTML DOM} targetElement
 * @param {Error Place Element} errorElement
 * @returns
 */
// var allCountries = "";
// function getAllCountries(hitUrl, targetElement = $(".country-select"), errorElement = $(".country-select").siblings(".select2")) {
//     if (targetElement.length == 0) {
//         return false;
//     }
//     var CSRF = $("meta[name=csrf-token]").attr("content");
//     var data = {
//         _token: CSRF,
//     };
//     $.post(hitUrl, data, function () {
//         // success
//         $(errorElement).removeClass("is-invalid");
//         $(targetElement).siblings(".invalid-feedback").remove();
//     }).done(function (response) {
//         // Place States to States Field
//         var options = "<option selected disabled>Select Country</option>";
//         var selected_old_data = "";
//         if ($(targetElement).attr("data-old") != null) {
//             selected_old_data = $(targetElement).attr("data-old");
//         }
//         $.each(response, function (index, item) {
//             options += `<option value="${item.name}" data-id="${item.id}" data-mobile-code="${item.mobile_code}" ${selected_old_data == item.name ? "selected" : ""}>${item.name}</option>`;
//         });
//         allCountries = response;
//         $(targetElement).html(options);
//     }).fail(function (response) {
//         var faildMessage = "Something went wrong! Please try again.";
//         var faildElement = `<span class="invalid-feedback" role="alert">
//                             <strong>${faildMessage}</strong>
//                         </span>`;
//         $(errorElement).addClass("is-invalid");
//         if ($(targetElement).siblings(".invalid-feedback").length != 0) {
//             $(targetElement).siblings(".invalid-feedback").text(faildMessage);
//         } else {
//             errorElement.after(faildElement);
//         }
//     });
// }


/**
 * Function For Open Modal Instant by pushing HTML Element
 * @param {Object} data
 */
function openModalByContent(data = {
    content: "",
    animation: "mfp-move-horizontal",
    size: "medium",
}) {
    $.magnificPopup.open({
        removalDelay: 500,
        items: {
            src: `<div class="white-popup mfp-with-anim ${data.size ?? "medium"}">${data.content}</div>`, // can be a HTML string, jQuery object, or CSS selector
        },
        callbacks: {
            beforeOpen: function () {
                this.st.mainClass = data.animation ?? "mfp-move-horizontal";
            },
            open: function () {
                var modalCloseBtn = this.contentContainer.find(".modal-close");
                $(modalCloseBtn).click(function () {
                    $.magnificPopup.close();
                });
            },
        },
        midClick: true,
    });
}

/**
 * Function For Open Modal with CSS Selector Ex: "#modal-popup"
 * @param {String} selector
 * @param {String} animation
 */
function openModalBySelector(selector, animation = "mfp-move-horizontal") {
    $(selector).addClass("white-popup mfp-with-anim");
    if (animation == null) {
        animation = "mfp-zoom-in"
    }
    $.magnificPopup.open({
        removalDelay: 500,
        items: {
            src: $(selector), // can be a HTML string, jQuery object, or CSS selector
            type: 'inline',
        },

        callbacks: {
            beforeOpen: function () {
                this.st.mainClass = animation;
            },
            elementParse: function (item) {
                var modalCloseBtn = $(selector).find(".modal-close");
                $(modalCloseBtn).click(function () {
                    $.magnificPopup.close();
                });
            },
        },
    });
    $.magnificPopup.instance._onFocusIn = function (e) {
        // Do nothing if target element is select2 input
        if ($(e.target).hasClass('select2-search__field')) {
            return true;
        }
        // Else call parent method
        $.magnificPopup.proto._onFocusIn.call(this, e);
    }
}


function currentModalClose() {
    return $.magnificPopup.instance.close();
}



/**
 * Function for getting CSRF token for form submit in laravel
 * @returns string
 */
function laravelCsrf() {
    return $("head meta[name=csrf-token]").attr("content");
}

/**
 * Function for help to add new input field in manual payment gateway
 */
function inputFieldValidationRuleFieldsShow(element) {
    if ($(element).attr("data-show-db") != undefined) {
        return false;
    }
    var value = element.val();
    var validationFieldsPlaceElement = $(element).parents(".add-row-wrapper").find(".field_type_input");
    if (value == "text" || value == "textarea") {
        var textValidationFields = getHtmlMarkup().manual_gateway_input_text_validation_field;
        validationFieldsPlaceElement.html(textValidationFields);
    } else if (value == "file") {
        var textValidationFields = getHtmlMarkup().manual_gateway_input_file_validation_field;
        validationFieldsPlaceElement.html(textValidationFields);
        var select2Input = validationFieldsPlaceElement.find(".select2-auto-tokenize");
        $(select2Input).select2();
    } else if (value == "select") {
        var textValidationFields = getHtmlMarkup().manual_gateway_select_validation_field;
        validationFieldsPlaceElement.html(textValidationFields);
    }

    // Refresh all file extension input name
    var fileExtenionSelect = $(element).parents(".results").find(".add-row-wrapper").find(".file-ext-select");
    $.each(fileExtenionSelect, function (index, item) {
        var fileExtSelectFieldName = "file_extensions[" + index + "][]";
        $(item).attr("name", fileExtSelectFieldName);
    });
}


// Generate Random Password
function makeRandomString(length, type = "alpha_speacial") {
    var result = '';
    var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    if (type == "alpha_speacial") {
        var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz#$%&'()*+,-./:;<=>?@[\]^_`{|}~";
    } else if (type == "alpha") {
        var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    } else if (type == "alpha_speacial_number") {
        var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789#$%&'()*+,-./:;<=>?@[\]^_`{|}~";
    }
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}


/**
 * Function for open delete modal with method DELETE
 * @param {string} URL
 * @param {string} target
 * @param {string} message
 * @returns
 */
function openDeleteModal(URL, target, message, actionBtnText = "Remove", method = "DELETE") {
    if (URL == "" || target == "") {
        return false;
    }

    if (message == "") {
        message = "Are you sure to delete ?";
    }
    var method = `<input type="hidden" name="_method" value="${method}">`;
    openModalByContent(
        {
            content: `<div class="card modal-alert border-0">
                        <div class="card-body">
                            <form method="POST" action="${URL}">
                                <input type="hidden" name="_token" value="${laravelCsrf()}">
                                ${method}
                                <div class="head mb-3">
                                    ${message}
                                    <input type="hidden" name="target" value="${target}">
                                </div>
                                <div class="foot d-flex align-items-center justify-content-between">
                                    <button type="button" class="modal-close btn btn--info">Close</button>
                                    <button type="submit" class="alert-submit-btn btn btn--danger btn-loading">${actionBtnText}</button>
                                </div>
                            </form>
                        </div>
                    </div>`,
        },

    );
}

// function openAlertModal(URL, target, message, actionBtnText = "Remove", method = "DELETE") {
//     if (URL == "" || target == "") {
//         return false;
//     }

//     if (message == "") {
//         message = "Are you sure to delete ?";
//     }
//     var method = `<input type="hidden" name="_method" value="${method}">`;
//     openModalByContent(
//         {
//             content: `<div class="card modal-alert border-0">
//                         <div class="card-body">
//                             <form method="POST" action="${URL}">
//                                 <input type="hidden" name="_token" value="${laravelCsrf()}">
//                                 ${method}
//                                 <div class="head mb-3">
//                                     ${message}
//                                     <input type="hidden" name="target" value="${target}">
//                                 </div>
//                                 <div class="foot d-flex align-items-center justify-content-between">
//                                     <button type="button" class="modal-close btn btn--info btn--base">{{__('Close')}}</button>
//                                     <button type="submit" class="alert-submit-btn btn btn--base bg--danger btn-loading">${actionBtnText}</button>
//                                 </div>
//                             </form>
//                         </div>
//                     </div>`,
//         },

//     );
// }

const pluck = property => element => element[property];

function placePhoneCode(code) {
    if (code != undefined) {
        code = code.replace("+", "");
        code = "+" + code;
        $("input.phone-code").val(code);
        $("div.phone-code").html(code);
    }
}


/**
 * Function For Get All Country list by AJAX Request
 * @param {HTML DOM} targetElement
 * @param {Error Place Element} errorElement
 * @returns
 */
var allCountries = "";
function getAllCountries(hitUrl,targetElement = $(".country-select"),errorElement = $(".country-select").siblings(".select2")) {
  if(targetElement.length == 0) {
    return false;
  }
  var CSRF = $("meta[name=csrf-token]").attr("content");
  var data = {
      _token      : CSRF,
  };
  $.post(hitUrl,data,function() {
      // success
      $(errorElement).removeClass("is-invalid");
      $(targetElement).siblings(".invalid-feedback").remove();
  }).done(function(response){
    // console.log(response);
      // Place States to States Field
      var options = "<option selected disabled>Select Country</option>";
      var selected_old_data = "";
      if($(targetElement).attr("data-old") != null) {
          selected_old_data = $(targetElement).attr("data-old");
      }
      $.each(response,function(index,item) {
          options += `<option value="${item.name}" data-id="${item.id}" data-mobile-code="${item.mobile_code}" data-currency-name="${item.currency_name}" data-currency-code="${item.currency_code}" data-iso2="${item.iso2}" data-currency-symbol="${item.currency_symbol}" ${selected_old_data == item.name ? "selected" : ""}>${item.name}</option>`;
      });

      allCountries = response;
      $(targetElement).html(options);
  }).fail(function(response) {
      var faildMessage = "Something went worng! Please try again.";
      var faildElement = `<span class="invalid-feedback" role="alert">
                              <strong>${faildMessage}</strong>
                          </span>`;
      $(errorElement).addClass("is-invalid");
      if($(targetElement).siblings(".invalid-feedback").length != 0) {
          $(targetElement).siblings(".invalid-feedback").text(faildMessage);
      }else {
          errorElement.after(faildElement);
      }
  });
}


/**
 * fetch data
 */
function fetchData(requestData, requestURL, loaderSelector = ".loader-spin"){
    $(loaderSelector).show();
    return new Promise((resolve, reject) => {
      $.get(requestURL, requestData, function(response) {
        // success response
      }).done((response) => {
        resolve(response);
        $(loaderSelector).hide();
      }).fail((response) => {
        let responseArray = [];
        try{
          responseArray = JSON.parse(response.responseText);
        }catch(error) {
          responseArray.push(error.message);
        }
        if(responseArray.errors != undefined) {
          responseArray = responseArray.errors;
        }
        if(responseArray?.message?.error) {
          responseArray = responseArray.message.error;
        }
        reject(responseArray);
        $(loaderSelector).hide();
      });
    });
  }
