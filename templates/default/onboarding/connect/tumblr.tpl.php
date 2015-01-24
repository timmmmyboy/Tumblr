<?php

    if (empty(\Idno\Core\site()->session()->currentUser()->tumblr)) {
        $login_url = \Idno\Core\site()->config()->getDisplayURL() . 'tumblr/auth';
    } else {
        $login_url = \Idno\Core\site()->config()->getDisplayURL() . 'tumblr/deauth';
    }

?>
<div class="social">
    <a href="<?= $login_url ?>" class="connect tm <?php

        if (!empty(\Idno\Core\site()->session()->currentUser()->tumblr)) {
            echo 'connected';
        }

    ?>" target="_top">Tumblr<?php

            if (!empty(\Idno\Core\site()->session()->currentUser()->tumblr)) {
                echo ' - connected!';
            }

        ?></a>
    <label class="control-label">Share pictures, updates, and posts to Tumblr.</label>
</div>
