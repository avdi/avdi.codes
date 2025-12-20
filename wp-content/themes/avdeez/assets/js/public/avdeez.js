'use strict';

(function () {
  if ('loading' === document.readyState) {
    // The DOM has not yet been loaded.
    document.addEventListener('DOMContentLoaded', initNavigation);
  } else {
    // The DOM has already been loaded.
    initNavigation();
  } // Initiate the menus when the DOM loads.

  function initNavigation() {
    if (!document.documentElement.classList) {
      return;
    }

    var menuContainer = document.getElementById('site-navigation');

    if (!menuContainer) {
      return;
    }

    setToggleSubmenuOnFocus(menuContainer);
    setToggleSubmenuOnTouch(menuContainer);
    createMobileMenu(menuContainer);
  }
  /**
   * Created a mobile menu based on the primary menu.
   *
   * @param {Object} container
   */

  function createMobileMenu(container) {
    var siteHeader = document.getElementById('masthead');

    if (!siteHeader) {
      return;
    }

    var mobileMenuContainer = document.getElementById('mobile-navigation');
    var headerMenus = siteHeader.querySelectorAll('.menu');

    if (!headerMenus.length) {
      return;
    }

    var mobileContent = document.createDocumentFragment();

    for (var menuIndex = 0; menuIndex < headerMenus.length; menuIndex++) {
      var currentMenu = headerMenus[menuIndex].cloneNode(true);
      currentMenu.removeAttribute('id');
      mobileContent.appendChild(currentMenu);
    } // Add a search form and list items to the mobile container.

    mobileMenuContainer.appendChild(mobileContent); // Remove ID attributes from the list items to avoid repetition.

    var mobileMenuItems = mobileMenuContainer.getElementsByTagName('li');
    var mobileMenuItemsCount = mobileMenuItems.length;

    if (mobileMenuItemsCount) {
      for (var itemIndex = 0; itemIndex < mobileMenuItemsCount; itemIndex++) {
        mobileMenuItems[itemIndex].removeAttribute('id');
      }
    } // Add dropdown toggle that displays child menu items.

    var parentMenuItems = mobileMenuContainer.querySelectorAll(
      '.menu-item-has-children'
    );

    if (parentMenuItems.length) {
      for (var i = 0; i < parentMenuItems.length; i++) {
        var buttonScreenReaderText = document.createElement('span');
        buttonScreenReaderText.className = 'screen-reader-text';
        buttonScreenReaderText.appendChild(
          document.createTextNode(screenReaderText.expand)
        );
        var dropdownToggle = document.createElement('button');
        dropdownToggle.className = 'dropdown-toggle clean-button has-icon';
        dropdownToggle.setAttribute('aria-expanded', false);
        dropdownToggle.appendChild(buttonScreenReaderText);
        parentMenuItems[i].appendChild(dropdownToggle);
        parentMenuItems[i].setAttribute('aria-haspopup', true);
      }
    } // Toggle buttons and submenu items with active children menu items.

    var activeToggleButtons = mobileMenuContainer.querySelectorAll(
      '.current-menu-ancestor > button'
    );
    var activeToggleSubMenus = mobileMenuContainer.querySelectorAll(
      '.current-menu-ancestor > .sub-menu'
    );

    if (activeToggleButtons.length) {
      for (var i = 0; i < activeToggleButtons.length; i++) {
        activeToggleButtons[i].classList.add('toggled-on');
        activeToggleButtons[i].setAttribute('aria-expanded', true);
      }
    }

    if (activeToggleSubMenus.length) {
      for (var i = 0; i < activeToggleSubMenus.length; i++) {
        activeToggleSubMenus[i].classList.add('toggled-on');
      }
    }

    var dropdownToggleButtons =
      mobileMenuContainer.getElementsByTagName('button');

    if (dropdownToggleButtons.length) {
      var dropdownToggleEvent = function dropdownToggleEvent(e) {
        e.preventDefault();
        var arrowIconElement =
          e.target.parentElement.querySelector('.arrow-icon');
        arrowIconElement.classList.toggle('toggled-on');
        var screenReader = e.target.querySelector('.screen-reader-text');

        if (-1 !== e.target.className.indexOf('toggled-on')) {
          e.target.setAttribute('aria-expanded', false);

          if (null != screenReader) {
            screenReader.textContent = screenReaderText.collapse;
          }
        } else {
          e.target.setAttribute('aria-expanded', true);

          if (null != screenReader) {
            screenReader.textContent = screenReaderText.expand;
          }
        }

        var parentItem = e.target.parentNode;

        for (var i = 0; i < parentItem.childNodes.length; i++) {
          if ('UL' === parentItem.childNodes[i].nodeName) {
            parentItem.childNodes[i].classList.toggle('toggled-on');
            break;
          }
        }
      };

      for (var i = 0; i < dropdownToggleButtons.length; i++) {
        dropdownToggleButtons[i].addEventListener(
          'click',
          dropdownToggleEvent,
          false
        );
      }
    }
  }
  /**
   * Toggle `focus` class to allow sub-menu access on focus and blur.
   *
   * @param {Object} container
   */

  function setToggleSubmenuOnFocus(container) {
    // Get the first ul element insite the menu container.
    var primaryMenu = container.getElementsByTagName('ul')[0]; // Get all the link elements within the menu.

    var menuLinks = primaryMenu.getElementsByTagName('a');
    var i, focusFn; // Sets or removes .focus class on an element.

    focusFn = function focusFn() {
      var self = this; // Move up through the ancestors of the current link until we hit .nav-menu.

      while (
        !self.classList.contains('site-menu') &&
        'nav' !== self.tagName.toLowerCase()
      ) {
        // On li elements toggle the class .focus.
        if ('li' === self.tagName.toLowerCase()) {
          if (self.classList.contains('focus')) {
            self.classList.remove('focus');
          } else {
            self.classList.add('focus');
          }
        }

        self = self.parentElement;
      }
    }; // Each time a menu link is focused or blurred, toggle focus.

    for (i = 0; i < menuLinks.length; i++) {
      menuLinks[i].addEventListener('focus', focusFn, false);
      menuLinks[i].addEventListener('blur', focusFn, false);
    }
  }
  /**
   * Toggle `focus` class to allow sub-menu access on touch screens.
   *
   * @param {Object} container
   */

  function setToggleSubmenuOnTouch(container) {
    var touchStartFn,
      touchOutsideFn,
      removeFocusFn,
      i,
      parentLink = container.querySelectorAll(
        '.menu-item-has-children > a, .page_item_has_children > a'
      );

    removeFocusFn = function removeFocusFn() {
      var focusedElements = container.querySelectorAll('li.focus');
      var i;

      for (i = 0; i < focusedElements.length; ++i) {
        focusedElements[i].classList.remove('focus');
      }
    };

    touchStartFn = function touchStartFn(e) {
      var menuItem = this.parentNode,
        i;

      if (!menuItem.classList.contains('focus')) {
        e.preventDefault();

        for (i = 0; i < menuItem.parentNode.children.length; ++i) {
          if (menuItem === menuItem.parentNode.children[i]) {
            continue;
          }

          menuItem.parentNode.children[i].classList.remove('focus');
        }

        if (!container.classList.contains('is-touched')) {
          container.classList.add('is-touched');
        }

        menuItem.classList.add('focus');
      } else {
        menuItem.classList.remove('focus');
      }
    };

    touchOutsideFn = function touchOutsideFn(e) {
      var isTochedMenu = container.classList.contains('is-touched');

      if (!isTochedMenu) {
        return;
      }

      var elementParent = e.target.parentNode;

      if (
        elementParent &&
        !elementParent.classList.contains('menu-item') &&
        isTochedMenu
      ) {
        removeFocusFn();
      }
    };

    document.addEventListener('touchstart', touchOutsideFn, false);

    for (i = 0; i < parentLink.length; ++i) {
      parentLink[i].addEventListener('touchstart', touchStartFn, false);
    }
  }
})();

