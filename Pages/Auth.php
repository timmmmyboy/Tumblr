<?php

    /**
     * Plugin administration
     */

    namespace IdnoPlugins\Tumblr\Pages {

        /**
         * Default class to serve the homepage
         */
        class Auth extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->gatekeeper(); // Logged-in users only
                if ($tumblr = \Idno\Core\site()->plugins()->get('Tumblr')) {
                  session_start();
                    $login_url = $tumblr->getAuthURL();
                    if (!empty($login_url)) {
                        $this->forward($login_url); exit;
                    }
                }
                $this->forward($_SERVER['HTTP_REFERER']);
            }

            function postContent() {
                $this->getContent();
            }

        }

    }
