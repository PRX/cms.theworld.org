<?php
if ( empty( $options['profile_title'] ) ) return;
?>

<div class="m-a-box-profile-title">
    <?php echo apply_filters( 'authorship/box/profile/title', $options['profile_title'], $author ); ?>
</div>