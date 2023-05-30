<?php
/*
 * @var $options array contains all the options the current block we're ediging contains
 * @var $controls NewsletterControls 
 */
?>
<?php $fields->text('q', __('Search')) ?>

<div style="clear: both; max-height: 300px; overflow: scroll; margin-bottom: 15px" id="tnp-giphy-results"></div>

<?php $controls->hidden('giphy_url') ?>
<div id="giphy-preview">
    <?php if (!empty($controls->data['giphy_url'])) { ?>
        <img src="<?php echo esc_attr($controls->data['giphy_url']) ?>" style="max-width: 300px">
    <?php } ?>
</div>


<?php $fields->block_commons() ?>

<script type="text/javascript">

    function choose_gif(url) {
        //jQuery("#tnp-giphy-results").html("");
        jQuery("#options-giphy_url").val(url);
        jQuery("#giphy-preview").html('<img src="' + url + '" style="max-width: 300px">');
        jQuery("#options-giphy_url").trigger("change");
    }

    jQuery("#options-q").keyup(
            function () {
                if (typeof (tid) != "undefined") {
                    window.clearTimeout(tid);
                }
                tid = window.setTimeout(function () {
                    var rating = "r";
                    var limit = 40;
                    var offset = 0;

                    jQuery.get("https://api.giphy.com/v1/gifs/search", {limit: limit, rating: rating, api_key: "57FLbVJJd7oQBZ0fEiRnzhM2VtZp5OP1", q: jQuery("#options-q").val()}, function (data) {
                        jQuery("#tnp-giphy-results").html("");
                        jQuery.each(data.data, function (index, value) {
                            jQuery("#tnp-giphy-results").append('<div style="overflow: hidden; width: 120px; height: 120px; float: left; margin: 5px"><img src="' + value.images.fixed_width_small.url + '" onclick="choose_gif(\'' + value.images.fixed_height.url + '\')" style="float:left; max-width: 100%"></div>');
                        });
                    }, "json").fail(function (x) {
                        alert('There was an error');
                        //console.log(x);
                    });
                }, 500);
            });
    if (jQuery("#options-q").val() !== '')
        jQuery("#options-q").trigger('keyup');

</script>
