
/**
 * @wordpress-plugin
 * Plugin Name:       Kntnt Category Blog URLs
 * Plugin URI:        https://github.com/Kntnt/kntnt-blog-url
 * Description:       Changes the URL structure of a category with the slug `blog`. Remember to flush permalinks (e.g. by just visisting the permalink settings) after activating or deractivating this plugin.
 * Version:           1.0.0.
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Kntnt\Category_Blog_URLs;

defined('ABSPATH') && new Plugin;

class Plugin {

	public function __construct() {
		add_filter( 'term_link', [ $this, 'blog_link' ], 10, 3 );
		add_filter( 'post_link', [ $this, 'blog_post_link' ], 10, 3 );
		add_action( 'generate_rewrite_rules', [ $this, 'rewrite_rules' ] );
	}

	public function blog_link( $permalink, $term, $taxonomy ) {
		if( ! empty( $category_slug = $term->slug ) && $category_slug == 'blog' ) {
			$permalink = $this->blog_url();
		}
		return $permalink;
	}

	public function blog_post_link( $permalink, $post, $leavename ) {
		if ( ! empty( $category = get_the_category($post->ID) ) && $category[0]->slug == 'blog' ) {
			$permalink = $this->blog_url ( "{$post->post_name}/{$post->ID}" );
		}
		return $permalink;
	}

	public function rewrite_rules( $wp_rewrite ) {
		if ($cat = get_category_by_slug( 'blog' ) ) {
			$new_rules['^investors/blog/[^/]+/(\d+)/?$'] = 'index.php?p=$matches[1]';
			$new_rules['^investors/blog/?$'] = "index.php?cat={$cat->term_id}";
			$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;		
		}
		return $wp_rewrite;
	}

	private function blog_url( $slug = '' ) {
		$path = '/investors/blog';
		if ( $slug ) {
			$path = "$path/$slug";
		}
		if ( substr( get_option( 'permalink_structure' ), -1 ) == '/' ) {
			$path = "$path/";
		}
		return home_url( $path );
	}

}
