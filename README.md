# avdi.codes README

## Logbook

Stuff changed outside this repo but is nonetheless worth noting down:

### 2024-06-11

Today in response to a massive hit after publishing fediverse updates, I changed the wp-config on roland.

- Moved `define( 'ACTIVITYPUB_SEND_VARY_HEADER', true );` higher in the config file because it was at the very end and I was seeing "constant redefined errors" for it
- Removed `define('FLUENTCRM_IS_DEV_FEATURES', true);` while I was in there

Removed the following lines from Cloudways Breeze Cache settings:

```
https://avdi.codes/wp-json/(.*)
https://avdi.codes/author/avdi/
```

In theory the `ACTIVITYPUB_SEND_VARY_HEADER` is enough to take care of the author page issue I'd had in the past, where it was replying with only HTML even in response to JSON requests.

I'm nervous about taking out wp-json, but I need to not get slammed every time the fediverse discovers I posted something new.

### 2024-06-15 (more or less)

After trying various caching plugins and talking to the ActivityPub maintainers, have replaced Breeze with WP Super Cache for now. This does not cache AP versions of pages. But it doesn't have the "whichever version gets cached first wins" problem of Breeze and W3 Total Cache.

### 2024-06-16

Added override CSS in the customizer so that HRs would actually show up:

```css
.wp-block-separator {
  border-top: 2px solid !important;
}
```

Should probably get this into a child theme, but we need a child theme first.