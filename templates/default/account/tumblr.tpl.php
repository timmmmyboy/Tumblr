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
                      Nice! You are now connected to Tumblr.
                    </p>

                    <?php
                    $tumblr = \Idno\Core\site()->plugins()->get('Tumblr');
                    $tumblrAPI = $tumblr->connect();

                    if ($accounts = \Idno\Core\site()->syndication()->getServiceAccounts('tumblr')) {

                      foreach ($accounts as $account) {
                        $avatar = $tumblrAPI->oauth_get('/blog/'.$account['username'].'/avatar');
                        $bloginfo = $tumblrAPI->get('/blog/'.$account['username'].'/info');
                        ?>

                        <div class="well">
                          <div class="row">
                            <div class="span1">
                              <img src="<?php echo $avatar->response->avatar_url ?>"/>
                            </div>
                            <div class="span4">
                              <h3><?php echo $bloginfo->response->blog->title; ?></h3>
                            </div>
                            <div class="span4">
                              <input type="hidden" name="remove" value="<?= $account['username'] ?>"/>
                              <button type="submit"
                              class="connect tw connected">Disconnect</button>
                            </div>
                          </div>
                        </div>
                          <?php

                        }

                      }

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
