/**
 * Set the Bootstrap Carousel items to be the
 * height of their container
 */
+function requireJquery($) {
  $(function onLoad() {
    var timer = 20000; /* 20 seconds */

    +function setUpReset() {
      setTimeout(function resetEveryTwoHours() {
        location.reload();
      }, 2 * 60 * 60 * 1000 /* 2 hour */ );
    }();

    +function setUpNoInteraction() {
      $('html').css({
        'overflow-y': 'hidden',
        'overflow-x': 'hidden'
      });
    }();

    +function setUpItemHeights() {
      $('.item').each(function setItemHeight(i, e) {
        var el,
          flexEl,
          closestSliderEl,
          height;

        el = $(e);
        flexEl = el.closest('.apiarium__flex');
        closestSliderEl = el.closest('.apiarium__slider');

        height = flexEl.height() - 10;
        // Substract the height of all other children
        flexEl.children().not(closestSliderEl).each(function(i, e) {
          height -= $(e).outerHeight();
        });

        el.css('height', height + 'px');

        var wordArray = e.innerHTML.split(' ');
        while (e.scrollHeight > e.offsetHeight && wordArray.length > 0) {
          wordArray.pop();
          e.innerHTML = wordArray.join(' ') + '...';
        }
      });
    }();

    +function setUpCarousels() {
      $('.carousel').carousel({
        pause: null,
        keyboard: false
      });
    }();

    +function setUpTweets() {
      setInterval(function animateTweets() {
        $('.apiarium__tweets').each(function(_, tweetContainer) {
          $(tweetContainer).find('.apiarium__tweet').first()
            .each(function animate(i, e) {
              $(e).animate(
                {
                  'margin-top': -$(e).height() + 'px'
                },
                'slow',
                function after() {
                  $(e).parent().append($(e));
                  $(e).css('margin-top', '0px');
                }
              );
            });
        });
      }, timer);

      setInterval(function updateTimes() {
        var $formatedTime = $('.apiarium__tweet__date time');
        var calculateRelativeTime = function(tweetTime) {
          var time = Math.round(+new Date() / 1000);
          var elapsedTime = time - tweetTime;
          var temp = '0h';
          if (elapsedTime < 1) {
            return 'now';
          }
          var timeConversion = [{
            secs: 1,
            unit: 's'
          }, {
            secs: 60,
            unit: 'm'
          }, {
            secs: 60 * 60,
            unit: 'h'
          }, {
            secs: 24 * 60 * 60,
            unit: 'd'
          }];

          for (var i = 0; i < timeConversion.length; i++) {
            var ratioElapsedToUnit = elapsedTime / timeConversion[i].secs;
            var roundedTime = Math.round(ratioElapsedToUnit);
            temp = roundedTime + timeConversion[i].unit;
            var notAFractionalUnit = ratioElapsedToUnit >= 1;
            var lessThanOneDay = timeConversion[i].unit == 'h' && ratioElapsedToUnit < 24;
            var lessThanOneHour = timeConversion[i].unit == 'm' && ratioElapsedToUnit < 60;
            var lessThanOneMinute = timeConversion[i].unit == 's' && ratioElapsedToUnit < 60;
            if (notAFractionalUnit && (lessThanOneDay || lessThanOneHour || lessThanOneMinute)) {
              return temp;
            }
          }
          return temp;
        };

        $formatedTime.each(function(i, e) {
          var oldTime = $(e).html();
          // Ignore the case there the date is already formatted
          // as a month
          if (oldTime.length <= 3) {
            var actualTime = $(e).data('actualTime');
            $(e).html(calculateRelativeTime(actualTime));
          }
        });
      }, timer);

      setInterval(function updateTweets() {
        $('.apiarium__tweets').each(function updateEachTweetSection(i, e) {
          var query = $(e).attr('data-query');
          var queryType = $(e).attr('data-query-type');
          var limit = $(e).attr('data-limit');

          var post_data = {
            action: 'apiarium-twitter',
            nonce: apiarium.nonce,
            serialized: JSON.stringify({
              query: query,
              queryType: queryType,
              limit: limit
            }),
          };

          $.post(
            apiarium.ajax_url,
            post_data,
            function success(response) {
              if (response.success) {
                $(e).replaceWith(response.data.script_response);
              }
            },
            'json'
          );
        });
      }, 5 * 60 * 1000 /* 5 minutes */ );
    }();

    +function setUpClock() {
      setInterval(function updateClock() {
        var date = new Date();
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var dateString = date.toString();
        var month = dateString.substring(4, 7);
        var currentDate = dateString.substring(8, 10);
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        hours = hours;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;

        $('.apiarium__calendar__time').html(strTime);
        $('.apiarium__calendar__date__icon__month').html(month);
        $('.apiarium__calendar__date__icon__date').html(currentDate);
      }, timer);
    }();

  });
}(jQuery);
