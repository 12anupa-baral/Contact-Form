<?php
/**
 * Plugin Name: Simple Contact Form
 * Description: Simple contact form plugin.
 * Author: Anupa Baral
 * Author URL: https://anupabaral.wordpress.com
 * Version: 1.0.0
 * Text Domain: simple-contact-form
 */

if (!defined('ABSPATH')) {
    // Stop execution if accessed directly.
    die("Why are you here?");
}

class SimpleContactForm {
    public function __construct() {
        //create custom post type
        add_action('init', array($this, 'create_custom_post_type'));

        //add assets (js,css)
        add_action('wp_enqueue_scripts',array($this,'load_assets'));//hook

        //add shortcodde
        add_shortcode( 'contact-form', array($this,'load_shortcode') );

        //load javascript
        add_action( 'wp_footer', array($this ,'load_script') );

        //register rest api
        add_action('rest_api_init', array($this,'register_rest_api'));
    }

    public function create_custom_post_type() {
        $args = array(
            'public'              => true,
            'has_archive'         => true,
            'supports'            => array('title'),
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'labels'              => array(
                'name'          => __('Contact Forms', 'simple-contact-form'),
                'singular_name' => __('Contact Form Entry', 'simple-contact-form'),
            ),
            'menu_icon'           => 'dashicons-media-text',
        );
        register_post_type('simple_contact_form', $args);
    }
    public function load_assets(){
        wp_enqueue_style(
            'simple-contact-form',
            plugin_dir_url(__FILE__ ). 'CSS/simple-contact-form.css',
            array(),
            1, 
            'all'
        );
    
        wp_enqueue_script( 
            'simple-contact-form',
            plugin_dir_url(__FILE__ ). 'js/simple-contact-form.js',
            array('jquery'), 
            1, 
            true 
        );

        wp_enqueue_script( 'wp-api' );
    }
public function load_shortcode()
    {?>
    <div class="simple-contact-form">
    
  
<form id="Admission-Form">
<h1>Application Form</h1>
<div class="form1">  
    <div>
        <div class="FormInput">
        <label for="fNo">Your IOE Form No.: </label>
    <input  id="fNo" name ="fNo" type = "number" placeholder="eg.2080-1" class="form-control" required/>
<div>
</div>
<label for="Sname">Student Name :</label>
    <input  id="Sname" name ="Sname" type = "text" placeholder=" Your Name" class="form-control" />
</div>
</div>

<div>
        <div class="FormInput">
        <label for="rank">IOE Rank : </label>
    <input id="rank" name="rank" type = "number" placeholder="eg.2080-1" class="form-control" required/>
<div>
</div>
<label for="gender" >Gender :</label>
    <select id="gender" name="gender" class="form-select">
        <option value="male" >Male</option>
        <option value="female">Female</option>
        <option value ="other" >Other</option>
      </select>
    
</div>
</div>
    <button type ="submit" class="btn btn-success btn-block">Send Message</button>

    </form>
   
    </div>  
    <!-- </div>   -->

   
<?php
    }


    public function load_script() {
        ?>
        <script>
           var nonce = '<?php echo wp_create_nonce('wp_rest');?>';

            (function($) {
                $(document).ready(function() {
                    $('#Admission-Form').submit(function(event) {
                        event.preventDefault();
                        var form = $(this).serialize();
                        console.log(form);

                       $.ajax({
                        method: 'POST',
                        url: '<?php echo get_rest_url(null ,'simple-contact-form/send-email');?>',
                        headers: {'X-WP-Nonce': nonce},
                        data: form
                       })
                    });
                });
            })(jQuery);
        </script>

        <?php
    }

    public function register_rest_api(){
        register_rest_route( 'simple-contact-form/v1', 'send-email', array(
            'methods' => 'get',
            'callback' => array($this,'handle_contact_form')

        ) );
    }

   

    public function handle_contact_form($data){
       echo "this is working";
    }
    
}


new SimpleContactForm();
?>
