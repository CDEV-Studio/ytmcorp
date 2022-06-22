//---------------------------GENERAL-----------------------------//

var burgerOpen = false;
var modalWrapper = false;

//---------modal window template---------//
function modalWindow(content) {
    if (modalWrapper) {
        modalWrapper = false;
        $('.modal__wrapper').remove();
    } else {
        modalWrapper = true;
        var html = '<div class="modal__wrapper">';
        html += '<div class="modal__content"><div class="close"></div>';
        html += content;
        html += '</div></div>';

        $('body').prepend(html);

        $('.modal__content .close').on('click', function () {
            modalWrapper = false;
            $('.modal__wrapper').remove();
        });
    }
}
//-------------------------------------//

//---------menu burger for mobile---------//

function menuBurger() {
    var html = '<div class="burger__wrapper">';
    html += ' <div class="burger__menu"><ul><li><a href="#">О компании</a></li><li><a href="#">Помощь</a></li><li><a href="#">Контакты</a></li></ul></div>';
    html += ' <div class="burger__close close"></div>';
    html += '</div>';

    $('.header').append(html);

    $('.burger__menu>ul>li>a').on('click', function () {
        if (burgerOpen) {
            burgerOpen = false;
            $('.burger__wrapper').remove();
            $('body,html').css('overflow-y', 'auto');
        }
    });

    $('.burger__close').on('click', function () {
        $('.burger').trigger('click');
    });
};

//-------------------------------------//

//---------calculation of margin-left---------//

function marginLeft() {
    var windowWidth = $(window).width();
    var containerWidht = $('.container').outerWidth();
    var marginWidth = (windowWidth - containerWidht) / 2;
    $('.margin-table').css('width', marginWidth + 15 + 'px');
};

//-------------------------------------//

