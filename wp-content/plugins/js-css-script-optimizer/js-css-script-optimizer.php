<?php
/*
  Plugin Name: JS & CSS Script Optimizer
  Plugin URI: http://4coder.info/en/code/wordpress-plugins/js-css-script-optimizer/
  Version: 0.2.5
  Author: Evgeniy Kotelnitskiy
  Author URI: http://4coder.info/en/
  Description: Features: Combine all scripts into the single file, Pack scripts using <a href="http://joliclic.free.fr/php/javascript-packer/en/">PHP version of the Dean Edwards's JavaScript Packer</a>, Move all JavaScripts to the bottom, Combine all CSS scripts into the single file, Pack CSS files (remove comments, tabs, spaces, newlines).
*/
class evScriptOptimizer {

    static $upload_path = '';
    static $upload_url = '';
    static $plugin_path = '';
    static $cache_directory = '';
    static $cache_url = '';
    static $options = null;
    static $js_printed = false;
	static $css_printed = false;
	
	static $ordering_started = false;

    /**
     * init
     */
    function init() {
        $is_logged_in = is_user_logged_in();

        // init some constants
        $uploads = wp_upload_dir();        
        self::$upload_path = $uploads['basedir'] . '/';
        self::$upload_url = $uploads['baseurl'] . '/';       
        
		if (substr(self::$upload_path, -1) != '/') self::$upload_path .= '/';
		if (substr(self::$upload_url, -1) != '/') self::$upload_url .= '/';
		
        self::$plugin_path = dirname(__FILE__);
        self::$cache_directory = self::$upload_path . 'spacker-cache/';
        self::$cache_url = self::$upload_url . 'spacker-cache/';

        // load plugin localizations
        load_plugin_textdomain('spacker', self::$plugin_path . '/lang', self::$plugin_path . '/lang');

        // load options
        self::$options = get_option('spacker-options');
		//self::$options = false;//***
        if (! is_array(self::$options)) {
            self::$options = array(
                'only-selfhosted-js'    => true,
                'combine-js'            => 'combine',
                'packing-js'            => true,
				'css'                   => true,
                'only-selfhosted-css'   => true,
                'combine-css'           => true,
                'packing-css'           => true,
                'inc-js'                => null,
                'inc-css'               => null,
                'exclude-js'            => null,
                'exclude-css'           => null,
                'cache'                 => array(),
                'cache-css'             => array(),
				'strict-ordering-beta'  => false,
			);
        }
		
		if (! isset(self::$options['strict-ordering-beta'])) {
			self::$options['enable-plugin'] = true;
			self::$options['strict-ordering-beta'] = false;
		}
		if (! isset(self::$options['combine-css']) AND !empty(self::$options['combining-css'])) {
			self::$options['combine-css'] = true;
		}
				
        // add actions and hooks
		if (! is_admin()) {
			if (self::is_on()) {
				add_action('wp_print_scripts',     array(__CLASS__, 'wp_print_scripts'), 200);
				if (self::$options['css']) {
					add_action('wp_print_styles',  array(__CLASS__, 'wp_print_styles'), 200);
				}
				add_action('wp_footer',            array(__CLASS__, 'footer'), 20000000);
				add_action('wp_head',              array(__CLASS__, 'head'), 20000000);

				// Include added scripts
				if (is_array(self::$options['inc-js'])) {
					foreach (self::$options['inc-js'] as $key => $js){
						if ($js['url']) {
							wp_deregister_script($key);
							wp_register_script($key, $js['url'], false);
						}
						wp_enqueue_script($key);
					}
				}
				
				if (is_array(self::$options['inc-css'])) {
					foreach (self::$options['inc-css'] as $key => $css){
						if (!$css['loggedIn'] || $is_logged_in)
							wp_enqueue_style($key, $css['url'], false, false, $css['media']);
					}
				}
				
				if (self::$options['strict-ordering-beta']) {
					self::ordering_start();
				}
			}
        }
        else {
            require_once('backend.php');
            evScriptOptimizerBackend::init();
        }
    }
	
	function is_on() {
        if (! is_writable(self::$cache_directory)) {
			echo 'Cache directory is not writable: ' . self::$cache_directory;
            return false;
        }        
		if (! self::$options['enable-plugin']) {
            return false;
        }
        return true;
	}

