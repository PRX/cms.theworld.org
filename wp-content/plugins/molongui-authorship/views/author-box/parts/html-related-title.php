<?php
if ( empty( $options['related_title'] ) ) return;
?>

<div class="m-a-box-related-title">
    <?php echo apply_filters( 'authorship/box/related/title', $options['related_title'], $author ); ?>
</div>