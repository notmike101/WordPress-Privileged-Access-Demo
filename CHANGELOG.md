# Changelog

## Version 0.5 - 2025-04-25
- Documentation and Ethical Reframing
  - Updated plugin README with educational framing and full environment setup instructions.
  - Rewrote sh/ and plugins/ folder usage policies, added .gitignore rules, and replaced harmful defaults with stubs.
  - Reinforced disclaimers and usage boundaries for simulation-only behavior.
- Project cleanup
  - Streamlined codebase to focus on local educational testing and plugin behavior isolation.

## Version 0.4
- Major internal refactor for modularity and maintainability
- Introduced dynamic plugin system:
  - Allows plugin ZIPs to be added to the `./plugins/` directory and loaded automatically
  - Installs and activates additional plugins on activation of this plugin
  - Deactivates and removes plugins upon deactivation to simulate attacker cleanup
  - Periodic re-activation logic added to simulate persistence under traffic
- Added removal of persistence user on deactivation
- Added optional file viewing functionality via base64-encoded filenames in the URL (for local testing of arbitrary file read behaviors)

## Version 0.3
- Added support for dynamic shell loader via `./sh/` directory to simulate arbitrary script execution
- Improved demonstration of sensitive file exposure (e.g., `wp-config.php`) via plugin hooks

## Version 0.2
- Refactored to class-based architecture for easier modification, testing, and lower collision risk
- Enabled basic PHP shell interaction via direct access in plugin folder
- Added redirect-based "kill switch" logic for remote control simulation
- Added demonstration for file access using encoded filenames

## Version 0.1
- Initial proof-of-concept
  - Created hidden administrator account using WordPress API
  - Suppressed user visibility from dashboard and admin panels
  - Ensured plugin auto-activation on installation (for simulation purposes)
