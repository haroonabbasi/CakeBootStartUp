<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    
    public $components = array('DebugKit.Toolbar','Session','RequestHandler','GlobalConstant');
	
    public function afterFilter(){
        parent::afterFilter();
 
        // sql logging to chrome console
        if (class_exists('ConnectionManager') && Configure::read('debug') >= 2) {
            
            $sources = ConnectionManager::sourceList();
            
            $logs = array();
            foreach ($sources as $source){
                $db = ConnectionManager::getDataSource($source);
                $logs[$source] = $db->getLog();
            }
            
            foreach ($logs as $source => $logInfo){
                
                $text = $logInfo['count'] > 1 ? 'queries' : 'query';
                ChromePhp::info('------- SQL: '.sprintf('(%s) %s %s took %s ms', $source, count($logInfo['log']), $text, $logInfo['time']).' -------');
                ChromePhp::info('------- REQUEST: '.$this->request->params['controller'].'/'.$this->request->params['action'].' -------');
                
                foreach ($logInfo['log'] as $k => $i){
                    
                    $i += array('error' => '');
                    if (!empty($i['params']) && is_array($i['params'])) {
                        $bindParam = $bindType = null;
                        if (preg_match('/.+ :.+/', $i['query'])) {
                            $bindType = true;
                        }
                        foreach ($i['params'] as $bindKey => $bindVal) {
                            if ($bindType === true) {
                                $bindParam .= h($bindKey) ." => " . h($bindVal) . ", ";
                            } else {
                                $bindParam .= h($bindVal) . ", ";
                            }
                        }
                        $i['query'] .= " , params[ " . rtrim($bindParam, ', ') . " ]";
                    }
                    
                    $error = !empty($i['error']) ? "\nError: ".$i['error']:"\n";
                    $logStr = $i['query'].$error."\nAffected: ".$i['affected']."\nNum. Rows: ".$i['numRows']."\nTook(ms): ".$i['took']."\n\n";
                    
                    if(!empty($i['error'])){
                        ChromePhp::error($logStr);
                    }
                    else if($i['took'] >= 100){
                        ChromePhp::warn($logStr);
                    }
                    else{
                        ChromePhp::info($logStr);
                    }
                }
            }
        }
    }	
	
    
}
