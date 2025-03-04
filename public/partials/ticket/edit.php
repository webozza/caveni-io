<!-- Start::app-content -->
<div class="main-content landing-main px-0">
    <div class="main-container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card mb-0">
                        <div class="card-header  border-0">
                            <div class="row align-items-center flex-grow-1">
                                <div class="col">
                                    <h4 class="card-title">
                                    <i class="fe fe-plus-circle"></i>
                                    <?php echo ($edited) ? __('Edit Ticket','caveni-io'): __('New Ticket','caveni-io'); ?>
                                    </h4>
                                </div>
                                <div class="col col-auto back-to-tickets-button">
                                                
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
                        
                        <form class="caveni-create-ticket-container" id="caveni-create-ticket-form"
                        method="post" enctype="multipart/form-data" action="">
                            <?php wp_nonce_field( 'create_ticket_nonce_action', 'create_ticket_nonce' ); ?>
                            <div class="card-body">
                                <div class="form-group mt-3">
                                    
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2"><?php echo __('Ticket Subject','caveni-io'); ?></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="ticket_subject" class="form-control" placeholder="Name" value="<?php echo ($edited) ? $ticket->post_title : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    
                                    <div class="row">
                                        
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2"><?php echo __('Ticket Category','caveni-io'); ?></label>
                                        </div>
                                        <div class="col-md-9">
                                            <select class="form-control custom-select ticket_category_select" name="ticket_category">
                                                <option value=""><?php echo __('Select Ticket Category','caveni-io'); ?></option>
                                                <?php foreach($ticket_terms as $term) : ?>
                                                    <option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2"><?php echo __('Ticket Description','caveni-io'); ?></label>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="create-ticket">
                                                <div id="reply-text-area" class=""></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2"><?php echo __('Upload File','caveni-io'); ?></label>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <input class="form-control" name="ticket_file" type="file">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row flex-grow-1">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-9 add-support-ticket-button">
                                    <input type="hidden" name="action" value="caveni_io_create_ticket_action" />
                                    <button id="caveni-ticket-create" type="button" class="btn btn-primary btn-lg">
                                        <span class="spinner-display d-none">
                                            <i class='fa fa-circle-o-notch fa-spin'></i>  
                                            <?php echo __('CREATING TICKET','caveni-io'); ?>
                                        </span>
                                        <span class="button-text">
                                            <?php echo __('CREATE TICKET','caveni-io'); ?>
                                        </span>
                                    </button>
                                    
                                </div>
                                <div class="row flex-grow-1">
                                    <div class="col-md-12" >
                                        <div class="alert d-none m-3" id="ticket_confirmation_alert" role="alert"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>