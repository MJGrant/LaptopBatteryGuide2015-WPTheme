<?php

remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'mjg_category_loop' );

function mjg_category_loop( $grid_classes ) {
	
	//echo '<div class="welcomeText"><p>Welcome to Laptop Battery Guide, a guide to the best laptop and smartphone technology to buy. Your purchase through one of our affiliate links supports our independent research. </br></br>Read more about us.</p></div>';
	global $_genesis_loop_args, $loop_counter;
	global $post;
	$columnCounter = 1;
	$columns = 3; // Alter this number to change the number of columns
	// Be able to convert the number of columns to the class name in Genesis
	$fractions = array( '', 'half', 'third', 'fourth', 'fifth', 'sixth' );
	//categories are going into an array! here are the parameters this array should have
	$cat_args = array(
		'orderby' 				=> 'name',
		'order' 				=> 'ASC',
		'exclude'				=> '1', //omit the uncategorized category
	);
	
	
	//get the categories and apply the arguments from the array above
	$categories=get_categories($cat_args);
	//$categories = wp_list_categories('exclude=1');
	//echo '<p>Welcome to Laptop Battery Guide, a guide to the best laptop and smartphone technology to buy. Your purchase through one of our affiliate links supports our independent research.</p>';
	/*
	// Add genesis-grid-column-? class to know how many columns across we are
	$grid_classes[] = sprintf( 'genesis-grid-column-%d', $column_number );
	// Add one-* class to make it correct width
	$grid_classes[] = sprintf( 'one-' . $fractions[$columns - 1], $columns );
	*/
	
	
	//print a category and advance to next column	
	//for each category make an array of posts that belong to it
	echo '<table class="homeTable"><tr>';
	foreach($categories as $category) {
		    $args=array(
		     'showposts' 		=> 3, 							//I want X posts per category
		     'category__in' 	=> array($category->term_id),	//in this category
		     'ignore_sticky_posts'	=> 1						//0 dont ignore sticky posts, 1 ignore sticky posts
		    );
			//we are still inside the category "for each" loop
			//get all the posts that belong to this category
			$posts=get_posts($args);
			//if there are posts in this category, print them
			  	if ($posts) {
			  		if ($columnCounter <= $columns) {
			  			//print the category title at the top of the column
				    	echo '<td><p class="catGridTitle"><a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s" ), $category->name ) . '" ' . '>' . $category->name.'</a></p>';
						echo '<ul>';
						//echo '<p>Column #: ' . $columnCounter . ' of ' . $columns . '</p>';
						//for each post, make a li item
				    	foreach($posts as $post) {
				      		setup_postdata($post);
							?>
		      				<p><li><?php the_post_thumbnail(array(65,65),none);?><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></li></p>
		  					<?php
						}
						
						//print the "View all posts in this category" link
						echo '<p id="seeMore">View all articles in <a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s" ), $category->name ) . '" ' . '>' . $category->name. ' &#8594; </a></p>';
						echo '</ul>';
						echo '</td>';
						
						//increment the column counter
						$columnCounter++;
						//was this the last column in the row? check and find out and if yes, start a new row before going back to the beginning
						if ($columnCounter > $columns) {
							$columnCounter =1;
							echo '</tr>';
							echo '<tr>';
						}
					} else {
						//end the row and start a new one with a reset counter
						//actually I don't think this should ever get called...
						echo '<p>else was called</p>';
					}
		  		} // if ($posts 
		} // foreach($categories
	echo '</tr></table>';
}

genesis ();