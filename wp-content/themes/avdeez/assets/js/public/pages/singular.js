'use strict';

(function () {
  if ('loading' === document.readyState) {
    // The DOM has not yet been loaded.
    document.addEventListener('DOMContentLoaded', initSingular);
  } else {
    // The DOM has already been loaded.
    initSingular();
  } // Initiate the header search when the DOM loads.

  function initSingular() {
    var pageHeader = document.getElementById('primary-header');

    if (null === pageHeader) {
      return;
    }

    var siteHeaderHeight = document.getElementById('masthead').offsetHeight;
    var stickyHeader = document.getElementById('sticky-header');
    var contentPosition = pageHeader.offsetHeight;

    if (stickyHeader) {
      contentPosition = contentPosition + stickyHeader.offsetHeight;
    }

    setPageHeaderAnimation(siteHeaderHeight, contentPosition);
    setSkipToEntryContentAction(contentPosition);
  } // Initiate the page header animation.

  function setPageHeaderAnimation(siteHeaderHeight, contentPosition) {
    if (!window.requestAnimationFrame) {
      return;
    }

    var pageHeader = document.getElementById('primary-header');

    if (null === pageHeader) {
      return;
    }

    var scrollTop = 0;
    var pageHeaderContainer = pageHeader.querySelector(
      '.wp-block-cover__inner-container'
    );

    var createHeaderAnimation = function createHeaderAnimation() {
      scrollTop = scrollTop - siteHeaderHeight;
      pageHeaderContainer.style.transform = 'translateY('.concat(
        Math.round(scrollTop / 3),
        'px)'
      );
      pageHeaderContainer.style.opacity = 1 - scrollTop / 500;
    };

    var removeHeaderAnimation = function removeHeaderAnimation() {
      pageHeaderContainer.style.transform = '';
      pageHeaderContainer.style.opacity = '';
    };

    var headerAnimationEvent = function headerAnimationEvent() {
      scrollTop = window.scrollY || document.documentElement.scrollTop;

      if (scrollTop >= siteHeaderHeight && scrollTop <= contentPosition) {
        window.requestAnimationFrame(createHeaderAnimation);
      } else if (scrollTop <= siteHeaderHeight) {
        window.requestAnimationFrame(removeHeaderAnimation);
      }
    };

    try {
      var io = new IntersectionObserver(
        function (entries) {
          // Element enters the viewport.
          if (entries[0].intersectionRatio !== 0) {
            window.addEventListener('scroll', headerAnimationEvent, false);
          } else {
            window.removeEventListener('scroll', headerAnimationEvent, false);
          }
        },
        {
          threshold: 0,
        }
      ); // Start observing.

      io.observe(pageHeader);
    } catch (error) {
      window.addEventListener('scroll', headerAnimationEvent, false);
    }
  } // Skip to entry content button.

  function setSkipToEntryContentAction(contentPosition) {
    var skipToEntryButton = document.getElementById('skip-to-entry');

    if (null === skipToEntryButton) {
      return;
    }

    var buttonAction = function buttonAction() {
      try {
        window.scrollTo({
          top: contentPosition,
          left: 0,
          behavior: 'smooth',
        });
      } catch (error) {
        window.scrollTo(0, contentPosition);
      }
    };

    skipToEntryButton.addEventListener('click', buttonAction, false);
  }
})();