$(document).ready(function () {

    //---------------------------GENERAL-----------------------------//

    // my select

    $('.my-select>div:first-child').on('click', function (event) {
        $('.my-select>div:nth-child(2):not(#' + $($(this).parent('div')).attr('id') + '>div:nth-child(2))').css('display', 'none');
        $($(this).next('div')).slideToggle(0);
        $('.my-select').css('z-index', '2');
        $($(this).parent('div')).css('z-index', '3');
        $(this).toggleClass('active');

    });

    $('.my-select >div:nth-child(2)> div').on('click', function (event) {
        $('.my-select div:nth-child(2)> div').removeClass('active');
        $(this).addClass('active');

        $($($(this).parent()).next('input')).val($(this).attr('data'));

        $($($(this).parent()).prev("div")).text($(this).text()).trigger('click');

    });

    //close my select on click OUT of my select
    $(document).mouseup(function (e) {
        var div = $('.my-select>div:nth-child(2)');
        if (!div.is(e.target)
            && div.has(e.target).length === 0) {
            div.hide();
        }
    });

    // currency select

    $('.currency-select>div:first-child').on('click', function (event) {
        $('.currency-select>div:nth-child(2):not(#' + $($(this).parent('div')).attr('id') + '>div:nth-child(2))').css('display', 'none');
        $($(this).next('div')).slideToggle(0);
        $('.currency-select').css('z-index', '2');
        $($(this).parent('div')).css('z-index', '3');
        $(this).toggleClass('active');

    });

    $('.currency-select >div:nth-child(2)> div').on('click', function (event) {
        $('.currency-select div:nth-child(2)> div').removeClass('active');
        $(this).addClass('active');

        $($($(this).parent()).next('input')).val($(this).attr('data'));

        $($($(this).parent()).prev("div")).text($(this).text()).trigger('click');

    });

    //close currency select on click OUT of currency select
    $(document).mouseup(function (e) {
        var div = $('.currency-select>div:nth-child(2)');
        if (!div.is(e.target)
            && div.has(e.target).length === 0) {
            div.hide();
        }
    });

    //menu burger for mobile

    $('.burger').on('click', function () {
        if (burgerOpen) {
            burgerOpen = false;
            $('.burger__wrapper').remove();
            $('body,html').css('overflow-y', 'auto');
        } else {
            burgerOpen = true;
            menuBurger();
            $('body,html').css('overflow-y', 'hidden');
        }
    });

    //add to favorite

    $('.star').on('click', function () {
        $(this).toggleClass('active');
    });

    //check-box-select
    $('.checkbox-styled').on('click', function () {
        $(this).toggleClass('checkbox-styled-selected');
    });

    //show/hide password
    $('.password-control').on('mousedown', function () {
        return false;
    });

    $('.password-control').on('click', function (event) {
        if ($($(this).prev('input')).attr('type') == 'password') {
            $(this).addClass('view');
            $($(this).prev('input')).attr('type', 'text');
        } else {
            $(this).removeClass('view');
            $($(this).prev('input')).attr('type', 'password');
        }
        return false;
    });

    //hover on buttons 
    $('.icon-button').mouseover(function () {
        $($(this).children('p')).fadeToggle(100);
        $(this).mouseout(function () {
            $($(this).children('p')).css('display', 'none');
        });
    });

    //-------------------------------------//



    //-----------------8-calculation_history.html-----------------//

    //open/close filter      

    $('.calculation-history__filter .filter__open').on('click', function () {
        if ($(window).width() > 1110) {
            $(this).addClass('active');
            $('.filter__wrapper').fadeIn(600);
            $('.filter__wrapper .close').on('click', function () {
                $('.filter__open').removeClass('active');
                $('.filter__wrapper').fadeOut(600);
            });
        } else {
            $(this).toggleClass('active');
            $('.filter__wrapper').slideToggle(400);
        }
    });

    //show/hide notification

    $('.create-calculation .button-active').mouseover(function () {
        if ($(window).width() > 1110) {
            $('.create-calculation .notification').css('display', 'block');
            $(this).mouseout(function () {
                $('.create-calculation .notification').css('display', 'none');
            });
        } else {

        }
    });

    $('.notification .close').on('click', function () {
        if ($(window).width() < 1111) {
            $('.create-calculation .notification').css('display', 'none');
        } else {

        }
    });

    //modal window for create new calculation

    $('.create-calculation .button-active').on('click', function () {
        $('.create-calculation .notification').css('display', 'none');
        var html = '<div class="create-calculation__modal"><p class="modal__text">Чтобы произвести расчёт,<br> вам необходимо пополнить баланс.</p>';
        html += '<div class="button-link">Оплатить банковской картой</div>';
        html += '<div class="button-link">Выписать счёт для оплаты через банк</div></div>';
        modalWindow(html);
    });

    //-------------------------------------//



    //---------9-calculation_new.html  and   11-cargo_new.html---------//

    //open next step for creating new calculation

    $('.calculation-new__step1 .button-active').on('click', function () {
        $('#calculation-new__step2').slideDown(400);
        $('.calculation-new__step1 .calculation-new__wrapper').removeClass('white-background');
        $('.calculation-new__step1 .button-active').css('display', 'none');
        $('.calculation-new__step1 .calculation-new__wrapper').css('padding-bottom', '0');
        $('.calculation-new__step1 .calculation-new__wrapper').css('padding-top', '0');
    });

    $('.calculation-new__step2 .button-active').on('click', function () {
        $('#calculation-new__step3').slideDown(400);
        $('.calculation-new__step2 .calculation-new__wrapper').removeClass('white-background');
        $('.calculation-new__step2 .button-active').css('display', 'none');
        $('.calculation-new__step1 .calculation-new__wrapper').css('padding-bottom', '0');
        $('.calculation-new__step2 .calculation-new__wrapper').css('padding-bottom', '0');
        $('.calculation-new__step2 .calculation-new__wrapper').css('padding-top', '0');
    });

    $('.calculation-new__step3 .button-active').on('click', function () {
        $('#calculation-new__step4').slideDown(400);
        $('.calculation-new__step3 .calculation-new__wrapper').removeClass('white-background');
        $('.calculation-new__step3 .button-active').css('display', 'none');
        $('.calculation-new__step2 .calculation-new__wrapper').css('padding-bottom', '0');
        $('.calculation-new__step3 .calculation-new__wrapper').css('padding-bottom', '0');
        $('.calculation-new__step3 .calculation-new__wrapper').css('padding-top', '0');
    });

    $('.calculation-new__step4 .button-active').on('click', function () {
        $('#calculation-new__step5').slideDown(400);
        $('.calculation-new__step4 .calculation-new__wrapper').removeClass('white-background');
        $('.calculation-new__step4 .button-active').css('display', 'none');
        $('.calculation-new__step3 .calculation-new__wrapper').css('padding-bottom', '0');
        $('.calculation-new__step4 .calculation-new__wrapper').css('padding-bottom', '0');
        $('.calculation-new__step4 .calculation-new__wrapper').css('padding-top', '0');
    });

    //-------------------------------------//



    //-----17-cargo_data_offer.html  and  20-cargo_data_favorite.html  and  21-cargo_data_negotiations.html  and 23-cargo_data_approval.html  and  38-cargo_data_reject.html-----//

    //withdraw cargo - return cargo - on click

    $('.cargo__withdraw-return').on('click', function () {
        $('.cargo-info__withdraw-text').slideToggle(300);
        $('.cargo__withdraw-return>p:nth-child(2)').fadeToggle(0);
        $('.cargo__withdraw-return>p:nth-child(1)').fadeToggle(0);
    });

    //open summary of wagon offers

    $('.cargo-info__summary').on('click', function () {
        $(this).css('display', 'none');
        $('.cargo-info__summary-card').fadeIn(300);
        $('.cargo-info__summary-close').on('click', function () {
            $('.cargo-info__summary').css('display', 'block');
            $('.cargo-info__summary-card').fadeOut(0);
        });
    });

    //change content for wagon offers

    $('.wagon-tabs>div').on('click', function () {
        $('.wagon-tabs>div').removeClass('active');
        var wagonTabNumber = $(this).attr('class');
        $('.wagon-items').css('display', 'none');
        $('#' + wagonTabNumber).css('display', 'grid');
        $(this).addClass('active');
    });

    //wagon-side-menu for mobile version

    $('.wagon-side-menu .mobile-active').on('click', function () {
        $('.wagon-side-menu__mobile').toggleClass('close');
        $('.wagon-side-menu__wrapper').slideToggle(300);
    });


    //-------------------------------------//



    //-------------71-wagon_offer.html and 69-subscribe.html -------------//

    //calculation of margin-left
    marginLeft();

    //-------------------------------------//



    //-------------73-supplier_search.html-------------//

    //send cargo info
    $('.supplier-table tbody tr td .button-active').on('click', function () {
        $(this).css('display', 'none');
        $(this).next('p').css('display', 'block');
    });

    //-------------------------------------//



    //---------------------------window resize-----------------------------//

    $(window).resize(function () {
        marginLeft();
        if ($(window).width() > 1110) {
            $('.calculation-history__filter .filter__wrapper').css('display', 'none');
            $('.calculation-history__filter .filter__open').removeClass('active');
            $('.wagon-side-menu__wrapper').css('display', 'block');
            $('.wagon-side-menu .mobile-active').css('display', 'none');
        } else {
            $('.wagon-side-menu .mobile-active').css('display', 'flex');
            $('.wagon-side-menu__wrapper').css('display', 'none');

        }
    });

    //-------------------------------------//

});

