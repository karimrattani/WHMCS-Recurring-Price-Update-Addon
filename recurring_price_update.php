<?php
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function recurring_price_update_config()
{
    return [
        // Display name for your module
        'name' => 'Recurring Price Update',
        // Description displayed within the admin interface
        'description' => 'Updates recurring price for all products using WHMCS Auto Recalculate feature',
           
        // Module author name
        'author' => 'Karim Rattani',
        // Default language
        'language' => 'english',
        // Version number
        'version' => '1.0'
    ];
}

function recurring_price_update_output($vars)
{
    // Get common module parameters
    $modulelink = $vars['modulelink']; // eg. addonmodules.php?module=addonmodule
    $version = $vars['version']; // eg. 1.0
    $_lang = $vars['_lang']; // an array of the currently loaded language variables
    echo 'Updates recurring price for all products using WHMCS Auto Recalculate feature';
    echo '<br/><br/>';


      if (isset($_POST['action']) == 'submit') {
        $currencyId = $_POST['currencyId'];
        $res = process_recurr_price_request($currencyId);
        echo '<b>'.$res.' for users with '.$_POST['currencyCode'].' currency</b><br/><br/><br/>';
      }

    
    foreach(Capsule::table('tblcurrencies')->get() as $currencies){
        echo '<form action="'.$modulelink.'" method="POST" >';
        echo '<input type="hidden" name="currencyId" value="'.$currencies->id.'"/>';
        echo '<input type="hidden" name="currencyCode" value="'.$currencies->code.'"/>';
        echo '<input type="hidden" name="action" value="submit"/>';
        echo '<input type="submit" name="CurrId" value="Update for users with '.$currencies->code.' currency"/>';
        echo '</form><br/>';
     }


   }

function process_recurr_price_request($currId)
{
    $adminUsername = '';
    foreach(Capsule::table('tblclients')->where('currency', '=', $currId)->pluck('id') as $userid){
       foreach (Capsule::table('tblhosting')->where('userid', '=', $userid)->pluck('id') as $serviceId) {
         localAPI('UpdateClientProduct', array('serviceid' => $serviceId, 'autorecalc' => true), $adminUsername);
        }
        foreach (Capsule::table('tblhostingaddons')->where('userid', '=', $userid)->pluck('id') as $serviceAddonId) {
         localAPI('UpdateClientAddon', array('id' => $serviceAddonId, 'autorecalc' => true), $adminUsername);
        }
       foreach (Capsule::table('tbldomains')->where('userid', '=', $userid)->pluck('id') as $domainId) {
         localAPI('UpdateClientDomain', array('domainid' => $domainId, 'autorecalc' => true), $adminUsername);
        }        
     }
   
    return 'Update Completed';
    
}



?>