<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Connect Login Contorller
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectControllerLogin extends ComBaseControllerResource
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);		       		
	}
		
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'behaviors' => array(
				'oauthorizable',
				'validatable'
			)
		));
	
		parent::_initialize($config);
	}
	
    /**
     * After getting the access token store the token in the session and redirect 
     *
     * @param KCommandContext $context Context parameter
     * @param void
     */
    protected function _actionGetaccesstoken($context)
    {
        parent::_actionGetaccesstoken($context);
                       
        $token   = $this->getAPI()->getToken();
        
        if ( empty($token) ) {
            $context->response->setRedirect(JRoute::_('index.php?'));
            return false;
        }
        
        $context->response->setRedirect(JRoute::_('option=com_connect&view=login'));
    }

    /**
     * Login via third party (facebook/twitter)
     *
     * @param KCommandContext $context Context parameter
     * @param void
	*/
	protected function _actionPost($context)
	{
		$context->response->setContentType('application/json');
		if( isset($_POST["oauth_handler"]) && isset($_POST["oauth_token"]) && isset($_POST["profile_id"]) && isset($_POST["oauth_secret"]))
		{
			/* link facebook/twitter accout with hyve accout if the request is passing owner_id, 
			   else login use facebook/twitter */
			if(isset($_POST["owner_id"])) {
				$session = $this->getService('repos://site/connect.session')
					->findOrAddNew(array(
		                    'owner.type'  => 'com:people.domain.entity.person',
		                    'profileId'   => $_POST["profile_id"],
		                    'api'         => $_POST["oauth_handler"]
						));

				$person = $this->getService('repos://site/people.person')
										->find(array('id'=>$_POST["owner_id"]));	

	            $session->setData(array(
	                'component' 	=> 'com_connect',
	                'owner' 		=> $person,
	                'tokenKey' 		=> $_POST["oauth_token"],
	                'tokenSecret'	=> $_POST["oauth_secret"]
	            ));                        
	            $session->save();  
				$context->response->setStatus(201);
				$context->response->setContent("Third Party Account Linked Successfully");
			}
			else {
		        $session = $this->getService('repos://site/connect.session')
		            ->find(array(
		                    'owner.type'  => 'com:people.domain.entity.person',
		                    'profileId'   => $_POST["profile_id"],
		                    'api'         => $_POST["oauth_handler"]
		                ));
	            if ( $session ) {
					$this->getService('com://site/people.controller.person', 
						        array('response'=>$context->response))
						         ->setItem($session->owner)->login();


					$session->set('tokenKey', $_POST["oauth_token"]);
					$session->set('tokenSecret', $_POST["oauth_secret"]);
					$session->save();

					$person = $this->getService('repos://site/people.person')
										->find(array('id'=>$session->owner->id))->toSerializableArray();
			    		
			    	$context->response->setStatus(201);
					$context->response->setContent(json_encode( $person ));

	            }
	            else {
	            	$context->response->setStatus(401, "User Doesn't Exist");
	            }
	        }
        }
        else {
        	$context->response->setStatus(400, "Missing Parameters");
        }
	}
    
	/**
	 * Redners the login form
	 * 
	 * @return void
	 */
	protected function _actionRead($context)
	{
	    if ( !$this->getAPI() ) 
	    {
	        $context->response->setRedirect(JRoute::_('option=com_people&view=person&get=settings&edit=connect'));
	        return false;	           
	    }
	    
		$service    = $this->getAPI()->getName();
		$userid     = $this->getAPI()->getUser()->id;		
		$token 	    =  $this->getService('repos://site/connect.session')->find(array('profileId'=>$userid,'api'=>$service));		
		$return_url = KRequest::get('session.return', 'raw');
		if ( $token ) 
		{
			$person = $token->owner;			
			KRequest::set('session.oauth', null);
			$this->getService('com://site/people.controller.person', 
			        array('response'=>$context->response))
			         ->setItem($person)->login();
			return false;
		}
		$this->return_url = base64_encode($return_url);		
	}
}
?>