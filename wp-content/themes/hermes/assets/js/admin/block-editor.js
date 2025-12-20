'use strict';

wp.domReady(function () {
  /**
   * Add table style: Borders
   */
  wp.blocks.registerBlockStyle('core/table', [
    {
      name: 'borders',
      label: wp.i18n.__('Borders', 'hermes'),
    },
  ]);
});
