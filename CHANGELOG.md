# Changelog

## 1.0.0

- Initial release
- SMS notifications via the [TextBee](https://textbee.dev) API
- Per-message device ID override
- Multi-recipient support via `->to(string|array)`
- Multi-SIM device support via `->simSubscriptionId(int)`
- `debug_to` config to redirect all messages in development
- `enabled` config toggle
- Self-hosted instance support via `base_url` config
- Laravel 12 and 13 support, PHP 8.3+
