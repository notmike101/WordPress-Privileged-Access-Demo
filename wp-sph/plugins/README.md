# Plugin Drop-ins (Simulation Only)

This folder is used to simulate how malicious plugins may auto-load and manage additional WordPress plugins after activation.

## Important

No real plugin ZIP files are included in this repository.  
You may add **safe, open-source plugins** here for testing purposes - such as "Hello Dolly" or other known demo plugins.

## What Happens Here?

Plugins placed in this directory will:

- Be automatically installed and activated when this educational plugin is enabled
- Be hidden from the WordPress admin UI if stealth mode is turned on
- Be removed during deactivation to simulate attacker cleanup behavior

This is intended purely for educational simulation.

**Do not include unauthorized, obfuscated, or third-party ZIPs in this folder.**

## Example Test Flow

1. Add a known-safe plugin ZIP (e.g., `hello-dolly.zip`) to this folder
2. Activate the main plugin
3. Observe that the plugin is installed and may be hidden
4. Deactivate the main plugin to remove the plugin drop-in
