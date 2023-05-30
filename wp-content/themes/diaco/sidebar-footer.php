<?php
if (is_active_sidebar('footer_sideber')  || is_active_sidebar('footer_4_sidebar')) {
?>
<div class="widget-section">
    <div class="row">
        <!--Footer Column-->
        <?php
        if (is_active_sidebar('footer_sideber')) :
            dynamic_sidebar('footer_sideber');
        endif;
        ?>
    </div>
</div>
<?php
}