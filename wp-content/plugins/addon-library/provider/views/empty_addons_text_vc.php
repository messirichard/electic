<?php 
// no direct access
defined('ADDON_LIBRARY_INC') or die;

$urlStarterPackPage = "http://addon-library.com/starter-pack-addons-for-visual-composer/";
$urlDocumentation = "http://addon-library.helpsite.io/";
$urlPremium = "http://addon-library.com";

?>

	<div class="uc-content-box">
    	<h2>Welcome To Addon Library for Visual Composer</h2>
        <p>You dont have any addons installed yet<br> we made a special starter pack for beginers</p>
        <a href="<?php echo $urlStarterPackPage?>" target="_blank">Download the Free Starter Pack</a>
        <p><span>click here to view <a href="<?php echo $urlDocumentation?>" target="_blank">documentation</a></span></p>
        <div class="border"><code class="white-bg">OR</code></div>
        <h3>Advanced Users</h3>
        <a href="<?php echo $urlPremium?>" class="green" target="_blank">Get Premium Addons Here</a>
    </div>
