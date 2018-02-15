<?php

/**
* Plugin administration
*/

namespace IdnoPlugins\tumblr\Pages {

  /**
  * Default class to serve the homepage
  */
  class Callback extends \Idno\Common\Page
  {

    function get($params = array())
    {
      $this->gatekeeper(); // Logged-in users only
      if ($token = $this->getInput('oauth_token')) {
        if ($tumblr = \Idno\Core\site()->plugins()->get('Tumblr')) {
          $user = \Idno\Core\site()->session()->currentUser();
          $user->tumblr = array('user_token' => \idno\Core\site()->session()->get('oauth_token'), 'user_secret' => \idno\Core\site()->session()->get('oauth_token_secret'));
          $user->save();
          $tumblrAPI = $tumblr->connect();
          // The oauth_verfier is set back from Tumblr and is needed to obtain access tokens
          $oauth_verifier = $_GET['oauth_verifier'];

          // Use the getAcessToken method and pass through the oauth_verifier to get tokens;
          $token = $tumblrAPI->getAccessToken($oauth_verifier);
          $info = $tumblrAPI->oauth_get("/user/info");
          $user->tumblr = array('user_token' => $token['oauth_token'], 'user_secret' => $token['oauth_token_secret']);
          foreach($info->response->user->blogs as $blog){
            $hostname = $tumblr->getHostname($blog->url);
            $avatar = $tumblrAPI->oauth_get('/blog/'.$hostname.'/avatar');
            \Idno\Core\site()->syndication()->registerServiceAccount('tumblr', $hostname, $blog->title);
            $user->tumblr[$hostname] = array(
              'user_token' => $token['oauth_token'],
              'user_secret' => $token['oauth_token_secret'],
              'avatar' => $avatar->response->avatar_url,
              'title' => $blog->title
              );
          }

          $user->save();
          \Idno\Core\site()->session()->addMessage('Tumblr credentials saved!');

        }

        if (!empty($_SESSION['onboarding_passthrough'])) {
          unset($_SESSION['onboarding_passthrough']);
          $this->forward(\Idno\Core\site()->config()->getURL() . 'begin/connect-forwarder');
        }
        $this->forward('/account/tumblr');
      }
    }
  }

}