(function () {
  if ('loading' === document.readyState) {
    // The DOM has not yet been loaded.
    document.addEventListener('DOMContentLoaded', initStickyMenu);
  } else {
    // The DOM has already been loaded.
    initStickyMenu();
  } // Initiate the sticky menu when the DOM loads.

  function initStickyMenu() {
    if (!document.documentElement.classList) {
      return;
    }

    var stickyHeader = document.getElementById('sticky-header');

    if (null === stickyHeader) {
      return;
    }

    stickyHeader.style.display = 'none';
    var siteHeader = document.getElementById('masthead');
    var headerContainerTop = siteHeader.offsetHeight;
    var primaryMenu = document
      .getElementById('site-navigation')
      .cloneNode(true);
    primaryMenu.removeAttribute('id');
    stickyHeader.appendChild(primaryMenu);
    stickyHeader.appendChild(
      siteHeader.querySelector('.header-controls').cloneNode(true)
    );
    stickyHeader.style.display = '';

    try {
      var io = new IntersectionObserver(
        function (entries) {
          // Element enters the viewport.
          if (entries[0].intersectionRatio === 0) {
            stickyHeader.classList.add('visible');
          } else {
            stickyHeader.classList.remove('visible');
          }
        },
        {
          threshold: 0,
        }
      ); // Start observing.

      io.observe(siteHeader);
    } catch (error) {
      var setStickyHeaderClass = function setStickyHeaderClass() {
        var scrollTop = window.scrollY || document.documentElement.scrollTop;

        if (scrollTop > headerContainerTop) {
          stickyHeader.classList.add('visible');
        } else {
          stickyHeader.classList.remove('visible');
        }
      };

      setStickyHeaderClass();

      var stickyHeaderEvent = function stickyHeaderEvent() {
        window.requestAnimationFrame(setStickyHeaderClass);
      };

      window.addEventListener('scroll', stickyHeaderEvent, false);
    }
  }
})();

