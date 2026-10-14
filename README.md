# Zero_Reviews

More from Magento's product reviews.
- Photos with a review (up to 5): re-encoded on upload (no EXIF, no GPS, no
  files posing as images), shown under the review once it is approved.
- "Verified purchase": the review's author is a customer who ordered the
  product.
- Google's product reviews feed ({store URL}/reviewfeed/google) for star
  ratings in Shopping ads.
Stores > Configuration > Zero > Reviews.

## Install

Requires Magento Open Source 2.4.7–2.4.9 and PHP 8.2–8.4.

```bash
composer require fixstacks/module-zero-reviews
bin/magento setup:upgrade
bin/magento setup:di:compile           # production mode
bin/magento setup:static-content:deploy # production mode
bin/magento cache:flush
```

Composer installs `fixstacks/module-zero-core` with it.

Part of [`fixstacks/zero`](https://fixstacks.com/zero), which installs the Zero theme and every Zero module at once.

## License

OSL-3.0 or AFL-3.0, your choice: see `NOTICE.md`.

---

Zero is made by [Fixstacks](https://fixstacks.com/zero) in partnership with [Vilmate](https://vilmate.com).
Documentation: https://fixstacks.com/zero/docs. Demo store: https://zero.fixstacks.com.
