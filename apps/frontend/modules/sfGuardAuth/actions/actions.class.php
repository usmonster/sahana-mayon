<?php

require_once(sfConfig::get('sf_plugins_dir').'/sfDoctrineGuardPlugin/modules/sfGuardAuth/lib/BasesfGuardAuthActions.class.php');

class sfGuardAuthActions extends BasesfGuardAuthActions
{

  public function executeSignin(sfWebRequest $request)
  {
    //if($request->getMethod() == sfWebRequest::)
    $user = $this->getUser();

    if ($user->isAuthenticated()) {
      return $this->redirect('@homepage');
    }

    $message = 'Authentification required';

    $class = sfConfig::get('app_sf_guard_plugin_signin_form', 'sfGuardFormSignin');
    $this->form = new $class();

    // if a user has passed credentials via url (curl, json get request, etc)
    // decode the passed in user/password
    if (isset($_SERVER['PHP_AUTH_USER'])) {
      
     
      $requestSignin = $request->getParameter('signin');
      $csrf_token = $requestSignin['_csrf_token'];

      $request->setParameter('signin',
                             array(
        '_csrf_token' => $csrf_token,
        'username' => base64_decode($_SERVER['PHP_AUTH_USER']),
        'password' => base64_decode($_SERVER['PHP_AUTH_PW'])
      ));

      $this->form->bind($request->getParameter('signin'));
      if ($this->form->isValid()) {
        $values = $this->form->getValues();
        $this->getUser()->signin($values['user']);

        return $this->redirect($request->getUri());
      } else {
        $message = $this->form->getErrorSchema();
      }
    } elseif ($request->isMethod('post')) {
      $this->form->bind($request->getParameter('signin'));
      if ($this->form->isValid()) {
        $values = $this->form->getValues();
        $this->getUser()->signin($values['user'],
                                 array_key_exists('remember', $values) ? $values['remember'] : false);

        // always redirect to a URL set in app.yml
        // or to the referer
        // or to the homepage
        $signinUrl = sfConfig::get('app_sf_guard_plugin_success_signin_url',
                                   $user->getReferer($request->getReferer()));

        return $this->redirect('' != $signinUrl ? $signinUrl : '@homepage');
      }
    } elseif ($request->isXmlHttpRequest()) {
      $this->getResponse()->setHeaderOnly(true);
      $this->getResponse()->setStatusCode(401);

      return sfView::NONE;
    }

    // if we have been forwarded, then the referer is the current URL
    // if not, this is the referer of the current request
    $user->setReferer($this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());

    $module = sfConfig::get('sf_login_module');
    if ($this->getModuleName() != $module) {
      return $this->redirect($module . '/' . sfConfig::get('sf_login_action'));
    }

    $this->getResponse()->setStatusCode(401);
    $header_message = "Basic realm=\"$message\"";

    $this->getResponse()->setStatusCode(401);
    $this->getResponse()->setHttpHeader('WWW_Authenticate', $header_message);

    return sfView::NONE;
  }
  
    public function executeSignout($request)
  {
    $this->getUser()->signOut();

    $signoutUrl = sfConfig::get('app_sf_guard_plugin_success_signout_url', $request->getReferer());

    $this->redirect('' != $signoutUrl ? $signoutUrl : '@homepage');
  }

  public function executeSecure($request)
  {
    $this->getResponse()->setStatusCode(403);
  }

  public function executePassword($request)
  {
    throw new sfException('This method is not yet implemented.');
  }

}