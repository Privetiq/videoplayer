function initializeMasonry() {
    //  initialize Masonry
    jQuery('#mosaic-holder').masonry({
        itemSelector: '.mosaic-content'
    });

    jQuery('#moments-holder').masonry({
        itemSelector: '.moment-content'
    });
}

function voteCallback(contestant_id, network) {
    if (contestant_id == 0) {
        jQuery('.popup:visible .error').html('Ви вже голосували через цю мережу,<br/>ви можете голосувати через рiзнi соцiальнi мережi');
    } else {
        jQuery('body').data('network', network);
        hidePopup();

        if (jQuery('[data-contestantid="' + contestant_id + '"] .vote-btn').length > 0) {
            jQuery('[data-contestantid="' + contestant_id + '"] .vote-btn').click();
        } else {
            jQuery('a.vote-btn[data-contestantid="' + contestant_id + '"]').click();
        }

    }

}

function showPopup($popup) {
    $popup.fadeTo(300, 1, function () {
        var winHeight = jQuery(window).height();
        $popup.animate({'top': winHeight / 2 - $popup.outerHeight() / 2}, 500);
    });
    jQuery('.popup-overlay').fadeTo(300, 0.7);
}

function hidePopup() {
    jQuery('.popup:visible').fadeOut(300);
    jQuery('.popup-overlay').fadeOut(300);

    if (jQuery('#video-popup.popup:visible').length > 0) {
        jQuery('#video-popup .video-holder').html();
    }
}

function msgPopup(msg) {
    var $popup = jQuery('#msg-popup');
    $popup.find('p').html(msg);

    showPopup($popup);
}

function share(network, shareUrl) {
    var link;
    if (typeof shareUrl == 'undefined') {
        shareUrl = window.location.href;
    }

    switch (network) {
        case 'Vkontakte':
            link = "http://vkontakte.ru/share.php?url={{shareUrl}}";
            break;
        case 'Facebook':
            link = "https://www.facebook.com/sharer/sharer.php?u={{shareUrl}}";
            break;
    }

    link = link.replace('{{shareUrl}}', shareUrl);

    window.open(link, '', 'toolbar=0,status=0,width=626,height=436');
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
    }
    return "";
}