(function () {
  if ('loading' === document.readyState) {
    // The DOM has not yet been loaded.
    document.addEventListener('DOMContentLoaded', initSearchForm);
  } else {
    // The DOM has already been loaded.
    initSearchForm();
  } // Initiate the header search when the DOM loads.

  function initSearchForm() {
    var formOpenButtons = document.querySelectorAll('.header-search-button');
    var searchContainer = document.getElementById('search-container');
    var formCloseButton = document.getElementById('close-search-container');

    if (!formOpenButtons.length) {
      return;
    } // Open a search form.

    var openSearchForm = function openSearchForm() {
      for (var i = 0; i < formOpenButtons.length; i++) {
        formOpenButtons[i].setAttribute('aria-expanded', true);
      }

      searchContainer.setAttribute('aria-hidden', false);
      document.getElementById('header-search-field').focus();
    };

    for (var i = 0; i < formOpenButtons.length; i++) {
      formOpenButtons[i].addEventListener('click', openSearchForm, false);
    } // Close a search form.

    var closeSearchForm = function closeSearchForm() {
      for (var i = 0; i < formOpenButtons.length; i++) {
        formOpenButtons[i].setAttribute('aria-expanded', false);
      }

      searchContainer.setAttribute('aria-hidden', true);
    };

    formCloseButton.addEventListener('click', closeSearchForm, false);

    var closeSearchWithKey = function closeSearchWithKey(e) {
      if (
        (e.key == 'Escape' || e.key == 'Esc' || e.keyCode == 27) &&
        searchContainer.classList.contains('th-flex')
      ) {
        closeSearchForm();
      }
    };

    searchContainer.addEventListener('keydown', closeSearchWithKey, false);
  }
})();

(function () {
  if ('loading' === document.readyState) {
    // The DOM has not yet been loaded.
    document.addEventListener('DOMContentLoaded', initGoTopButton);
  } else {
    // The DOM has already been loaded.
    initGoTopButton();
  } // Initiate the header search when the DOM loads.

  function initGoTopButton() {
    var buttonAction = function buttonAction() {
      try {
        window.scrollTo({
          top: document.getElementById('page').offsetTop,
          left: 0,
          behavior: 'smooth',
        });
      } catch (error) {
        window.scrollTo(0, document.getElementById('page').offsetTop);
      }
    };

    document
      .getElementById('gotop')
      .addEventListener('click', buttonAction, false);
  }
})();

('use strict');
/*
 Handles additional functionalities of the theme.
*/

(function () {
  var avdeezTheme = {
    // Run on ready.
    onReady: function onReady() {
      this.skipLinkFocusFix();
      this.setToggleSidebar();
    },
    // Set up a mobile menu.
    setToggleSidebar: function setToggleSidebar() {
      var toggleSection = document.getElementById('toggle-sidebar');

      if (!toggleSection) {
        return;
      }

      var toggleButton = document.getElementById('menu-button');

      if (!toggleButton) {
        return;
      }

      var closeBuutton = document.getElementById('close-toggle-sidebar');

      var bodyAction = function bodyAction(e) {
        if (
          !toggleSection.contains(e.target) &&
          e.target.nodeName !== 'BUTTON'
        ) {
          document.body.classList.toggle('active-toggle-sidebar');
          closeToggleSection();
        }
      };

      var closeToggleSection = function closeToggleSection() {
        // Update aria attributes.
        toggleSection.setAttribute('aria-hidden', true);
        toggleButton.setAttribute('aria-expanded', false); // Detect all clicks on the document

        document.body.removeEventListener('click', bodyAction, false);
        document.body.classList.add('hidden-toggle-sidebar');
      };

      var showToggleSection = function showToggleSection() {
        if (-1 !== document.body.className.indexOf('hidden-toggle-sidebar')) {
          document.body.classList.remove('hidden-toggle-sidebar');
        } // Update aria attributes.

        toggleSection.setAttribute('aria-hidden', false);
        toggleButton.setAttribute('aria-expanded', true); // Detect all clicks on the document

        document.body.addEventListener('click', bodyAction, false);
      };

      var buttonAction = function buttonAction() {
        document.body.classList.toggle('active-toggle-sidebar');

        if (-1 !== document.body.className.indexOf('active-toggle-sidebar')) {
          showToggleSection();
        } else {
          closeToggleSection();
        }
      };

      toggleButton.addEventListener('click', buttonAction, false);
      closeBuutton.addEventListener('click', buttonAction, false);
    },
    // Helps with accessibility for keyboard only users.
    skipLinkFocusFix: function skipLinkFocusFix() {
      var isIe = /(trident|msie)/i.test(navigator.userAgent);

      if (isIe && document.getElementById && window.addEventListener) {
        window.addEventListener(
          'hashchange',
          function () {
            var id = location.hash.substring(1),
              element;

            if (!/^[A-z0-9_-]+$/.test(id)) {
              return;
            }

            element = document.getElementById(id);

            if (element) {
              if (
                !/^(?:a|select|input|button|textarea)$/i.test(element.tagName)
              ) {
                element.tabIndex = -1;
              }

              element.focus();
            }
          },
          false
        );
      }
    },
  }; // Things that need to happen when the document is ready.

  document.addEventListener('DOMContentLoaded', function (event) {
    avdeezTheme.onReady();
  });
})();
