<!-- Start::app-content -->
<div class="main-content landing-main px-0">

    <!--Section-->
    <section>
        <div class="cover-image sptb section-bg">
            <div class="main-container">
                <div class="row">
                    <div class="col-xl-12 dashboard-tickets-by-status">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-12">
                                <div class="card custom-card">
                                    
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-8">
                                                    <div class="mt-0 text-start">
                                                        <span class="fs-16 fw-semibold"><?php echo __('Total Tickets','caveni-io'); ?></span>
                                                        <h3 class="mb-0 mt-1 text-primary fs-25"><?php echo $all_tickets_count;  ?></h3>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="icon1 bg-primary-transparent my-auto float-end">
                                                        <i class="las la-ticket-alt"></i> </div>
                                                </div>
                                            </div>
                                        </div>
                                
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-12">
                                <div class="card custom-card">
                                    
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-8">
                                                    <div class="mt-0 text-start">
                                                        <span class="fs-16 fw-semibold"><?php echo __('Active Tickets','caveni-io'); ?></span>
                                                        <h3 class="mb-0 mt-1 text-success fs-25"><?php echo $open_tickets_count;  ?></h3>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="icon1 bg-success-transparent my-auto float-end">
                                                        <i class="ri-ticket-2-line"></i> </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-12">
                                <div class="card custom-card">
                                    
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-8">
                                                    <div class="mt-0 text-start">
                                                        <span class="fs-16 fw-semibold"><?php echo __('Closed Tickets','caveni-io'); ?></span>
                                                        <h3 class="mb-0 mt-1 text-danger fs-25"><?php echo $close_tickets_count;  ?></h3>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="icon1 bg-danger-transparent my-auto  float-end">
                                                        <i class="ri-coupon-2-line"></i> </div>
                                                </div>
                                            </div>
                                        </div>
                                    
                                </div>
                            </div>
                        </div>
                        

                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 ticket-summary-block">
                                <div class="card custom-card mb-0">
                                    <div class="card-header border-0">
                                        <div class="row align-items-center flex-grow-1">
                                            <div class="col">
                                                
                                                    <h4 class="card-title">
                                                        <i class="fe fe-list"></i>
                                                        <?php echo __('Tickets Summary','caveni-io'); ?>
                                                    </h4>
                                                
                                            </div>
                                            <div class="col col-auto add-support-ticket-button">
                                                
                                                    <a href="<?php echo home_url( $wp->request )."/?_ticket_action=add"; ?>"
                                                        class="btn btn-primary btn-w-md btn-wave"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="ADD NEW TICKET">
                                                        <?php echo __('ADD SUPPORT TICKET','caveni-io'); ?> 
                                                    </a>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body tickets-home-page">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="alert d-none m-3" id="deleted_ticket_confirmation_alert" role="alert"></div>
                                            </div>
                                        </div>
                                        <div class="table-responsive tickets-table-area">
                                            <table
                                                class="table table-vcenter text-nowrap table-bordered border-bottom"
                                                id="supportticket-dash" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0">#<?php echo __('ID','caveni-io'); ?></th>
                                                        <th class="border-bottom-0"><?php echo __('Title','caveni-io'); ?></th>
                                                        <th class="border-bottom-0"><?php echo __('Category','caveni-io'); ?></th>
                                                        <th class="border-bottom-0"><?php echo __('Date','caveni-io'); ?></th>
                                                        <?php if(is_user_admin_or_in_array(array())) : ?>
                                                            <th class="border-bottom-0"><?php echo __('Client','caveni-io'); ?></th>
                                                        <?php endif; ?>
                                                        <th class="border-bottom-0"><?php echo __('Status','caveni-io'); ?></th>
                                                        <th class="border-bottom-0"><?php echo __('Actions','caveni-io'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php 
                                                if($tickets):
                                                    foreach($tickets as $ticket) :
                                                        $ticket_status = get_post_meta($ticket->ID,'_caveni_ticket_status',true);
                                                        $ticket_author = get_the_author_meta('display_name',$ticket->post_author);
                                                        $status_class = 'bg-success';
                                                        if($ticket_status == 'closed'){
                                                            $status_class = 'bg-danger';
                                                        }
                                                ?>
                                                        <tr>
                                                            <td>#<?php echo $ticket->ID; ?></td>
                                                            <td><a href="<?php echo home_url( $wp->request )."/?_ticket_action=view&_ticket_id=".$ticket->ID; ?>"><?php echo $ticket->post_title; ?></a>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $category_detail = get_the_terms( $ticket, CAVENI_IO_POST_TAXONOMY);
                                                                if($category_detail){
                                                                   // echo "<pre>";print_r($category_detail);echo "</pre>";
                                                                    foreach($category_detail as $cd){
                                                                        echo $cd->name;
                                                                    } 
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                    echo get_the_date( 'd-m-Y', $ticket->ID );
                                                                ?>
                                                        </td>
                                                            <?php if(is_user_admin_or_in_array(array())) : ?>
                                                                <td><?php echo $ticket_author; ?></td>
                                                            <?php endif; ?>
                                                            <td><span class="badge <?php echo $status_class; ?>"><?php echo strtoupper($ticket_status); ?></span></td>
                                                            <td>
                                                                <div class="d-flex">
                                                                    
                                                                    <a href="<?php echo home_url( $wp->request )."/?_ticket_action=view&_ticket_id=".$ticket->ID; ?>"
                                                                        class="action-btns1"
                                                                        data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        title="View Ticket"><i
                                                                            class="fe fe-eye text-primary"></i></a>
                                                                    <?php if(is_user_admin_or_in_array(array())) : ?>
                                                                        <a href="javascript:void(0);"
                                                                            class="action-btns1 delete-ticket-by-admin"
                                                                            data-bs-toggle="tooltip"
                                                                            data-bs-placement="top"

                                                                            title="Delete Ticket" data-ticket-id="<?php echo $ticket->ID; ?>"><i
                                                                                class="fe fe-trash-2 text-danger"></i></a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                <?php 
                                                    endforeach;
                                                endif;
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Section-->

</div>
<!-- End::app-content -->