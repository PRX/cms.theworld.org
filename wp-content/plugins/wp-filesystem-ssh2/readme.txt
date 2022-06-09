=== WP Filesystem SSH2 ===
Contributors: Kerfred
Plugin Uri: https://www.fredericgilles.net/wp-filesystem-ssh2/
Tags: ssh, ssh2, sftp, filesystem, phpseclib
Requires at least: 4.9
Tested up to: 5.8.2
Stable tag: 1.0.2
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=fred%2egilles%40free%2efr&lc=FR&item_name=wp-filesystem-ssh2&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted

This plugin provides the class WP_Filesystem_SSH2 to be able to make SFTP connections.

== Description ==

The class WP_Filesystem_SSH2 already exists in the WordPress core, but it is not loaded by default in the backend. Moreover, it requires to compile libssh2 in PHP.
This class does the same thing with the help of the phpseclib library. So there is no need to compile libssh2 in PHP.
This class has got no interface, it can just be used by other plugins to make SFTP connections like this:
`$this->ftp = new WP_Filesystem_SSH2($options);`
where
`$options = array('hostname' => 'ftp.mydomain.com',
                  'port' => 22,
                  'username' => 'login',
                  'password' => '********'
                 );
`
The class is documented on https://developer.wordpress.org/reference/classes/wp_filesystem_ssh2/

Note that if the core WP_Filesystem_SSH2 class is already loaded, this plugin will do nothing.
 
== Installation ==

1.  Extract the plugin zip file and load up to your wp-content/plugins directory
2.  Activate the plugin in the Plugins menu

== Changelog ==

= 1.0.2 =
Fixed: Parse error: syntax error, unexpected 'new' (T_NEW) in /usr/local/pear/share/pear/Crypt/RSA.php on line 232

= 1.0.1 =
Fixed: Notice: Trying to access array offset on value of type bool

= 1.0.0 =
Initial version
