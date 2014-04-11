<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GlobalConstants
 *
 * * @author Haroon Abbasi
 */
App::uses('Component', 'Controller');
class GlobalConstantComponent extends Component {
    
    function initialize(Controller $controller) {
        $constants = array(
            'Website_Name' => 'Bookmarks',
            'Website_Url' => 'www.Bookmarks.com',
        );
        
        foreach ($constants as $key => $const) {
            if(!defined($key))
            define($key,$const);
        }        
    }

}

?>
