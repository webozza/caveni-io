<?php
    $ticket_status = get_post_meta($ticket->ID,'_caveni_ticket_status',true);
    $status_class = 'bg-success';
    $status_class_tb = 'bg-success';
    if($ticket_status == 'closed'){
        $status_class = 'bg-danger';
        $status_class_tb = 'bg-danger';
    }
    $ticket_attachment = get_wp_scaled_image_url($ticket->ID,"full");
?>
<div class="landing-page-wrapper">
<!-- Start::app-content -->
<div class="main-content landing-main px-0">
    <!--Div-->
    <div>
        <div class="cover-image sptb section-bg">
            <div class="main-container">
                <div class="row">
                <div class="col-xl-8">
                        <div class="card custom-card ticket-info-card-block">
                            <div class="card-header justify-content-between">
                                <div>
                                    <h4 class="card-title mb-1 fs-22">
                                        <i class="fe fe-file-text"></i>    
                                        <?php echo $ticket->post_title; ?> 
                                    </h4>
                                    <small class="fs-13"><i class="fe fe-clock text-muted me-1"></i> <?php echo sprintf("%s <span class='text-muted'> %s.</span>",$modified_by_text,$postedAtString); ?></small>
                                </div>
                                <div class="card-options">
                                    <span class="badge <?php echo $status_class; ?> fs-10"><?php echo strtoupper($ticket_status); ?></span>
                                </div>
                            </div>
                            <div class="card-body pt-2">
                                <p><?php echo $ticket->post_content; ?></p>
                                <?php if($ticket_attachment != "") { ?>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-3 mt-2">
                                            <a href="<?php echo $ticket_attachment; ?>" download class="attach-supportfiles">
                                                <span class="img-options-remove" data-bs-toggle="remove"><i class="fe fe-x"></i></span>
                                                <i class="far fa-file"></i>
                                                <div class="attach-title">
                                                    
                                                        <?php echo esc_html( basename( esc_html( $ticket_attachment ) ) ); ?>
                        
                                                    </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if($ticket_comments) : ?>
                        <div class="card support-converbody custom-card">
                            <div class="card-header border-0">
                                <h4 class="card-title">
                                    <i class="fe fe-message-circle"></i>     
                                    <?php echo __('Comments','caveni-io'); ?></h4>
                            </div>

                            <div class="row">
                                        <div class="col-12">
                                            <div class="alert d-none m-3" id="deleted_ticket_comment_confirmation_alert" role="alert"></div>
                                        </div>
                                    </div>
                            <?php 
                                    foreach ($ticket_comments as $comment) :
                                        //$author = get_comment_author( $comment->comment_ID); 
                                        $author_avatar = get_avatar( $comment->user_id, 48 );
                                        $is_admin = check_if_user_is_admin($comment->user_id);
                                        //echo "<pre>";print_r($comment);exit;
                                        $to_time = strtotime("now");
                                        $from_time = strtotime($comment->comment_date_gmt);
                                        $diff_in_minutes = round(abs($to_time - $from_time) / 60,2);
                                        $comment_creator_class  = ($is_admin) ? "bg-custom-admin" : "bg-custom-client";
                                        $comment_creator_text = ($is_admin) ? __('ADMIN','caveni-io') : __('CLIENT','caveni-io');
                                        $comment_attachment = get_wp_scaled_image_url($comment->comment_ID,'full',true);
                                        //$file_path           = !empty($comment_attachment) ? $comment_attachment[0] : '';
                                        // /echo "<pre>";var_dump($comment_attachment);exit;
                                        
                            ?>
                                        <div class="card-body border-bottom">
                                            <div class="d-sm-flex">
                                                <div class="d-flex mr-3">
                                                    <div class="avatar avatar-lg avatar-rounded">
                                                       <!-- <img src="../assets/images/users/16.jpg" alt="img">-->
                                                        <?php echo $author_avatar; ?>

                                                    </div>
                                                </div>
                                                <div class="media-body comments-body">
                                                    <h5 class="mt-1 mb-1 fw-semibold"><?php echo $comment->comment_author; ?> 
                                                        <span class="badge <?php echo $comment_creator_class; ?> bg-md ml-2"><?php echo $comment_creator_text; ?></span>
                                                    </h5>
                                                    <small class="text-muted"><i class="fe fe-clock"></i> <?php echo get_minutes_ago_time(  get_comment_date('U',$comment->comment_ID)) ?></small>
                                                    <p class="fs-13 mb-0 mt-1 supportnote-body">
                                                        <?php echo trim($comment->comment_content); ?>
                                                    </p>
                                                    <?php if($comment_attachment != "") { ?>
                                                        <div class="row">
                                                            <div class="col-lg-4 col-md-3 mt-2">
                                                                <a href="<?php echo $comment_attachment; ?>" download class="attach-supportfiles">
                                                                    <span class="img-options-remove" data-bs-toggle="remove"><i class="fe fe-x"></i></span>
                                                                    <i class="far fa-file"></i>
                                                                    <div class="attach-title">
                                                                        
                                                                            <?php echo esc_html( basename( esc_html( $comment_attachment ) ) ); ?>
                                            
                                                                     </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <?php if( $diff_in_minutes <= 15 && $ticket_status == 'open' && $comment->user_id == get_current_user_id()) { ?>
                                                    <div class="ms-auto mt-2 mt-md-0">
                                                        <span class="action-btns edit-comment-button supportnote-icon" data-comment-id="<?php echo $comment->comment_ID; ?>" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Edit"><i
                                                                class="fe fe-edit text-primary fs-16"></i></span>
                                                    </div>
                                                <?php } ?>
                                                <?php if( is_user_admin_or_in_array(array()) ) { ?>
                                                    <div class="ms-auto mt-2 mt-md-0">
                                                        <span class="action-btns delete-comment-button supportnote-icon" data-comment-id="<?php echo $comment->comment_ID; ?>" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Delete"><i
                                                                class="fe fe-trash text-danger fs-16"></i></span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                            <?php 
                                    endforeach;
                            ?>
                        </div>
                        <?php  endif; ?>
                        <?php if( $ticket_status == 'open') : ?>
                        <div class="card custom-card">
                            <div class="card-header border-0">
                                <h4 class="card-title">
                                    <i class="fe fe-send"></i>
                                    <?php echo __('Reply','caveni-io'); ?></h4>
                            </div>
                            <form class="add-ticket-reply-container" id="add-ticket-reply-form"
                                method="post" enctype="multipart/form-data" action="">
                                <div class="card-body">
                                
                                <?php wp_nonce_field( 'reply_ticket_nonce_action', 'reply_ticket_nonce' ); ?>
                                    <div id="reply-text-area"></div>
                                    <div class="form-group mt-3">
                                        <label id="form-label" class="form-label"><?php echo __('Upload File','caveni-io'); ?></label>
                                        <input class="form-control" name="ticket_comment_file" type="file">
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="alert d-none m-3" id="ticket_confirmation_alert" role="alert"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-12">
                                            <input type="hidden" name="action" value="caveni_io_reply_ticket_action" />
                                            <input type="hidden" name="ticket_id" value="<?php echo $ticket->ID; ?>" />
                                            <input type="hidden" name="ticket_comment_id" value="" />
                                            <button id="add-ticket-reply" type="button" class="btn">
                                                <span class="spinner-display d-none">
                                                    <i class='fa fa-circle-o-notch fa-spin'></i>  
                                                    <?php echo __('SUBMITTING  REPLY','caveni-io'); ?>
                                                </span>
                                                <span class="button-text">
                                                    <?php echo __('SUBMIT REPLY','caveni-io'); ?>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                    
                                </div>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-xl-4 mb-4 mb-xl-0">
                        <div class="position-sticky top-0">
                            <div class="card custom-card border-0 overflow-hidden ticket-information-block pb-0">
                                <div class="card-header justify-content-between">
                                    <h4 class="card-title">
                                        <i class="fe fe-info"></i>
                                        <?php echo __('Ticket Information','caveni-io'); ?>
                                    </h4>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-borderless ticket-information-table">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <span class="w-50"><?php echo __('Ticket ID','caveni-io'); ?></span>
                                                    </td>
                                                    <td>:</td>
                                                    <td>
                                                        <span class="fw-semibold">#<?php echo $ticket->ID; ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="w-50"><?php echo __('Ticket User','caveni-io'); ?></span>
                                                    </td>
                                                    <td>:</td>
                                                    <td>
                                                        <span class="fw-semibold"><?php the_author_meta( 'display_name' , $author_id ); ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="w-50"><?php echo __('Ticket Category','caveni-io'); ?></span>
                                                    </td>
                                                    <td>:</td>
                                                    <td>
                                                        <span class="fw-semibold">
                                                            <?php
                                                                $category_detail = get_the_terms( $ticket, CAVENI_IO_POST_TAXONOMY);
                                                                if($category_detail){
                                                                    foreach($category_detail as $cd){
                                                                        echo $cd->name;
                                                                    } 
                                                                }
                                                            ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="w-50"><?php echo __('Assigned ','caveni-io'); ?></span>
                                                    </td>
                                                    <td>:</td>
                                                    <td>
                                                        <span class="fw-semibold"><?php the_author_meta( 'display_name' , 1 ); ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="w-50"><?php echo __('Open Date','caveni-io'); ?></span>
                                                    </td>
                                                    <td>:</td>
                                                    <td>
                                                        <span class="fw-semibold"><?php echo get_the_date( 'm/d/Y', $ticket->ID ); ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="w-50"><?php echo __('Status','caveni-io'); ?></span>
                                                    </td>
                                                    <td>:</td>
                                                    <td>
                                                        <span class="badge <?php echo $status_class_tb; ?>"><?php echo strtoupper($ticket_status); ?></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer close-ticket-button-footer ">
                                    <div class="row">
                                        <div class="col-12 pl-0 pr-0 text-center back-to-tickets-button">
                                            
                                            <a href="<?php echo home_url( "/helpdesk" ); ?>"
                                                class="btn btn-primary btn-w-md btn-wave"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="BACK TO TICKETS">
                                                <?php echo __('BACK TO TICKETS','caveni-io'); ?> 
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card custom-card border-0 overflow-hidden">
                                <div class="card-header justify-content-between">
                                    <h4 class="card-title">
                                        <i class="fe fe-users"></i>
                                        <?php echo __('Ticket Details','caveni-io'); ?>
                                    </h4>
                                </div>
                                <div class="card-body p-0">
                                    <?php
                                        $support_admin_id = get_support_amdin();
                                    ?>
                                    <!-- Start::app-sidebar -->
                                    <aside class="support-sidebar">
                                        <div class="profile-pic">
                                            <div class="profile-pic-img">
                                                <span class="bg-success dots" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title=""
                                                    data-original-title="online"
                                                    data-bs-original-title=""></span>
                                                <div class="avatar avatar-xxl avatar-rounded">
                                                    <?php echo get_avatar($support_admin_id,80); ?>
                                                </div>
                                            </div>
                                            <small class="text-muted "><?php echo __('Assigned To','caveni-io'); ?></small>
                                            <a href="javascript:void(0);" class="text-dark">
                                                <h5 class="mt-1 mb-1 fw-semibold2"><?php the_author_meta( 'display_name' , $support_admin_id ); ?></h5>
                                            </a>
                                        </div>
                                        <div class="profile-pic">
                                            <div class="profile-pic-img">
                                                <span class="bg-success dots" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title=""
                                                    data-original-title="online"
                                                    data-bs-original-title=""></span>
                                                <div class="avatar avatar-xxl avatar-rounded">
                                                    <?php echo get_avatar( $author_id, 80 ); ?>
                                                </div>
                                            </div>
                                            <small class="text-muted "><?php echo __('Created By','caveni-io'); ?></small>
                                            <a href="javascript:void(0);" class="text-dark">
                                                <h5 class="mt-1 mb-1 fw-semibold2"><?php the_author_meta( 'display_name' , $author_id ); ?></h5>
                                            </a>
                                        </div>

                                    </aside>
                                    <!-- End::app-sidebar -->
                                </div>
                                <div class="card-footer  close-ticket-button-footer ">
                                <div class="row">
                                    <div class="col-12 pl-0 pr-0">
                                        <?php if( $ticket_status == 'open') : ?>
                                            <form class="close-ticket-container" id="close-ticket-form" method="post">
                                                <?php wp_nonce_field( 'close_ticket_nonce_action', 'close_ticket_nonce' ); ?>
                                                <input type="hidden" name="action" value="caveni_io_close_ticket_action" />
                                                <input type="hidden" name="close_ticket_id" value="<?php echo $ticket->ID; ?>" />
                                                <div class="d-grid text-center close-ticket-button-container">
                                                    <button id="close-ticket-button" type="button" class="btn btn-primary">
                                                        <span class="spinner-display d-none">
                                                            <i class='fa fa-circle-o-notch fa-spin'></i>  
                                                            <?php echo __('CLOSING TICKET','caveni-io'); ?>
                                                        </span>
                                                        <span class="button-text">
                                                            <?php echo __('CLOSE TICKET','caveni-io'); ?>
                                                        </span>
                                                    </button>
                                                </div>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <div class="col-12 mt-1 pl-0 pr-0">
                                    <?php if( is_user_admin_or_in_array(array())) : ?>
                                        <form class="delete-ticket-container" id="delete-ticket-form" method="post">
                                            <?php wp_nonce_field( 'delete_ticket_nonce_action', 'delete_ticket_nonce' ); ?>
                                            <input type="hidden" name="action" value="caveni_io_delete_ticket_action" />
                                            <input type="hidden" name="delete_ticket_id" value="<?php echo $ticket->ID; ?>" />
                                            <div class="d-grid text-center delete-ticket-button-container">
                                                <button id="delete-ticket-button" type="button" class="btn btn-danger">
                                                    <span class="spinner-display d-none">
                                                        <i class='fa fa-circle-o-notch fa-spin'></i>  
                                                        <?php echo __('DELETING TICKET','caveni-io'); ?>
                                                    </span>
                                                    <span class="button-text">
                                                        <?php echo __('DELETE TICKET','caveni-io'); ?>
                                                    </span>
                                                </button>
                                            </div>
                                            
                                        </form>
                                    <?php endif; ?>
                                    </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="alert d-none m-3" id="close_ticket_confirmation_alert" role="alert"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <!--Div-->
</div>
<!-- End::app-content -->

</div>