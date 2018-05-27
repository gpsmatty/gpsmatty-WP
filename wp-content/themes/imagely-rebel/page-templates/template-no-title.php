<?php

/**
 * Template Name: No Title
 */

/* Removes the title from the page using Genesis hook. */
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

/* Run it all */
genesis();