# Track-my-SQL
PhP script that will track every database modification based on save_posts hook. This script was made as plugin type

# How to use it
1. Add `index.php` to a folder and add that folder to wp-content/plugins/your-folder-name.
2. Activate the plugin from WP admin.
3. One plugin is activated will track all the changes and create a file post_changes.sql in the same directory.
4. Once you done and don't want to track anymore, simply deactivate the plugin and use the file as needed.

# Good to know
1. This plugin can be modified to be using `save_post` and `acf/save_post` so that way will track changes to content too.
2. It will only track changes to posts&pages.
3. If you want more things to track, I recommend to be using `query` hook or `shutdown` hook, it will track everything.
4. This plugin is free to use. Make sure to leave the author there.
5. You can modify the code in your project as needed.

Have fun.
