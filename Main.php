<?php

namespace IdnoPlugins\Tumblr {

  class Main extends \Idno\Common\Plugin
  {

    function registerPages()
    {
      // Auth URL
      \Idno\Core\site()->addPageHandler('tumblr/auth', '\IdnoPlugins\Tumblr\Pages\Auth');
      // Deauth URL
      \Idno\Core\site()->addPageHandler('tumblr/deauth', '\IdnoPlugins\Tumblr\Pages\Deauth');
      // Register the callback URL
      \Idno\Core\site()->addPageHandler('tumblr/callback', '\IdnoPlugins\Tumblr\Pages\Callback');
      // Register admin settings
      \Idno\Core\site()->addPageHandler('admin/tumblr', '\IdnoPlugins\Tumblr\Pages\Admin');
      // Register settings page
      \Idno\Core\site()->addPageHandler('account/tumblr', '\IdnoPlugins\Tumblr\Pages\Account');

      // Add menu items to account & administration screens
      \Idno\Core\site()->template()->extendTemplate('admin/menu/items', 'admin/tumblr/menu');
      \Idno\Core\site()->template()->extendTemplate('account/menu/items', 'account/tumblr/menu');
      \Idno\Core\site()->template()->extendTemplate('onboarding/connect/networks', 'onboarding/connect/tumblr');
    }

    function registerEventHooks()
    {

      \Idno\Core\site()->syndication()->registerService('tumblr', function () {
        return $this->hasTumblr();
      }, array('article'));

      if ($this->hasTumblr()) {
        if (is_array(\Idno\Core\site()->session()->currentUser()->tumblr)) {
          foreach(\Idno\Core\site()->session()->currentUser()->tumblr as $username => $details) {
            if (!in_array($username, ['user_token','user_secret','screen_name'])) {
              \Idno\Core\site()->syndication()->registerServiceAccount('tumblr', $username, $username);
            }
          }
        }
      }

      // Function for articles
      $article_handler = function (\Idno\Core\Event $event) {
        if ($this->hasTumblr()) {
          $eventdata = $event->data();
          $object     = $eventdata['object'];
          if (!empty($eventdata['syndication_account'])) {
            $blogName  = $eventdata['syndication_account'];
            $tumblrAPI  = $this->connect();
          } else {
            $tumblrAPI  = $this->connect();
          }
          $status     = $object->getTitle();
          $status .= ' ' . $object->getURL();
          $params = array(
          'status' => $status
          );

          $response = $tumblrAPI->createPost($blogName, $params);

          if (!empty($tumblrAPI->response['response'])) {
            if ($json = json_decode($tumblrAPI->response['response'],true)) {
              if (!empty($json['posts'])) {
                $object->setPosseLink('tumblr', $json['posts'][0]['post_url']);
                $object->save();
              }
            }
          }

        }
      };

      // Push "articles" to Tumblr
      \Idno\Core\site()->addEventHook('post/article/tumblr', $article_handler);

    }

    /**
    * Retrieve the OAuth authentication URL for the API
    * @return string
    */
    function getAuthURL()
    {
      $tumblr    = $this;
      $tumblrAPI = $tumblr->connect();
      if (!$tumblrAPI) {
        return '';
      }
      // Get the request tokens based on your consumer and secret and store them in $token
      $token = $tumblrAPI->getRequestToken();

      // Set session of those request tokens so we can use them after the application passes back to your callback URL
      \Idno\Core\site()->session()->set('oauth_token', $token['oauth_token']);
      \Idno\Core\site()->session()->set('oauth_token_secret', $token['oauth_token_secret']);

      // Grab the Authorize URL and pass through the variable of the oauth_token
      $data = $tumblrAPI->getAuthorizeURL($token['oauth_token']);

      return $data;
    }

    /**
    * Returns a new Tumblr OAuth connection object, if credentials have been added through administration
    * and it's possible to connect
    *
    * @return bool|\Tumblr
    */
    function connect()
    {
      include(dirname(__FILE__) . '/external/tumblrPHP/lib/tumblrPHP.php');
      if (!empty(\Idno\Core\site()->config()->tumblr)) {
        $consumer_key = \Idno\Core\site()->config()->tumblr['consumer_key'];
        $consumer_secret = \Idno\Core\site()->config()->tumblr['consumer_secret'];
        $token = \Idno\Core\site()->session()->currentUser()->tumblr['user_token'];
        $token_secret = \Idno\Core\site()->session()->currentUser()->tumblr['user_secret'];
        if($token && $token_secret){
          return new \Tumblr($consumer_key,$consumer_secret,$token,$token_secret);
        }
        else{
          return new \Tumblr($consumer_key,$consumer_secret);
        }
      }

      return false;
    }

    /**
    * Can the current user use Tumblr?
    * @return bool
    */
    function hasTumblr()
    {
      if (\Idno\Core\site()->session()->currentUser()->tumblr) {
        return true;
      }

      return false;
    }

    /**
    * Tumblr requires clean hostnames but provides full URL strings.
    * @return string
    */
    function getHostname($input){
      // in case scheme relative URI is passed, e.g., //www.google.com/
      $input = trim($input, '/');

      // If scheme not included, prepend it
      if (!preg_match('#^http(s)?://#', $input)) {
        $input = 'http://' . $input;
      }

      $urlParts = parse_url($input);
      
      // remove www
      $domain = preg_replace('/^www\./', '', $urlParts['host']);

      return $domain;
    }

  }

}
