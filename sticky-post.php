<?php
/*
	File Name: Sticky Post
	Description: Function to handle the sticky post
	Version: 1.0
	Author: zourbuth
	Author URI: http://zourbuth.com
	License: GPL2

	Copyright 2011  zourbuth.com  (email : zourbuth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function sticky_post_widget( $args ) {
	extract($args, EXTR_SKIP);
	$html = '';
	
	$posts = get_posts( array('post__in' => get_option('sticky_posts'), 'showposts' => $items, 'order' => $order_by)); 
	$postarr = implode(",", get_option('sticky_posts')); 

	global $wpdb;
	
	$tax = !empty($taxonomy) ? "AND tt.term_id IN ($taxonomy)" : "";
	
	$query = "	SELECT p.*, count(tr.object_id) as count 
				FROM $wpdb->term_taxonomy AS tt, $wpdb->term_relationships AS tr, 
				$wpdb->posts AS p WHERE tt.term_taxonomy_id = tr.term_taxonomy_id 
				AND tr.object_id  = p.ID 
				$tax 
				AND p.post_status = 'publish'
				AND p.post_date_gmt < NOW()
				AND p.ID IN ($postarr) 
				GROUP BY tr.object_id
				ORDER BY count DESC, p.post_date_gmt $order_by
				LIMIT 0," . $items;
	$posts = $wpdb->get_results($query);

	// Ok then, lets we proceed the posts into the template.
	if (empty($posts)) {
		$html .= "No post(s) with your current queries";
	} else {
		$html .= "<ul class='cpw template-$template'>";

		foreach($posts as $post) {
			$html.= '<li style="min-height: ' . ( $icon_height + 5 ) . 'px">';

				if ( $show_thumbnail ) {
					$html .= '<div class="cpw-post-thumbnail" style="height:' . $icon_height . 'px; width:' . $icon_width . 'px;" />';
					if ( has_post_thumbnail( $post->ID ) ) {
						$image_id = get_post_thumbnail_id( $post->ID );
						$imgsrc = wp_get_attachment_image_src($image_id, 'thumbnail', true);
						$image_url = $imgsrc[0];
					} else {
						$image_url = STICKY_POST_URL . 'images/thumbnail.png';
					}
					
					//-------- CHANGE THE TIMTHUMB LINE HERE -------------------------/
					$html .= '<a href="'.get_permalink($post->ID).'">';
					$html .= '<img class="cpw-thumbnail" src="' . $image_url . '" alt="' . $post->post_title . '" />';
					$html .= '</a>';
					//----------------------------- END LINE -------------------------/			
					
					$html .= '</div>';
				}

				if ( $template != 'block' )
					$html .= "<div class='cpw-post-info' style='padding-$template: " . ( $icon_width + 10 ) . "px;'>";
				else
					$html .= "<div class='cpw-post-info'>";

					$html .= '<a class="cpw-title" href="' . get_permalink( $post->ID ). '">' . $post->post_title . '</a>';
					
					if ( $show_date )
						$html .= '<a class="cpw-date" href="' . get_permalink( $post->ID ). '">' . mysql2date( $date_format , $post->post_date, false) . '</a>';
					
					if ( $show_comments )
						$html .= '<a class="cpw-comments" href="' . get_comments_link(  $post->ID ) . '">' . get_comments_number( $post->ID ) . '</a>';
					
					if ( $show_excerpt )
						$html .= '<p>' . sticky_post_excerpt($post->ID, $excerpt_length, $excerpt_more) . '</p>';	
					
				$html .= '</div>';
				$html .= '<div class="clear"></div>';
			$html .= '</li>';
		}
		
		$html.= '</ul>';
	}
	
	return $html;
}

function sticky_post_excerpt($post_id, $excerpt_length = 15, $excerpt_more = '...'){
    $the_post = get_post($post_id); //Gets post ID
    $the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
    $the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
    $words = explode(' ', $the_excerpt, $excerpt_length + 1);

    if(count($words) > $excerpt_length) :
        array_pop($words);
        array_push($words, $excerpt_more);
        $the_excerpt = implode(' ', $words);
    endif;

    return $the_excerpt;
}
?>