<?php
/**
 * Plugin Name: Sublime Skinz WP
 * Plugin URI: http://sublimeskinz.com
 * Description: Display Awesome Skinz advertising Campaigns on you Wordpress Blog or Website.
 * Version: 1.0
 * Author: Sublime Skinz
 * Author URI: http://sublimeskinz.com
 * License:  GPL2
 */

global $zid;

add_filter('template_include','custom_include',1);

function custom_include($template) {
        ob_start();
        return $template;
}

function sublime_install(){
    global $wpdb;
    $table = $wpdb->prefix.'sublime_table';

    $sql = "CREATE TABLE " . $table . " (
              id INT NOT NULL AUTO_INCREMENT,
              zid INT NOT NULL,
              UNIQUE KEY id (id)
              );";

    $wpdb->query($sql);

}
 
add_action('admin_menu', 'sublime_admin_menu');
function sublime_admin_menu() {
    $page_title = 'Sublime Settings';
    $menu_title = 'Sublime Skinz WP';
    $capability = 'manage_options';
    $menu_slug = 'sublime-settings';
    $function = 'sublime_settings';
    add_options_page($page_title, $menu_title, $capability, $menu_slug, $function);
}

function sublime_settings() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Here is where you could start displaying the HTML needed for the settings
    // page, or you could include a file that handles the HTML output for you.
    if(isset($_POST["zid"])){
        
        $postzid=mysql_real_escape_string($_POST["zid"]);
        global $wpdb;
        $table = $wpdb->prefix . 'sublime_table';
        
        $totalinfo = $wpdb->get_results( "SELECT zid FROM " . $table . " where id='1';",ARRAY_A);
        $zid=$totalinfo[0][zid];
        if($zid!=null){
            if($postzid!=""){
                 $sql = "INSERT INTO " . $table . " 
              (id,zid) values ('1','".$postzid."') on duplicate key update zid='".$postzid."'
              ;";
                 $wpdb->query($sql); 
            }
            else{
                 $wpdb->query("TRUNCATE TABLE ".$table." ");
   
            }
           
            
        }
        else{
            $sql = "INSERT INTO " . $table . " 
              (id,zid) values ('1','".$postzid."') on duplicate key update zid='".$postzid."'
              ;";
           
            $wpdb->query($sql); 
        }

            
        echo "<div style='border:1px solid orange;width:200px; padding:5px;margin-top:10px;'>Changes saved<br/></div>";
        
    }
    global $wpdb;
    $table = $wpdb->prefix . 'sublime_table';
    $totalinfo = $wpdb->get_results( "SELECT zid FROM " . $table . " where id='1';",ARRAY_A);
    $zid=$totalinfo[0][zid];
    
    ?>
<div style='background-image: url("../wp-content/plugins/sublime-skinz-wp/img/skinz-fond-s-wp2.png");background-position: bottom right;background-repeat: no-repeat; width:100%; height:550px;'>
    
    <div style="position:absolute;top:10; right:10px;"><img src='../wp-content/plugins/sublime-skinz-wp/img/skinz-logo-wp.png'></div>
<h2>Sublime Skinz WP Settings</h2>
<div style="width:50%">To display awesome Skinz advertising Campaigns on you Wordpress Blog or Website, please fill in the form with your Sublime Skinz ID!<br/><br/>
    
    You can get your ID on <a href='http://sublimeskinz.com' target="_blank">sublimeskinz.com</a>.
    <br/><br/>
    Zone ID:
    <form name="f" method="post">
        <input type="text" name="zid" value="<?php echo $zid; ?>"/>
        <input type="submit" name="envoyer" value="Save">
    </form>
</div>

</div>
<?php
}

add_filter('shutdown','body_inject',0);

function body_inject() {
    global $wpdb;
    $table = $wpdb->prefix . 'sublime_table';
    $totalinfo = $wpdb->get_results( "SELECT zid FROM " . $table . " where id='1';",ARRAY_A);
    $zid=$totalinfo[0][zid];
        $inject = '<script type="text/javascript" src="http://ads.ayads.co/ajs.php?zid='.$zid.'"></script>';
        $content = ob_get_clean();
        $content = preg_replace('#<body([^>]*)>#i',"<body$1>{$inject}",$content);
        echo $content;
}

add_action('init', 'sublime_init');

function sublime_init() {
   /*  if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }*/
        global $wpdb;
        $table = $wpdb->prefix . 'sublime_table';
        $totalinfo = $wpdb->get_results( "SELECT zid FROM ".$table.";",ARRAY_A);
        if($totalinfo!=null){
            $zid=$totalinfo[0][zid];
    //$sql = "SELECT * FROM ".$table."";
    //$r=mysql_query($sql);
    //if($row=  mysql_fetch_assoc($r)){
       // $zid=$row["zid"];
        
    }
    else{
        sublime_install();
    }

    
        
}


add_filter('plugin_action_links', 'sublime_plugin_action_links', 10, 2);

function sublime_plugin_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        // The "page" query string value must be equal to the slug
        // of the Settings admin page we defined earlier, which in
        // this case equals "myplugin-settings".
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=sublime-settings">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}

register_deactivation_hook(__FILE__, 'sublime_uninstall');

function sublime_uninstall(){
    

    // delete custom tables
    global $wpdb;
    $tablename = $wpdb->prefix . 'sublime_table';
    $wpdb->query("DROP TABLE ".$tablename."");
    
   
}

?>
