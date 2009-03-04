<?php

/**
 * Formats an address for profile
 * @param Profile_model $profile
 * @return string
 */
function formatProfileAddress($profile) 
{
    $completeAddress = 'Incomplete';
    $street = $profile->getStreet();
    $zipCode = $profile->getZip();
    $city = $profile->getCity();
    $country = $profile->getCountry();
    
    if(!empty($street) && !empty($zipCode) && !empty($city) && !empty($country)) {
        
        $countryCode = $profile->getCountryModel()->getCode();
        
        switch($countryCode) {
            
            case 'NL':
            case 'DE':
            case 'BE':
            case 'RU':
                $completeAddress = $street . '<br />' .
                    $zipCode . ' ' . $city . '<br />' .
                    $country;
            break;
            default:
            $completeAddress = $street . '<br />' . 
               $city . ', ' . $zipCode . '<br />' . 
               $country;
        }
        
    }
    
    return $completeAddress;
}