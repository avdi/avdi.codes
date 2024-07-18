# avdi.codes README

## WPFusion Notes

The `wpf_cookie` tends to mess with naive caching (e.g. Surge); and there is no on/off switch for setting the `wpf_ref` cookie. Instead, you have to disable the lead source fields (such as "original referrer") in the WP Fusion field mapping settings.

## Notes on ActivityPub

### Deletes

I accidentally converted a post back to draft and then deleted it, which resulted in the Delete never propogating out to the fediverse. After much trial and error, the solution turned out to be to create a new post and override its permalink to be that of the old post, at which point the fediverse treated it as the new source of that post's contents.

One important thing I learned along the way is that the Outbox is not used for Delete actions, apparently. Those are sent directly to followers on a cron schedule. An hourly schedule, specifically. So don't expect it to happen right away.

But before I figured this out I did a lot of research on adding stuff to the Outbox and on synthesizing Delete events. So for posterity, even though this **does not work**:

```php
// add_filter('activitypub_rest_outbox_array', 'avdicodes_activitypub_outbox_extras', 10, 1);
function avdicodes_activitypub_outbox_extras($json) {
    $delete_item = [
        "id" => "https://avdi.codes/#activity-delete-manual-17915-2-2024-06-20-05",
        "published" => "2024-06-20T15:28:45Z",
        "to" => [
                "https://www.w3.org/ns/activitystreams#Public",
                "https://avdi.codes/wp-json/activitypub/1.0/actors/1/followers"
            ],
        "cc" => [],
        "type" => "Delete",
        "object" => "https://avdi.codes/17915-2/",
        "actor" => "https://avdi.codes/author/avdi/",
        "summary" => "Delete post 17915-2"
    ];
    array_unshift($json->orderedItems, $delete_item);
    $remove_item = [
        "id" => "https://avdi.codes/#activity-remove-manual-17915-2-2024-06-20-05",
        "published" => "2024-06-20T15:28:45Z",
        "to" => [
                "https://www.w3.org/ns/activitystreams#Public",
                "https://avdi.codes/wp-json/activitypub/1.0/actors/1/followers"
            ],
        "cc" => [],
        "type" => "Remove",
        "object" => "https://avdi.codes/17915-2/",
        "actor" => "https://avdi.codes/author/avdi/",
        "target" => "https://avdi.codes/wp-json/activitypub/1.0/actors/1/outbox",
        "summary" => "Remove post 17915-2"
    ];
    array_unshift($json->orderedItems, $remove_item);
    return $json;
}
```

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

---

Added `resource` to Breeze cached query strings (https://avdi.codes/wp-admin/options-general.php?page=breeze) so that webfinger queries will be cached. Test with:

```
curl -v https://avdi.codes/.well-known/webfinger?resource=acct:avdi@avdi.codes
```

---

Update: having conferred with CloudWays support, and tested 3 different cache plugins (Breeze, WP Rocket, and W3 Total Cache) I have completely disabled caching for now. ALL of the above plugins seem to cause the caching layer(s) to completely ignore content-type and Vary headers, so they break ActivityPub.


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