    /**
     * Check exclude list
     */
    function exclude_this_js($handle, $src) {
        static $exclude_js = false;
        if ($exclude_js === false) {
            $exclude_js = explode(',', self::$options['exclude-js']);
            foreach ($exclude_js as $_k => $_v) {
                $exclude_js[$_k] = trim($_v);
                if (! $exclude_js[$_k])
                    unset($exclude_js[$_k]);
            }
        }
        return (in_array($handle, $exclude_js) || in_array(basename($src), $exclude_js));
    }

    /**
     * Check exclude list for css
     */
    function exclude_this_css($handle, $src) {
        static $exclude_css = false;
        if ($exclude_css === false) {
            $exclude_css = explode(',', self::$options['exclude-css']);
            foreach ($exclude_css as $_k => $_css) {
                $exclude_css[$_k] = trim($_css);
                if (! $exclude_css[$_k]) unset($exclude_css[$_k]);
            }
        }
        return (in_array($handle, $exclude_css) || in_array(basename($src), $exclude_css));
    }

    /**
     * wp_print_scripts action
     *
     * @global $wp_scripts, $auto_compress_scripts
     */
    function wp_print_scripts() {
        if (is_admin()) return;

        global $wp_scripts, $auto_compress_scripts;
		if (! is_a($wp_scripts, 'WP_Scripts')) return;
		
        if (! is_array($auto_compress_scripts))
            $auto_compress_scripts = array();

        $queue = $wp_scripts->queue;
        $wp_scripts->all_deps($queue);
        $to_do = $wp_scripts->to_do;

        foreach ($to_do as $key => $handle) {
            $src = $wp_scripts->registered[$handle]->src;

            // Check exclude list
            if (self::exclude_this_js($handle, $src))
                continue;

            // Check host
            if (substr($src, 0, 4) != 'http') {
                $src = site_url($src);
                $external = false;
            }
            else {
                $home = get_option('home');		
                if (substr($src, 0, strlen($home)) == $home) {
                    $external = false;
                }
                else $external = true;
            }
			
            if (! self::$options['only-selfhosted-js'] || ! $external) {
                unset($wp_scripts->to_do[$key]);
                $auto_compress_scripts[$handle] = array(
													'src' => $src, 
													'external' => $external,
													'ver' => $wp_scripts->registered[$handle]->ver,
													'args' => $wp_scripts->registered[$handle]->args,
													'extra' => $wp_scripts->registered[$handle]->extra,
												);
            }
        }
        foreach ($wp_scripts->queue as $key => $handle) {
            if (isset($auto_compress_scripts[$handle]))
                unset($wp_scripts->queue[$key]);
        }

        // printing scripts hear or move to the bottom
        if (! self::$options['combine-js'] || self::$js_printed) {
            self::print_compressed_scripts();
        }       
    }

    /**
     * wp_print_styles action
     *
     * @global $wp_styles, $auto_compress_styles
     */
    function wp_print_styles() {
        if (is_admin()) return;
		
        global $wp_styles, $auto_compress_styles;
		if (! is_object($wp_styles)) return;
			
        if (! is_array($auto_compress_styles))
            $auto_compress_styles = array();
		
        $queue = $wp_styles->queue;
        $wp_styles->all_deps($queue);
        $to_do = $wp_styles->to_do;
        $queue_unset = array();
		
        foreach ($to_do as $key => $handle) {
            $src = $wp_styles->registered[$handle]->src;

            // Check exclude list
            if (self::exclude_this_css($handle, $src))
                continue;

            $media = ($wp_styles->registered[$handle]->args ? $wp_styles->registered[$handle]->args : 'screen');

            if (substr($src, 0, 4) != 'http') {
                $src = site_url($src);
                $external = false;
            }
            else {
                $home = get_option('home');
                if (substr($src, 0, strlen($home)) == $home) {
                    $external = false;
                }
                else $external = true;
            }

            if (! self::$options['only-selfhosted-css'] || ! $external) {
                unset($wp_styles->to_do[$key]);
                
                $auto_compress_styles[$media][$handle] = array(
															'src' => $src, 
															'external' => $external,
															'ver' => $wp_styles->registered[$handle]->ver,
															'args' => $wp_styles->registered[$handle]->args,
															'extra' => $wp_styles->registered[$handle]->extra,
														);
                $queue_unset[$handle] = true;
            }
        }

        foreach ($wp_styles->queue as $key => $handle) {
            if (isset($queue_unset[$handle]))
                unset($wp_styles->queue[$key]);
        }

		// printing CSS
		if (self::$css_printed || !self::$options['combine-css']) {
			self::wp_head_print_styles();
		}
    }
	
