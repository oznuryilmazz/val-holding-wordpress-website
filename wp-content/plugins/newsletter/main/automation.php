<?php
/* @var $this Newsletter */

defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
?>

<style>
<?php include __DIR__ . '/css/automation.css' ?>
</style>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <!--
    <div id="tnp-heading">
        <h2>Automated and Autoresponder</h2>
    </div>
    -->

    <div id="tnp-body">

        <div class="tnp-promo-widgets">

            <div class="tnp-promo-widget">
                <h3>You create, we send</h3>
                <h4>with Automated Addon</h4>
                <p>
                    Articles, products, events, recipes: Automated gets them, creates
                    a newsletter with your preferred design and sends it daily, weekly, monthly
                    or even more complex scheduling.
                </p>
                <p>
                    Yes! It takes care to check if there isn'tnew content, 
                    to target the right subscribers by  list and language, 
                    to autogenerate the subject, ...
                </p>
                <p>
                    Not only a single automatic newsletter with its scheduling and targeting 
                    but as many as you want.
                </p>
                <p>
                    Of course, full statistics are collected for each newsletter.
                </p>

                <div class="tnp-ctas">
                    <a href="https://www.thenewsletterplugin.com/automated?utm_campaign=automation&utm_source=plugin" class="tnp-cta" target="_blank">I want to know more</a>
                    <a href="https://www.thenewsletterplugin.com/premium?utm_campaign=automation&utm_source=plugin" class="tnp-cta tnp-cta-green" target="_blank">How much is it?</a>
                </div>
            </div>


            <div class="tnp-promo-widget">
                <h3>Be at your subscriber's side</h3>
                <h4>with Autoresponder Addon</h4>
                <p>
                    Prepare what you want to say and when you want to say: Autoresponder will follow up subscribers 
                    starting from the first contact like if you are conversating with her/him.
                </p>
                <p>
                    They can be lessons, tips, step-by-step guides, challenges, and everything else that comes to your mind.
                </p>
                <p>
                    One conversation or more? You can create as many conversations as you want, of course!
                </p>

                <div class="tnp-ctas">
                    <a href="https://www.thenewsletterplugin.com/autoresponder?utm_campaign=automation&utm_source=plugin" target="_blank" class="tnp-cta">It sounds cool!</a>
                    <a href="https://www.thenewsletterplugin.com/premium?utm_campaign=automation&utm_source=plugin" class="tnp-cta tnp-cta-green" target="_blank">How much is it?</a>
                </div>
            </div>

        </div>

    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
