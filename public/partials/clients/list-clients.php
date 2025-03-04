<!-- Start::app-content -->
<!-- <php pre($clients, 1); ?> -->
<?php include_once CAVENI_IO_PATH . 'public/partials/clients/add-client.php';

?>
<div class="main-content landing-main px-0">

    <!--Section-->
    <section>
        <div class="cover-image sptb section-bg">
            <div class="main-container">

                <div class="row">
                    <div class="col-xl-12 dashboard-tickets-by-status">

                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 ticket-summary-block">
                                <div class="card custom-card mb-0">
                                    <div class="custom-header">
                                        <div class="cilent-title"><i
                                                class="fe fe-users text-primary"></i>
                                            <h4><?php echo __('Clients Summary', 'caveni-io'); ?></h4>
                                        </div>
                                        <div class="actions">
                                            <div class="search-grp">
                                                <i class="fe fe-search text-primary"></i>
                                                <input type="search" id="caveni-custom-search" placeholder="Search Clients">
                                            </div>
                                            <a id="cavein-client-add" class="btn btn-primary btn-w-md btn-wave"><?php echo __('ADD CLIENTS', 'caveni-io'); ?></a>
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
                                                id="supportclient-dash" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom-0">#<?php echo __('ID', 'caveni-io'); ?></th>
                                                        <th class="border-bottom-0"><?php echo __('Company', 'caveni-io'); ?></th>
                                                        <th class="border-bottom-0"><?php echo __('Email', 'caveni-io'); ?></th>
                                                        <th class="border-bottom-0"><?php echo __('Website ', 'caveni-io'); ?></th>
                                                        <th class="border-bottom-0"><?php echo __('Roles', 'caveni-io'); ?></th>
                                                        <th class="border-bottom-0"><?php echo __('Actions', 'caveni-io'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($clients):
                                                        foreach ($clients as $client) :
                                                            $user_data = get_userdata($client['ID']);
                                                            $company_username = get_user_meta($client['ID'], 'company', true);
                                                            $user_website = $user_data->user_url;
                                                            $userAvatar = get_buddypress_profile_picture($client['ID'], 50);

                                                    ?>
                                                            <tr>
                                                                <td><?php echo $client['ID']; ?></td>
                                                                <!-- <php echo $ticket->post_title; ?></a> -->
                                                                </td>
                                                                <td>
                                                                    <span class="company-info">
                                                                        <span class="avatar">
                                                                            <img src="<?php echo $userAvatar; ?>"></span><span class="company-name"><?php echo $company_username;
                                                                                                                                                    ?>
                                                                        </span></span>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo $client['email'];
                                                                    ?>
                                                                </td>

                                                                <td><a href="<?php echo $user_website; ?>"><?php echo $user_website; ?></a></td>
                                                                <td><?php echo $client['roles']; ?></span></td>
                                                                <td>
                                                                    <div class="d-flex">

                                                                        <a href="javascript:void(0);"
                                                                            class="action-btns1 caveni-edit-client" data-client-id="<?php echo $client['ID']; ?>"
                                                                            data-bs-toggle="tooltip"
                                                                            data-bs-placement="top"
                                                                            title="View Client"><i
                                                                                class="fe fe-eye text-primary"></i></a>

                                                                        <a href="javascript:void(0);"
                                                                            id=""
                                                                            class="action-btns1 caveni-delete-client"
                                                                            data-bs-toggle="tooltip"
                                                                            data-bs-placement="top"

                                                                            title="Delete Client" data-client-id="<?php echo $client['ID'];; ?>"><i
                                                                                class="fe fe-trash-2 text-danger"></i></a>

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
    <div class="ci-loader ci-loader-list" style="display: none;">
        <img src="<?php echo CAVENI_IO_URL . '/public/images/loader.svg'; ?>" alt="loader">
    </div>
    <!--Section-->

</div>
<!-- End::app-content -->