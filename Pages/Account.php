<?php

    /**
     * Plugin administration
     */

    namespace IdnoPlugins\Tumblr\Pages {

        /**
         * Default class to serve the homepage
         */
        class Account extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->gatekeeper(); // Logged-in users only
                $oauth_url = \Idno\Core\site()->config()->getDisplayURL() . 'tumblr/auth';
                $t = \Idno\Core\site()->template();
                $body = $t->__(array('oauth_url' => $oauth_url))->draw('account/tumblr');
                $t->__(array('title' => 'Tumblr', 'body' => $body))->drawPage();
            }

            function postContent() {
                $this->gatekeeper(); // Logged-in users only
                if (($this->getInput('remove'))) {
                    $user = \Idno\Core\site()->session()->currentUser();
                    $user->tumblr = array();
                    $user->save();
                    \Idno\Core\site()->session()->addMessage('Your Tumblr settings have been removed from your account.');
                }
                $this->forward(\Idno\Core\site()->config()->getDisplayURL() . 'account/tumblr/');
            }

        }

    }