jQuery(function ($) {
    var $menu, vote, voteTop, voteBottom, viewportHeight, loginWindow;
    var currentScreen = 'home';
    var $screens = $('section');

    $('#mosaic-holder').on('click', '.mosaic-content', function () {
        $(window).resize();

        $('#question-popup-content').html($(this).html());
        $('#question-popup').show();
        $('#question-popup').css('marginTop', -$('#question-popup').outerHeight() / 2);

        $('.questions-overlay').show();

    });

    $(window).resize(function() {
        var contentWidth = $('#questions').width();

        $('.questions-overlay').css({
            'width' : contentWidth,
            'marginLeft' : ($(window).width() - contentWidth) / 2
        })
    });

    $('.question-popup-close').click(function (e) {
        e.preventDefault();

        $('.questions-overlay').hide();
        $('#question-popup').hide();
    });

    /*
     * Video
     */
    //player.online('video-holder', '189931', '1','http://www.viglee.com/wp-content/uploads/2014/06/Justin-Bieber-Diprotes-Gara-Gara-Video-Rasis.jpg', 1000, 500, 0);

    /*
     * Links
     */
    $('a[href^="#"]').on('click', function (e) {
        var href = $(this).attr('href');
        if (href.length > 1) {
            if ($(href).length > 0) {
                $('html, body').animate({
                    scrollTop: $(href).offset().top
                }, 500, function () {
                    $(window).scroll();
                });
            }

            e.preventDefault();
        }
    });

    /*
     * Popup
     */
    $('.close-popup-btn').on('click', function (e) {
        e.preventDefault();
        hidePopup();
    });

    /*
     * Click on current language icon
     */
    $('.langs .active').on('click', function (e) {
        e.preventDefault();

        $('.langs a').removeClass('hidden');
    });

    $('.langs .to-choose').on('click', function (e) {
        if (!$(this).hasClass('to-choose')) {
            e.preventDefault();
        }
        $('.langs a').removeClass('active').removeClass('to-choose');
        $(this).addClass('active').siblings('a').addClass('hidden');
    });

    /*
     * Click on SEARCH icon
     */
    $('#search-icon').on('click', function (e) {
        e.preventDefault();

        $(this).addClass('hidden');
        $(this).closest('div').find('#searchform').removeClass('hidden');
    });

    /*
     * Click on contestant -> show description
     */
    $('.contestant-photo').on('click', function (e) {

        var $this = $(this);
        var $holder = $this.closest('#contestants');

        if ($holder.length == 0) {
            $holder = $this.closest('#experts-holder');
        }

        $holder.find('.contestant-photo').removeClass('active');


        $this.addClass('active');
        setTimeout(function () {
            $holder.addClass('active');
        }, 1000);

        $holder.addClass('active_');

        var contestantId = $(this).closest('.contestant').data('contestantid');

        if (contestantId == null) {
            var expertId = $(this).closest('.contestant').data('expertid');

            $holder.find('.contestant-description').addClass('visible-description');
            $holder.find('.contestant-description p').addClass('hidden');

            $holder.find('.contestant-description p[data-expertid="' + expertId + '"]').removeClass('hidden');

        } else {

            $holder.find('.contestant-description').addClass('visible-description');
            $holder.find('.contestant-description p').addClass('hidden');

            $holder.find('.contestant-description p[data-contestantid="' + contestantId + '"]').removeClass('hidden');
        }


    });

    /*
     * Close contestant description
     */
    $('.contestant-description .close-btn').on('click', function (e) {
        e.preventDefault();

        var $this = $(this);
        var $holder = $this.closest('#contestants');

        if ($holder.length == 0) {
            $holder = $this.closest('#experts-holder');
        }

        $holder.removeClass('active_').removeClass('active');
        setTimeout(function () {
            $holder.find('.contestant-photo').removeClass('active');
        }, 50);
        $holder.find('.contestant-description').removeClass('visible-description');

    });

    /*
     * Click on moment -> show description
     */
    $('.moment-content').live('click', function (e) {
        e.preventDefault();
        $('.moment-content').removeClass('active');

        var $this = $(this);
        if ($(e.target).is('.social-links a') || $(e.target).is('.social-links')) {
            return;
        }

        var momentId = $(this).data('momentid');

        $this.find('.popup-shadow').removeClass('hidden');
        $this.find('.moment-description').addClass('visible-description');
        $this.find('.moment-description .single-moment-description').addClass('hidden');
        $this.find('.moment-description .single-moment-description[data-momentid="' + momentId + '"]').removeClass('hidden');

        $(window).resize();
    });

    /*
     * Close moment description
     */
    $('.moment-description .close-btn').live('click', function (e) {
        e.preventDefault();

        $(this).closest('.moment-description-holder').find('.popup-shadow').addClass('hidden');
        $(this).closest('.moment-description').removeClass('visible-description');
        var videoURL = $(this).closest('.moment-description').find('.single-moment-description iframe').clone();
        $(this).closest('.moment-description').find('.single-moment-description iframe').replaceWith(videoURL);

        return false;

    });

    /*
     * Click on "ЗАПИТАННЯ"
     */
    $('#write-question').on('click', function (e) {
        e.preventDefault();

        $('#question-form').removeClass('hidden');
    });

    /*
     * Close question form
     */
    $('#question-form .close-btn').on('click', function (e) {
        e.preventDefault();

        $('#question-form').addClass('hidden');
    });

    /*
     * Voting
     */
    $('.vote-btn').on('click', function (e) {
        e.preventDefault();

        var $this = $(this);
        if ($this.hasClass('disabled') || $this.hasClass('voted')) {
            return false;
        }

        var contestant_id = $this.parent().data('contestantid');

        if (typeof contestant_id == 'undefined') {
            contestant_id = $this.data('contestantid');
        }

        var data = {
            'action': 'preVote',
            'contestant_id': contestant_id
        }

        if ($this.hasClass('second-poll')) {
            data.second_poll = true;
        }

        var network = $('body').data('network');

        $.post('/wp-admin/admin-ajax.php', data, function (txt) {
            if (txt == 'login_needed') {
                $this.removeClass('disabled');
                var $popup = $('#login-popup');
                $popup.find('.error').text('')
                $popup.data('id', contestant_id);
                showPopup($popup);
            } else {

                if (txt.substr(0, 7) == 'already') {
                    txt = txt.substr(8);
                    msgPopup('Ви вже проголосували!');
                }

                var $btn = $('[data-contestantid="' + txt + '"] .vote-btn');

                if ($btn.length > 0) {
                    $btn
                        .addClass('voted')
                        .parent()
                        .siblings()
                        .each(function () {
                            $(this).find('.vote-btn').addClass('disabled');
                        });

                } else {
                    $btn = $('a.vote-btn[data-contestantid="' + txt + '"]');

                    $btn
                        .addClass('voted')
                        .siblings()
                        .addClass('disabled');

                }


            }
        }, 'text');
    });

    $('.login-vk,.login-fb').on('click', function (e) {
        e.preventDefault();

        var $popup = $(this).closest('.popup');
        var contestant_id = $popup.data('id');
        var network = $(this).data('network');


        if (typeof loginWindow != 'undefined' && typeof loginWindow.close != 'undefined') {
            loginWindow.close();
        }

        // login and then callback
        loginWindow = window.open("/?action=login&network=" + network + "&contestant_id=" + contestant_id, 'Login via ' + network, "location=0,menubar=0,status=0,height=380,width=660,top=" + $popup.position().top + ",left=" + $popup.position().left);
    });


    /*
     * Paralax
     */
    if (!(/Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i).test(navigator.userAgent || navigator.vendor || window.opera)) {
        var s = skrollr.init({
            'forceHeight': false,
            'smoothScrolling': false
        });
    }

    /*
     * Intro video
     */
    function closeIntro() {
        var $intro = $('#intro');

        $intro.stop().animate({
            'top': '-100%'
        }, 400);

        $intro.find('iframe').remove();
    }

    $('#intro').on('click', '#close-intro-btn', function (e) {
        e.preventDefault();
        closeIntro();
    });

    $('a[href="#intro"]').on('click', function (e) {
        e.preventDefault();

        var $intro = $('#intro');
        var url = $intro.data('url');

        var $iframe = $('<iframe />').attr({
            'id': 'intro-player',
            'allowfullscreen': 'allowfullscreen',
            'frameborder': '0',
            'src': url
        });

        $intro.html($iframe);

        $intro.stop().animate({
            'top': 0
        }, 400);

        new YT.Player('intro-player', {
            events: {
                'onStateChange': function (event) {
                    if (event.data == YT.PlayerState.ENDED) {
                        closeIntro();
                    }
                }
            }
        });

        $intro.prepend('<a href="#" id="close-intro-btn"></a>');

    });

    /*
     * Calculate positions on resize
     */
    if ($('body.home').length > 0) {
        $(window).resize(function () {
            $menu = $('#menu');
            $vote = $('#vote');

            if ($vote.length === 0) {
                return;
            }

            voteTop = $vote.offset().top;
            voteBottom = voteTop + $vote.outerHeight();

            viewportHeight = $(window).height();
        });

        $(window).resize(); //trigger resize

        $(window).scroll(function () {

            var scrollTop = $(window).scrollTop();

            var sectionPos;

            $screens.each(function () {
                sectionPosTop = $(this).get(0).getBoundingClientRect().top;
                var diff = (viewportHeight / 2) > $(this).height() ? $(this).height() : (viewportHeight / 2);

                if (sectionPosTop > -diff && sectionPosTop < diff) {
                    $('body').data('page', $(this).attr('id'));
                    return false;
                }
            });

            $menu.find('li').add('#logo').each(function () {
                var pos = $(this).offset().top + $(this).height() / 2;

                if (pos > voteTop && pos < voteBottom) {
                    $(this).addClass('vote-page');
                } else {
                    $(this).removeClass('vote-page');
                }
            });

            var currentPage = $('body').data('page');

            $('#menu a[href="#' + currentPage + '"]').closest('li').addClass('current').siblings().removeClass('current');

            if (currentPage != 'home' && $('#menu').is(':hidden') && scrollTop >= $('#home').height()) {
                $('#menu').fadeIn(500);
            }
        });

        $(window).scroll(); //trigger scroll
    }


    $('#submit-question-form').on('submit', function (e) {
        e.preventDefault();

        var form = $(this);
        var data = form.serialize();

        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: data + '&action=submit_question',
            success: function (response) {
                if (response === 'success') {
                    form.get(0).reset();
                    $('.success').text(form.data('success'));
                    $('#submit-question-form [type="submit"]').attr('disabled', true).css({opacity: .5});
                } else {
                    $(response).addClass('error');
                }
            }
        });

        return false;
    });

    $(document).on('change', '.error', function () {
        $(this).removeClass('error');
    })


    // next questions button
    $('.next-questions').on('click', function (e) {
        e.preventDefault();
        var link = $(this);

        var container = $('#mosaic-holder');
        var lang = $('body:first').data('language');
        var skip = container.children().length;
        var episode = $('#current_voting_id').val();

        $.get(ajaxurl + '?action=next_questions&lang=' + lang + '&skip=' + skip + '&episode=' + episode, function (response) {

            if (!response || response === 'no more questions') {
                link.animate({opacity: 0}, function () {
                    link.css('visibility', 'hidden');
                });
                return;
            }

            var newQuestions = $(response);
            container.append(newQuestions);
            container.masonry('appended', newQuestions);


            if (newQuestions.filter('div').length < 8) {
                link.animate({opacity: 0}, function () {
                    link.css('visibility', 'hidden');
                });
                return;
            }

        });

    });

    // next moments button
    $('.next-moments').on('click', function (e) {
        e.preventDefault();
        var link = $(this);

        var container = $('#moments-holder');
        var lang = $('body:first').data('language');
        var skip = container.children().length;
        var episode = container.data('episode');

        $.get(ajaxurl + '?action=next_moments&lang=' + lang + '&skip=' + skip + '&episode=' + episode, function (response) {

            if (response == '' || response === 'no more moments') {
                link.animate({opacity: 0}, function () {
                    link.remove();
                });
                return;
            }

            var newMoments = $(response);
            newMoments.css('visibility', 'hidden');
            container.append(newMoments);

            container.imagesLoaded(function () {
                newMoments.css('visibility', 'visible');
                container.masonry('appended', newMoments);
            });


            if (newMoments.filter('div').length < 4) {
                link.animate({opacity: 0}, function () {
                    link.remove();
                });
                return;
            }

        });

    });

    $('#searchform input').keyup(function() {
        $('#searchform').submit();
    });

    $('#searchform').submit(function (event) {
        event.preventDefault();

        var searchFor = $(this).find('input').val().toLowerCase();

        if (!searchFor) {
            $('.episode-holder').show();
            return false;
        }

        var found = false;

        $('.episode-holder').each(function () {
            var txt = $(this).text().toLowerCase();

            if (txt.indexOf(searchFor) != -1) {
                found = true;
                return false;
            }
        });

        if (found) {
            $('.episode-holder').each(function () {
                var txt = $(this).text().toLowerCase();

                if (txt.indexOf(searchFor) != -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $('#searchform').find('.error').addClass('hidden');
        } else {
            $('#searchform').find('.error').removeClass('hidden');

        }

        return;

        /*
        $.get(ajaxurl + '?action=search_for' + '&search=' + searchFor, function (response) {
            var container = $('#archive');
            var errorHolder = $('#searchform').find('.error');

            if (!response || response === 'no results') {
                errorHolder.removeClass('hidden');
                return;
            }

            errorHolder.addClass('hidden');
            var searchResults = $(response);
            container.html(searchResults);

        });
        */
    });

    /**
     * Auto-update on homepage
     */
    if ($('body.home').length > 0) {
        setInterval(function () {
            $.get(location.href, {}, function (html) {
                var $html = $(html);

                var $moments = $html.find('.moment-content');

                var moments = [];
                $moments.each(function () {
                    var id = $(this).data('momentid');

                    if ($('.moment-content[data-momentid="' + id + '"]').length == 0) {
                        moments.push($(this).get(0));
                    }
                });

                $('#moments-holder').prepend(moments).imagesLoaded(function () {
                    $('#moments-holder').masonry('prepended', moments);
                });


                var $questions = $html.find('.mosaic-content');
                var last_question_id = $('.mosaic-content:first').data('questionid');

                var questions = [];
                $questions.each(function () {
                    var id = $(this).data('questionid');

                    if ($('.mosaic-content[data-questionid="' + id + '"]').length == 0) {
                        questions.push($(this).get(0));
                    }
                });

                $('#mosaic-holder').prepend(questions).imagesLoaded(function () {
                    $('#mosaic-holder').masonry('prepended', questions);
                });

            }, 'html');
        }, 30000);
    }

    var voted = getCookie('voted');
    var voted2 = getCookie('voted2');

    if (voted != '') {
        var $votedBtn = $('[data-contestantid="' + voted + '"] .vote-btn');
        if ($('[data-contestantid="' + voted + '"] .vote-btn') > 0) {

            $('[data-contestantid="' + voted + '"] .vote-btn')
                .addClass('voted')
                .siblings().each(function () {
                    $(this).find('.vote-btn').addClass('disabled');
                });

        } else {

            $('a.vote-btn[data-contestantid="' + voted + '"]')
                .addClass('voted')
                .siblings()
                .addClass('disabled');
        }

    }

    if (voted2 != '') {
        var $votedBtn = $('[data-contestantid="' + voted2 + '"] .vote-btn');
        if ($('[data-contestantid="' + voted2 + '"] .vote-btn') > 0) {

            $('[data-contestantid="' + voted2 + '"] .vote-btn')
                .addClass('voted')
                .siblings().each(function () {
                    $(this).find('.vote-btn').addClass('disabled');
                });

        } else {

            $('a.vote-btn[data-contestantid="' + voted2 + '"]')
                .addClass('voted')
                .siblings()
                .addClass('disabled');
        }
    }

    $('.member-link').click(function (e) {
        e.preventDefault();

        var $container = $(this).closest('.member-container');

        $('.member-info.visible').removeClass('visible');
        $container.find('.member-info').addClass('visible');
    });

    $('.member-info .close-popup-btn').click(function (e) {
        e.preventDefault();

        $(this).closest('.member-info').removeClass('visible');
    });

    /*
     $('.red-vote-btn').click(function(e) {
     e.preventDefault();

     if ($('#poll1').length > 0) {

     } else if ($('#poll2').length > 0) {

     }
     });
     */

    $(window).resize(function () {
        $('iframe[src^="https://tsn.ua/bin/player/iframe/"]').add('iframe[src^="//ovva.tv/"]').add('iframe[src^="https://ovva.tv/"]')
        .each(function () {
            var width = $(this).height() * 1.17777;//1.25925;
            $(this).attr('width', width);
        });
    }).resize();
});

jQuery(window).on('load', initializeMasonry);
