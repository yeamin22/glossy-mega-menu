<?php
/**
 * Template Name: Easyformwebsite Template
 */
get_header();
use Ewformp_Core\API;
if ( class_exists( "Ewformp_Core\API" ) ) {
    define( "ASSETS_URI", "https://api.easywebsiteform.com/storage/" );
    define( "TEMPLATE_URI", "https://apps.easywebsiteform.com/template/useit" );
    $api = new API();
    $orderbyes = [ 
        'created_at:desc' => "Newest", 
        'ratings_count:desc' => "Top Rated", 
        'downloads_count:desc' => "Top downloads",          
    ];  

    $category_slug = $order_by = $keyword = '';
    if ( isset( $_GET['category_slug'] ) ) {
        $category_slug = $_GET['category_slug'];
    }
    if ( isset( $_GET['keyword'] ) ) {
        $keyword = $_GET['keyword'];
    }
    if ( isset( $_GET['order_by'] ) ) {
        $order_by = $_GET['order_by'];
    }
    $categories = $api->get_template_categories();
    $args = [
        'category_slug' => $category_slug,
        'order_by'      => $order_by,
        'keyword'       => $keyword,
    ];
    $templates = $api->get_templates( $args );

} else {
    wp_die( "Sorry try again" );
}
?>
<div class="elementor-element elementor-element-e084d18 e-con-full e-flex e-con e-parent template-breadcrumb" data-id="e084d18" data-element_type="container" data-settings="{&quot;background_background&quot;:&quot;gradient&quot;}">
	<div class="elementor-element elementor-element-24de371 elementor-widget elementor-widget-heading" data-id="24de371" data-element_type="widget" data-widget_type="heading.default">
		<div class="elementor-widget-container">
			<h1 class="elementor-heading-title elementor-size-default"><?php the_title(); ?></h1>		</div>
	</div>
</div>
<div class="ewform_templates_container">
    <div class="container">
        <div class="ewf-row">
            <div class="col-lg-4 col-sm-12">
                <div class="ewform_categories">
                    <h4 class="border-bottom mb-3 d-flex align-items-center cat_heading">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512"
                            class="me-2" height="1.3rem" width="1.3rem" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M149.333 216v80c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-80c0-13.255 10.745-24 24-24h101.333c13.255 0 24 10.745 24 24zM0 376v80c0 13.255 10.745 24 24 24h101.333c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H24c-13.255 0-24 10.745-24 24zM125.333 32H24C10.745 32 0 42.745 0 56v80c0 13.255 10.745 24 24 24h101.333c13.255 0 24-10.745 24-24V56c0-13.255-10.745-24-24-24zm80 448H488c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24zm-24-424v80c0 13.255 10.745 24 24 24H488c13.255 0 24-10.745 24-24V56c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24zm24 264H488c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24z">
                            </path>
                        </svg>
                        Categories
                    </h4>
                    <ul class="ewform_categories_list">
                        <?php
                            if ( $categories ) {
                                foreach ( $categories as $cat ) {
                                    if ( isset($cat['children']) ) {
                                        echo "<li class='has-child'>";
                                        $templateNum = intval( $cat['templates_count'] ) + intval( $cat['children'][0]['templates_count'] );
                                        printf( "<a href='?category_slug=%s'><span class='temp_number'>%s(%s)</span> <button class='reset_style'><svg stroke='currentColor' fill='currentColor' stroke-width='0' viewBox='0 0 320 512' height='1em' width='1em' xmlns='http://www.w3.org/2000/svg'><path d='M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z'></path></svg></button></a>", $cat['slug'], esc_html( $cat['name'] ), $templateNum );

                                        echo "<ul class='cat_children'>";
                                        if ( isset( $cat['children'] ) ) {
                                            foreach ( $cat['children'] as $child_cat ) {
                                                printf( '<li class="list-unstyled"><a href="?category_slug=%s"><span class="temp_number">%s(%s)</span></a></li>', $child_cat['slug'], $child_cat['name'], $child_cat['templates_count'] );
                                            }
                                        }
                                        echo "</ul></li>";
                                    } else {
                                        printf( '<li><a href="?category_slug=%s"><span class="temp_number">%s(%s)</span></a></li>', $cat['slug'], esc_html( $cat['name'] ), $cat['templates_count'] );
                                    }
                                }
                            } else {
                                printf( "Not found any category" );
                            }
                            ?>
                    </ul>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="ewform_templates_container_right">
                    <div class="filter_options_header ewf-row justify-content-center">
                        <div class="col-md-12 col-lg-8 col-sm-12">
                            <form>
                                <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                                    <div class="input-group">
                                        <span class="input-group-text text-body cursor-pointer px-3">
                                            <a href="#" id="search_icon">
                                                <i class="fas fa-search" aria-hidden="true"></i>
                                            </a>
                                        </span>
                                        <input type="text" id="keyword" name="keyword" class="form-control"
                                            placeholder="Type here..." value="">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-dark d-none">Submit</button>
                            </form>
                        </div>
                        <div class="col-md-12 col-lg-12 col-sm-12 d-flex align-items-center justify-content-end">
                            <ul class="btn-group" role="group" aria-label="Basic example"> 
                                <?php
                                    foreach($orderbyes as $key => $order_text){
                                        $active = '';
                                        if(($key == $order_by && !empty($order_by))){
                                            $active= "active";                               
                                        }
                                        printf('<li class="%s" ><a href="#" data-filter="%s">%s</a></li>',$active,$key,$order_text);
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="ewform_templates ewf-row">
                        <?php
                if ( $templates ) {
                    foreach ( $templates as $template ) {
                        ?>
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="ewf_templates_cardImage">
                                      
                                            <img alt="Book a Bed Room" sizes="100vw"
                                                src="<?php echo ASSETS_URI . $template['thumbnail']['path']; ?>"
                                                width="600" height="661" decoding="async" data-nimg="1" loading="lazy"
                                                style="color: transparent; width: 100%; height: auto;">
                                       
                                    </div>
                                    <h6 class="card-title mb-0">
                                        <a href="#">
                                            <?php echo $template['title']; ?>
                                        </a>
                                    </h6>
                                    <div class="ewf-row">
                                             <small class="text-muted cat_name"> Category in
                                                <?php
                                                    if ( $template['template_category'] ) {
                                                        printf( '<a href="">%s</a>', $template["template_category"]["name"] );
                                                    }
                                                    ?>
                                            </small>
                                        <div class="col-lg-8">        
                                            <small class="text-muted"><?php echo $template['downloads_count']; ?>
                                                Downloads</small><br>
                                            <small class="text-muted">Published at
                                                <?php
                                                    $date = date_create( $template['created_at'] );
                                                    echo date_format( $date, "F j, Y" );
                                                ?>
                                            </small><br>
                                            <small class="text-muted d-flex align-items-center"> </small>
                                        </div>
                                        <div class="col-lg-2 justify-content-lg-end align-item-end d-flex">
                                            <a href="<?php echo add_query_arg( "tid", $template['id'], TEMPLATE_URI ); ?>"
                                                type="button" class="btn btn-primary mb-0">USE</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- End col-lg-3 -->
                        <?php
                                }
                            } else {
                                printf( "Not Found Any Template" );
                            }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
?>