<div class="row">

  <div class="span10 offset1">
    <?=$this->draw('account/menu')?>
    <h1>Tumblr</h1>

  </div>

</div>
<div class="row">
  <div class="span10 offset1">
    <?php

    if (!empty(\Idno\Core\site()->config()->tumblr['consumer_key']) && !empty(\Idno\Core\site()->config()->tumblr['consumer_secret'])) {

      ?>
      <form action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/tumblr/" class="form-horizontal" method="post">
        <?php
        if (empty(\Idno\Core\site()->session()->currentUser()->tumblr)) {
          ?>

          <div class="control-group">
            <div class="controls-config">

              <div class="row">
                <div class="span6">
                  <p>
                    Easily share updates, posts, and pictures to Tumblr. </p>
                    <p>
                      With Tumblr connected, you can cross-post content that you publish publicly on your site.
                    </p>


                    <div class="social span6">
                      <p>
                        <a href="<?= $vars['oauth_url'] ?>" class="connect tw">Connect Tumblr</a>
                      </p>
                    </div>


                  </div>
                </div>
              </div>
            </div>

            <?php

          } else {

            ?>
            <div class="control-group">
              <div class="controls-config">
                <div class="row">
                  <div class="span10">
                    <p>
                      Nice! You are now connected to Tumblr. Please choose which blogs you would like to syndicate (you can always come back and adjust these settings at any time).
                    </p>

                    <?php
                    $tumblr = \Idno\Core\site()->plugins()->get('Tumblr');
                    $tumblrAPI = $tumblr->connect();
                    $info = $tumblrAPI->oauth_get("/user/info");
                    foreach($info->response->user->blogs as $blog){
                      $hostname = $tumblr->getHostname($blog->url);
                      echo '<div class="well">';
                      $avatar = $tumblrAPI->oauth_get('/blog/'.$hostname.'/avatar');
                      echo '<div class="row">
                      <div class="span1">
                      <img src="'.$avatar->response->avatar_url.'"/>';
                      $bloginfo = $tumblrAPI->get('/blog/'.$hostname.'/info');
                      echo '</div>
                      <div class="span4">
                      <h3>'.$bloginfo->response->blog->title.'</h3>';
                      echo '</div>
                      <div class="span4">
                      <input type="hidden" name="remove" value="1" />
                      <button type="submit" class="connect tw">Enable Syndication</button>
                      </div>
                      </div>
                      </div>';
                    }
                    echo '</ul>';

                    ?>
                  </div>
                </div>
              </div>
            </div>

            <?php

          }

          ?>

          <?= \Idno\Core\site()->actions()->signForm('/account/tumblr/')?>

        </form>
        <?php

      } else {

        if (\Idno\Core\site()->session()->currentUser()->isAdmin()) {

          ?>
          <div class="control-group">
            <div class="controls-config">
              <div class="row">
                <div class="span6">
                  <p>
                    Before you can begin connecting to Tumblr, you need to set it up.
                  </p>
                  <p>
                    <a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>admin/tumblr/">Click here to begin
                      Tumblr configuration.</a>
                    </p>
                    <?php

                  } else {

                    ?>
                    <p>
                      The administrator has not finished setting up Tumblr on this site.
                      Please come back later.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <?php

          }

        }

        ?>
      </div>
    </div>
