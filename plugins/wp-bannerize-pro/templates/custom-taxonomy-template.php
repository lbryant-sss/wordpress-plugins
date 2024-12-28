<?php

get_header(); ?>

<div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">

        <?php if (have_posts()) : ?>
            <?php
            // Start the loop.
            while (have_posts()) :
                the_post();
                the_content();
                ?>
            <?php endwhile; ?>

        <?php endif; ?>

    </div><!-- #content -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
