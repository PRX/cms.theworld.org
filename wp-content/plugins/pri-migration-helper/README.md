# WP CLI module.
**Example:**
`wp tw-fix command_to_run arg1 arg2 ... argN`
## Commands:
**`(base) tw-fix`**
###
`wp tw-fix get_unprocessed_images`
###
`wp tw-fix get_images_count`
### Fix image size values in all images imported
`wp tw-fix image_fix [all|new limit]`
Command used to fix the img sizes (width and height) using drupal metadata for all images imported.
### Fix media element in all posts
`wp tw-fix posts_content_media_fix [limit comma_separated_post_ids(optional)]`
Command used to replace img tags by image blocks in a all posts.
### Fix media element in a single post content
`wp tw-fix single_post_content_media_fix [post_id]`
Used to test the code used to replace img tags by image blocks in a single post.
### Cleanup Post Tags
`wp tw-fix post_tags_fix_duplicate [start_page_number post_per_page]`
Command to loop through all post_tags, check each term if it is duplicated in other custom taxonomies.
If it is duplicated, add posts related to the post_tag to the custom taxonomy term.