	function wp_head_print_styles() {
		global $auto_compress_styles;
        foreach ($auto_compress_styles as $media => $scripts) {
            self::print_compressed_styles($media);
        }
		self::$css_printed = true;
	}

    function print_compressed_scripts() {
        global $auto_compress_scripts;
        if (! is_array($auto_compress_scripts) || ! count($auto_compress_scripts))
            return;

        $home = get_option('siteurl').'/';
        if (! is_array(self::$options['cache']))
            self::$options['cache'] = array();

        if (self::$options['combine-js']) {
            $handles = array_keys($auto_compress_scripts);
            $handles = implode(', ', $handles);
			
            // Calc "modified tag"
            $fileId = 0;
            foreach ($auto_compress_scripts as $handle => $script) {
                if (! $script['external']) {
                    $path = self::get_path_by_url($script['src'], $home);
                    $fileId += @filemtime($path);
                }
				else {
					$fileId += $script['ver'].$script['src'];
				}
            }			
			
            $cache_name = md5(md5($handles).$fileId);
            $cache_file_path = self::$cache_directory . $cache_name . '.js';
            $cache_file_url = self::$cache_url . $cache_name . '.js';
			
			//if (isset($_GET['debug'])) print_r(self::$options['cache']);

            //echo "$fileId<br>".self::$options['cache'][$cache_name]."<br>$cache_file_path<br>$cache_file_url<br>".is_readable($cache_file_path);
            // Find a cache
            if (!empty(self::$options['cache'][$cache_name]) && is_readable($cache_file_path)) {
                // Include script: ?>
                <script type="text/javascript" src="<?php echo $cache_file_url; ?>">/*Cache!*/</script>
                <?php
                $auto_compress_scripts = array();
                return;
            }

            // Build cache
            $scripts = '';
            foreach ($auto_compress_scripts as $handle => $script) {
				$src = html_entity_decode($script['src']);
                $scripts .= "/* $handle: ($src) */\n";
                $contents = @file_get_contents(self::add_url_param($src, 'v', rand(1, 9999999)));
				
                if (self::$options['packing-js']) {
                    require_once self::$plugin_path . '/JavaScriptPacker.php';
                    $packer = new JavaScriptPacker($contents);
                    $contents = $packer->pack();
                }
                $scripts .= $contents . "\n";
            }
            $comment = "/*\nCache: ".$handles."\n*/\n";

            // Save cache
            self::save_script($cache_file_path, $comment . $scripts);
            self::$options['cache'][$cache_name] = $fileId;
            update_option('spacker-options', self::$options);

            // Include script: ?>
            <script type="text/javascript" src="<?php echo $cache_file_url; ?>"></script>
            <?php
            $auto_compress_scripts = array();
            //--------------------------------------------------------------------------------
        }
        else {
            foreach ($auto_compress_scripts as $handle => $script) {
				$src = html_entity_decode($script['src']);
                $fileId = 0;
                if (! $script['external']) {
                    $path = self::get_path_by_url($script['src'], $home);
                    $fileId = @filemtime($path);
                }
				else {
					$fileId += $script['ver'].$script['src'];
				}
                $cache_name = md5(md5($handle).$fileId);
                $cache_file_path = self::$cache_directory . $cache_name . '.js';
                $cache_file_url = self::$cache_url . $cache_name . '.js';

                // Find a cache
                if (!empty(self::$options['cache'][$cache_name]) && is_readable($cache_file_path)) {
                    // Include script: ?>
                    <script type="text/javascript" src="<?php echo $cache_file_url; ?>">/*Cache!*/</script>
                    <?php
                    continue;
                }

                //echo "$src ($fileId)<br>";
                $content = @file_get_contents(self::add_url_param($src, 'v', rand(1, 9999999)));
                if (self::$options['packing-js']) {
                    require_once self::$plugin_path . '/JavaScriptPacker.php';
                    $packer = new JavaScriptPacker($content);
                    $content = $packer->pack();
                }

                // Save cache
                $comment = "/* $handle: ($src) */\n";
                self::save_script($cache_file_path, $comment . $content);
                self::$options['cache'][$cache_name] = $fileId;
                ?>
                <script type="text/javascript" src="<?php echo $cache_file_url; ?>"></script>
                <?php
            }
            update_option('spacker-options', self::$options);
            $auto_compress_scripts = array();
        }
    }
		
