<?php

class IndexController extends Zend_Controller_Action
{
    
    public function indexAction()
    {
        $this->view->headTitle('Send Tweet');
        $this->view->title = 'Send Tweet';
        $fc = $this->getFrontController();

        // Get the model instance from the action helper
        $twitter = $this->_helper->twitter(); /* @var $twitter Application_Model_Twitter */
        if ($twitter->isLoggedIn()) {
            // We only care abotu setting up the home page for posting a tweet
            // if we are logged in.
            
            $this->view->name = $twitter->getName();
            // Form to do tweeting with position
            $form = new Application_Form_Tweet();
            $form->setAction($this->view->url(array(), null, true));
            $this->view->form = $form;
            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->getRequest()->getPost())) {
                    $data = $form->getValues();
                    $tweet = $data['tweet'];                    
                    try {
                        $result = $twitter->send($tweet);
                        if ($result->isSuccess()) {
                            $message = 'Tweet sent';
                        } else {
                            $message = 'Failed to send tweet.';
                        }
                    } catch (Exception $e){
                        $message = 'Failed to send tweet. Reported error: ' . $e->getMessage();
                    }

                    $this->_helper->flashMessenger->addMessage($message);
                    $this->_helper->redirector->gotoRouteAndExit();
                }
            }
        }
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    public function logoutAction()
    {
        $session = new Zend_Session_Namespace();
        $session->unsetAll();
        $this->_helper->redirector('index', 'index');
    }

    public function loginAction()
    {
        $twitter = $this->_helper->twitter(); /* @var $twitter Application_Model_Twitter */

        // We need the request token for use in the callback when the user is
        // redirected back here from Twitter after authenticating
        $session = new Zend_Session_Namespace();
        $session->requestToken = $twitter->getRequestToken();

        // redirect to the Twitter website
        $twitter->loginViaTwitterSite();
    }
    
    public function lotteryAction()
    {
        $this->view->headTitle('Lottery');
        $this->view->title = 'Lottery';
        $fc = $this->getFrontController();
        
        // Get the model instance from the action helper
        $twitter = $this->_helper->twitter(); /* @var $twitter Application_Model_Twitter */
        if ($twitter->isLoggedIn()) {
            // We only care abotu setting up the home page for posting a tweet
            // if we are logged in.    
            $this->view->name = $twitter->getName();
        
        $prices = array("jablko", "okurka", "rajce", "zeli", "pomeranc");
        $tickets  = range(1,100);
        
        foreach ($prices as $p) {
            shuffle($tickets);
            $price = array_shift($tickets);                    
            $out[] = $p . " => " . $price . "<br />"; 
            $this->view->output = $out;

                    try {
                        $result = $twitter->send($p ." => " . $price);
                        if ($result->isSuccess()) {
                            $message = 'Tweet sent';
                        } else {
                            $message = 'Failed to send tweet.';
                        }
                    } catch (Exception $e){
                        $message = 'Failed to send tweet. Reported error: ' . $e->getMessage();
                    }

   //                 $this->_helper->flashMessenger->addMessage($message);
        }        
//                    $this->_helper->redirector->gotoRouteAndExit();            
        }
        
//        $this->view->messages = $this->_helper->flashMessenger->getMessages();        
    }
}
