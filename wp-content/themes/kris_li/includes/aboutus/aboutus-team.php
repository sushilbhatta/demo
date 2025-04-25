<?php
$team_heading = get_post_meta($post->ID, '_team_heading', true);
$team_paragraph = get_post_meta($post->ID, '_team_paragraph', true);
?>
<section class="team">
    <header class="offer-heading">
        <h4><?php echo $team_heading ?></h4>
        <p><?php echo $team_paragraph ?></p>
    </header>


    <ul class="team-members">
        <?php
        $team_icon     = get_post_meta(get_the_ID(), '_team_icon', true) ?: [];
        $team_title   = get_post_meta(get_the_ID(), '_team_title', true) ?: [];
        $team_description = get_post_meta(get_the_ID(), '_team_description', true) ?: [];

        for ($i = 0; $i < 2; $i++) {
        ?>
            <li class="team-member">
                <!-- icon -->
                <?php if (!empty($team_icon[$i])) : ?>
                    <div class="member_image">
                        <img src=" <?php echo esc_url($team_icon[$i]); ?> " alt=" <?php esc_attr__($team_title[$i]); ?>" />
                    </div>
                <?php endif; ?>
                <div class="member_info">
                    <?php if (!empty($team_title[$i])) : ?>
                        <h4>
                            <?php echo esc_html($team_title[$i]); ?>
                        </h4>
                    <?php endif; ?>

                    <?php if (!empty($team_description[$i])) : ?>
                        <p>
                            <?php echo esc_html($team_description[$i]); ?>
                        </p>
                    <?php endif; ?>

                </div>
            </li>
        <?php
        }
        ?>
    </ul>

</section>