    function compress_css($css, $path) {
        // remove comments, tabs, spaces, newlines, etc.
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', ' ', $css);
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), ' ', $css);
		$css = str_replace(
             array(';}', ' {', '} ', ': ', ' !', ', ', ' >', '> '),
             array('}',  '{',  '}',  ':',  '!',  ',',  '>',  '>'), $css);
		
        // url
        $dir = dirname($path).'/';
		/*if (is_user_logged_in()) {
			echo "$dir <br/>\n";
		}*/
	    $css = preg_replace('|url\(\'?"?([a-zA-Z0-9=\?\&\-_\s\./]*)\'?"?\)|', "url(\"$dir$1\")", $css);

        return $css;
    }
	
	function get_path_by_url($url, $home) {
		$path = ABSPATH . str_replace($home, '', $url);
		$_p = strpos($path, '?');
		if ($_p !== false) {
			$path = substr($path, 0, $_p);
		}
		return $path;
	}
	
	
    /*
     * Print CSS
     */
    function print_compressed_styles($media = 'screen') {
        global $auto_compress_styles;
        if (! is_array($auto_compress_styles[$media]) || ! count($auto_compress_styles[$media]))
            return;

        $home = get_option('siteurl').'/';
        if (! is_array(self::$options['cache-css']))
            self::$options['cache-css'] = array();

        if (self::$options['combine-css']) {
            $handles = array_keys($auto_compress_styles[$media]);
			$handles = implode(', ', $handles);
			
            // Calc "modified tag"
            $fileId = 0;
            foreach ($auto_compress_styles[$media] as $handle => $script) {
                if (! $script['external']) {
                    $path = self::get_path_by_url($script['src'], $home);
                    $fileId += @filemtime($path);
                }
				else {
					$fileId += $script['ver'].$script['src'];
				}
            }
			
            $cache_name = md5(md5($handles).$fileId);
            $cache_file_path = self::$cache_directory . $cache_name . '.css';
            $cache_file_url = self::$cache_url . $cache_name . '.css';
            // echo "$fileId<br>".self::$options['cache-css'][$cache_name]."<br>$cache_file_path<br>$cache_file_url<br>".is_readable($cache_file_path);
			
            // Find a cache
            if (!empty(self::$options['cache-css'][$cache_name]) && is_readable($cache_file_path)) {
                // Include script: ?>
                <link rel="stylesheet" href="<?php echo $cache_file_url; ?>" type="text/css" media="<?php echo $media; ?>" /><!-- Cache! -->
                <?php
                $auto_compress_styles[$media] = array();
                return;
            }

            // Build cache
            $scripts = '';
            foreach ($auto_compress_styles[$media] as $handle => $script) {
                $src = html_entity_decode($script['src']);
                $scripts .= "/* $handle: ($src) */\n";
                $content = @file_get_contents(self::add_url_param($src, 'v', rand(1, 9999999)));

                if (self::$options['packing-css']) {
                    $content = self::compress_css($content, $src);
                }
                $scripts .= $content . "\n";
            }
            $comment = "/*\nCache: ".$handles."\n*/\n";

            // Save cache
            self::save_script($cache_file_path, $comment . $scripts);
            self::$options['cache-css'][$cache_name] = $fileId;
            update_option('spacker-options', self::$options);

            // Include script: ?>
            <link rel="stylesheet" href="<?php echo $cache_file_url; ?>" type="text/css" media="<?php echo $media; ?>" />
            <?php
            $auto_compress_styles[$media] = array();
            //--------------------------------------------------------------------------------
        }
        else {
            foreach ($auto_compress_styles[$media] as $handle => $script) {
                $src = html_entity_decode($script['src']);
                $fileId = 0;
                if (! $script['external']) {
                    $path = self::get_path_by_url($script['src'], $home);
                    $fileId = @filemtime($path);
                }
				else {
					$fileId += $script['ver'].$script['src'];
				}
                $cache_name = md5(md5($handle).$fileId);
                $cache_file_path = self::$cache_directory . $cache_name . '.css';
                $cache_file_url = self::$cache_url . $cache_name . '.css';

                // Find a cache
                if (!empty(self::$options['cache-css'][$cache_name]) && is_readable($cache_file_path)) {
                    // Include script: ?>
                    <link rel="stylesheet" href="<?php echo $cache_file_url; ?>" type="text/css" media="<?php echo $media; ?>" /><!-- Cache! -->
                    <?php
                    continue;
                }

                //echo "$src ($fileId)<br>";
                $content = @file_get_contents(self::add_url_param($src, 'v', rand(1, 9999999)));
                if (self::$options['packing-css']) {
                    $content = self::compress_css($content, $src);
                }

                // Save cache
                $comment = "/* $handle: ($src) */\n";
                self::save_script($cache_file_path, $comment . $content);
                self::$options['cache-css'][$cache_name] = $fileId;
                ?>
                <link rel="stylesheet" href="<?php echo $cache_file_url; ?>" type="text/css" media="<?php echo $media; ?>" />
                <?php
            }
            update_option('spacker-options', self::$options);
            $auto_compress_styles[$media] = array();
        }
    }
	
    function add_url_param($url, $name, $val) {
        if (strpos($url, '?') === false)
			return $url."?$name=$val";
			
		return $url."&$name=$val";
    }

    function save_script($filename, $content) {
        if (is_writable(self::$upload_path)) {
            $fhandle = @fopen($filename, 'w+');
            if ($fhandle) fwrite($fhandle, $content, strlen($content));
        }
        return false;
    }

    function head() {
        if (self::$options['combine-js'] == 'combine') {
            self::print_compressed_scripts();
        }
		if (self::$options['combine-css']) {
			self::wp_head_print_styles();
		}
		
		if (self::$options['strict-ordering-beta']) {
            self::ordering_stop();
        }
    }

    function footer() {
        if (self::$options['combine-js']) {
            self::$js_printed = true;
            self::print_compressed_scripts();
        }
    }
	
    function ordering_start() {
		self::$ordering_started = true;
		//echo "<br/>\nordering_start(+)\n<br/>";
		ob_start();		
	}	
	
    function ordering_stop() {
		if (! self::$ordering_started) return;
		self::$ordering_started = false;		
		$html = ob_get_contents();
		ob_end_clean();

		$html = self::order_scripts($html);
		echo $html;
		//echo "<br/>\nordering_stop(-)\n<br/>";
	}
	
	function order_scripts($html){
		$count = preg_match_all('/<!--\[if[^<]*<script.*>.*<\/script>.*if\]-->/imxsU', $html, $matches);
		$if_scripts = '';
		if ($count) {
			foreach ($matches[0] as $_script) {
				$if_scripts .= $_script."\n";
				$html = str_replace($_script, '', $html);
			}
		}
		
		$count = preg_match_all('/<script.*>(.*)<\/script>/imxsU', $html, $matches);	
		$scripts = '';
		if ($count) {
			for ($i = 0; $i < $count; $i++) {
				$script = $matches[0][$i];
				$content = trim($matches[1][$i]);
				if (empty($content) || $content == '/*Cache!*/') 
					continue; 
				
				$scripts .= $script."\n";
				$html = str_replace($script, '', $html);
			}
		}
		
		$html .= "\n".$scripts."\n";
		$html = str_replace('</title>', "</title>\n".$if_scripts, $html);
		/*
		$res = array(
					'html' => $html,
					'scripts' => $scripts,
					'if_scripts' => $if_scripts,
				);
				
		//print_r($res); exit;
		*/
		return $html;
	}
}

//evScriptOptimizer::init();
add_action('init', array('evScriptOptimizer', 'init'));