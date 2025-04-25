# Wordpress Admin Persistence Plugin (Educational Purposes Only)

# Disclaimer

This plugin was created strictly for **educational and controlled-environment use**. It serves as a **security awareness tool** that demonstrates how WordPress plugins can be used to maintain persistent administrative access, hide users, and load arbitrary code without detection.

This is **not a malicious tool**, but it replicates behaviors seen in real-world backdoor plugins.  
Its purpose is to help developers, sysadmins, and security professionals understand potential plugin-based attack vectors and encourage tighter plugin auditing and defense practices.

**Misuse may violate laws and ethical guidelines.** The authors accept no responsibility for unauthorized or malicious use.

**Do not use this plugin on systems you do not own or have explicit permission to test.**

# Why This Exists

Many developers and site owners trust plugins without understanding the access they grant.  
This project was created to demonstrate how certain plugin structures can be misused to create hidden admin accounts, load arbitrary code, and maintain stealth access - **all without leaving obvious traces**.


This tool was built to:

- Demonstrate abuse potential in WordPress's plugin architecture
- Encourage thorough review of plugin code and permissions
- Educate developers and defenders on plugin-level persistence and stealth techniques

It is **not** intended for exploitation, but to raise awareness and support ethical research.

# What This Plugin Demonstrates

This plugin simulates common WordPress security threats by showcasing:

- Hidden creation of an administrator account
- Stealth plugin loading from a designated folder (`./plugins/`)
- Obfuscation of users and plugins in the admin dashboard
- A URL-based "kill switch" that simulates site-level redirect behavior
- Optional access to legacy web shells (e.g., c99, b374k) via configured keys (*for research purposes only*)
- A file access function using base64-encoded filenames for testing visibility of sensitive files like `wp-config.php`

These behaviors are intended for **red team simulation**, **educational labs**, or **plugin auditing research**—**never production use**.

# Intended Audience

This tool is intended for:

* Security researchers studying WordPress plugin-based persistence
* Developers learning to identify dangerous plugin patterns
* Educators teaching secure development or ethical hacking
* Red teamers simulating stealth plugin-based access in test environments

# Tested On

- WordPress 4.5 through 4.5.3

*Note: This plugin is not actively maintained. Contributions are welcome.*

# Installation (In local test environment only)

*Note:* **Do not install on production systems.**

1. Clone or download the repository
2. Modify the username, password, and access key in the plugin's `__construct()` method.
3. Upload the plugin to `/wp-content/plugins/wp-sph/`
4. Activate the plugin (titled `WordPress Importing Tool`) via the WordPress Admin plugins screen.
5. Log in using the credentials you configured

# Ethical Usage

If you use this plugin:

- Only do so in environments you own or have explicit permission to test
- Use it in educational demos, red team exercises, or code analysis settings
- Never deploy it on public or client-facing systems

If you're a defender or plugin reviewer, this tool can help highlight gaps in detection and offer insights into stealth plugin behavior.

# Frequently Asked Questions

**Q: What does the plugin do by default?**  
A: It creates a hidden administrator account with pre-defined credentials and an access key. These are defined in the plugin’s constructor and must be modified before any use. This simulates persistence techniques used in real-world malicious plugins.

**Q: Where is the backdoor user stored and how is it hidden?**
A: The user is created as a standard WordPress admin but filtered from the admin UI using WordPress hooks—similar to how real backdoors avoid detection.

**Q: How are additional plugins installed?**
A: Any plugin ZIP files placed in the `./plugins/` directory are automatically installed and activated when the backdoor is active. These plugins are hidden unless accessed by the backdoor user.

**Q: Are plugins and users visible to other admins?**  
A: The hidden user and loaded plugins are deleted to simulate attacker cleanup and reduce forensic evidence.

**Q: What happens when the plugin is deactivated?**  
A: Upon deactivation, the backdoor user and any dynamically installed plugins are removed. This is meant to simulate an attacker covering their tracks.

**Q: What is the purpose of the “kill switch” and shell access features?**
A: These are included to demonstrate how plugin-based threats can introduce remote control or destructive functionality.

**Q: How can defenders detect this plugin or similar ones?**  
A: Warning signs include:
* Users existing in the database but not shown in the admin UI
* Plugins present on disk but not visible in the plugin dashboard
* Suspicious access logs or hidden GET parameters
* Excessive or unexpected use of `add_filter()` or `remove_filter()` in plugin code

**Q: What are the default login credentials and access key?**  
A: Credentials must be manually configured in the plugin's `__construct()` method before use.

**Q: What does the `displayfile` function do?**  
A: It allows file content retrieval via base64-encoded filenames in the URL. This demonstrates how poorly secured plugins may expose sensitive data such as `wp-config.php`. It should only be tested in local or sandbox environments and never deployed on systems connected to the internet.

# Usage Restrictions

This plugin must **never** be deployed on production systems or environments you do not own or explicitly have permission to test. Use of this code in unauthorized or unethical scenarios may be illegal and is not supported by the authors.
