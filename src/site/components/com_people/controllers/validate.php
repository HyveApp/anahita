<?php 

class ComPeopleControllerValidate extends ComBaseControllerResource
{   
    /**
     * Validate username or email for uniqueness
     * @param  KCommandContext
     * @return application/json
     */
    protected function _actionPost(KCommandContext $context)
    {
    	if($context->data->email)
    	{
    		$result = $this->getService('com://site/people.controller.validator.person')
    						->validateEmail($context->data->email);
    	}
    	else if($context->data->username)
    	{
    		$result = $this->getService('com://site/people.controller.validator.person')
    						->validateUsername($context->data->username); 
    	}

    	if($result)
    	{
    		$response = array( "result" => array( "success" => 1) );
    	}
    	else
    	{
    		$response = array( "result" => array( "success" => 0,
    											  "error"	=> "already in use" ) );
    	}

    	$context->response->setContentType('application/json');
    	$context->response->setContent(json_encode( $response ));
    }
}