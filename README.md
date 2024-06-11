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