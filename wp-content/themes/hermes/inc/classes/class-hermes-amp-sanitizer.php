<?php
/**
 * Hermes AMP Sanitizer class
 */

/**
 * This class is in charge of adding AMP support for the site.
 *
 *
 * @since 1.0.0
 */
final class Hermes_AMP_Sanitizer extends AMP_Base_Sanitizer {
	/**
	 * XPath for the DOMDocument.
	 *
	 * @since 1.0.0
	 * @var DOMXPath
	 */
	protected $xpath;

	/**
	 * Sanitizes the HTML contained in the DOMDocument.
	 *
	 * @since 1.0.0
	 */
	public function sanitize() {
		$this->xpath = new DOMXPath( $this->dom );

        $this->support_toggle_sidebar();
		$this->support_header_search_form();
        $this->support_scroll_to_buttons();
	}

    /**
	 * Adds support for toggle sidebar (mobile menu), which is otherwise controlled by JS.
	 *
	 * @since 1.0.0
	 */
    protected function support_toggle_sidebar() {
        $state_id          = 'hermesToggleSidebar';
        $mobile_menu_id    = 'mobile-navigation';
        $toggle_sidebar_id = 'toggle-sidebar';
        $open_button_id    = 'menu-button';
        $close_button_id   = 'close-toggle-sidebar';
        $toggle_class      = 'active-toggle-sidebar';

        // Get the toggle sidebar contaniner.
        $toggle_sidebar = $this->dom->getElementById( $toggle_sidebar_id );
        if ( ! $toggle_sidebar ) {
			return;
		}

        // Get the open button for toggle sidebar.
        $open_button = $this->dom->getElementById( $open_button_id );
        if ( ! $open_button ) {
			return;
		}

        // Get the close button for toggle sidebar.
        $close_button = $this->dom->getElementById( $close_button_id );
        if ( ! $close_button ) {
			return;
		}

        // Get the mobile menu navigation.
        $mobile_menu = $this->dom->getElementById( $mobile_menu_id );
        if ( ! $mobile_menu ) {
			return;
		}

        // Get body element id.
        $body_element = $this->dom->getElementsByTagName( 'body' )->item( 0 );
        if ( ! $body_element ) {
			return;
		}

        if ( ! $body_element->getAttribute( 'id' ) ) {
            $body_element->setAttribute( 'id', 'body-1' );
        }
        $body_id = $body_element->getAttribute( 'id' );

        $open_button->setAttribute(
            'on',
            "tap:
                AMP.setState( { {$state_id}: { toggleSidebarExpanded: ! {$state_id}.toggleSidebarExpanded } } ),
                $body_id.toggleClass(class=$toggle_class)
            "
        );

        $open_button->setAttribute(
            'data-amp-bind-aria-expanded',
            "{$state_id}.toggleSidebarExpanded ? 'true' : 'false'"
        );

        $close_button->setAttribute(
            'on',
            "tap:
                AMP.setState( { {$state_id}: { toggleSidebarExpanded: ! {$state_id}.toggleSidebarExpanded } } ),
                $body_id.toggleClass(class=$toggle_class)
            "
        );

        $toggle_sidebar->setAttribute(
            'data-amp-bind-aria-hidden',
            "{$state_id}.toggleSidebarExpanded ? 'false' : 'true'"
        );

        // Header menu elements.
		$header_menus = $this->xpath->query( '//header[ @id = "masthead" ]//ul[ contains( @class, "menu" ) ]' );
		if ( ! $header_menus->length ) {
			return;
		}

        foreach ( $header_menus as $menu ) {
            $mobile_menu->appendChild( $menu->cloneNode( true ) );
        }
    }

    /**
	 * Adds support for Scroll To buttons, which is otherwise controlled by JS.
	 *
	 * @since 1.0.0
	 */
    protected function support_scroll_to_buttons() {
        $gototop_button_id              = 'gotop';
        $gototop_button_target_id       = 'page';
        $skiptocontent_button_id        = 'skip-to-entry';
        $skiptocontent_button_target_id = 'main';

        // Get the Go to top button.
        $gototop_button = $this->dom->getElementById( $gototop_button_id );
        if ( ! $gototop_button ) {
			return;
		}

        $gototop_button->setAttribute(
            'on',
            "tap:AMP.scrollTo(id='$gototop_button_target_id')"
        );

        // Get the Skip to content button.
        $skiptocontent_button = $this->dom->getElementById( $skiptocontent_button_id );
        if ( ! $skiptocontent_button ) {
			return;
		}

        $skiptocontent_button->setAttribute(
            'on',
            "tap:AMP.scrollTo(id='$skiptocontent_button_target_id')"
        );
    }

    /**
	 * Adds support for header search form, which is otherwise controlled by JS.
	 *
	 * @since 1.0.0
	 */
	protected function support_header_search_form() {
        $state_id              = 'hermesHeaderSearchForm';
        $header_search_form_id = 'search-container';
        $toggle_class          = 'th-flex';

        // Get the header search container.
        $header_search_container = $this->dom->getElementById( $header_search_form_id );
        if ( ! $header_search_container ) {
			return;
		}

        // Get the button that opens the header search form.
		$open_button = $this->xpath->query( '//header[@id = "masthead"]//button[ contains( @class, "header-search-button" ) ]' );
        if ( ! $open_button->length ) {
			return;
		}

        $open_button = $open_button->item( 0 );
        if ( ! $open_button instanceof DOMElement ) {
            return;
        }

        // Get the button that closes the header search form.
        $close_button = $this->dom->getElementById( 'close-search-container' );
		if ( ! $close_button ) {
			return;
		}

        /** @var \DOMElement $open_button */
		$open_button->setAttribute(
            'on',
            "tap:
                AMP.setState( { {$state_id}: { headerSearchFormExpanded: ! {$state_id}.headerSearchFormExpanded } } ),
                $header_search_form_id.toggleClass(class=$toggle_class)
            "
        );

        $open_button->setAttribute(
            'data-amp-bind-aria-expanded',
            "{$state_id}.headerSearchFormExpanded ? 'true' : 'false'"
        );

        $close_button->setAttribute(
            'on',
            "tap:
                AMP.setState( { {$state_id}: { headerSearchFormExpanded: ! {$state_id}.headerSearchFormExpanded } } ),
                $header_search_form_id.toggleClass(class=$toggle_class)
            "
        );

        $header_search_container->setAttribute(
            'data-amp-bind-aria-hidden',
            "{$state_id}.headerSearchFormExpanded ? 'false' : 'true'"
        );
	}
}
