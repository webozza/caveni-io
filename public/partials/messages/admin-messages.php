<div class="cover-image sptb section-bg ci-admin-screen ci-message-screen">

    <div class="main-container">
        <div class="row">
            <div class="col-xl-8">

                <div class="card mb-3 border-0 rounded-0 bg-white chat-card">
                    <div class="card-head box-shadow bg-white d-flex align-items-center justify-content-between p-3">
                        <span class="chater-info"> <img src="<?php echo $userProfile; ?>" class="rounded-circle user chater-avtar" width="44" height="44" alt="user" />
                            <span data-name="<?php echo $company_username; ?>"><?php echo $company_username; ?> Chat</span></span>
                    </div>
                    <div class="card-body p-3">
                        <div class="chat-list">

                            <?php
                            // Check if any messages were found
                            if ($messages) {
                                $messages = array_reverse($messages);
                                // pre($messages);
                                $lastDate = '';
                                foreach ($messages as $x => $message) {
                                    $even = $current_user_id == $message['sender_id'] ? 'even' : '';

                                    if ($message['separator'] != $lastDate) {
                                        echo '<div class="ci-separator" data-date="' . $message['date_group'] . '"><span>' . $message['separator'] . '</span></div>';
                                        $lastDate = $message['separator'];
                                    }
                                    echo '<div class="chat-item mb-3 ' . $even . '" data-id="' . $message['id'] . '" data-date="' . $message['date_group'] . '">';
                                    echo '<div class="d-flex justify-content-between ci-message-info">';
                                    if (!$even) {
                                        echo '<div class="chater-img"><img src="' . $message['sender_img'] . '" class="rounded-circle user" width="44" height="44" alt="user" /></div>';
                                        echo '<span class="ci-sender-name">' . $message['sender_name'] . '</span>';
                                    }
                                    echo '<span class="ci-msg-time">' . esc_html($message['date']) . '</span>';
                                    echo '</div>';
                                    echo '<div class="message mt-1"><div class="inner">';
                                    if ($message['attachment']) {
                                        echo '<a href="' . esc_url($message['attachment']['url']) . '" class="btn btn-link p-0 ci-attachment-file" download data-id="' . $message['attachment']['id'] . '">';
                                        if ($message['attachment']['type'] == 'image') {
                                            echo '<img src="' . $message['attachment']['url'] . '" class="rounded-square" width="200" height="200" alt="attachment" />';
                                        } else {
                                            echo '<i class="fas fa-file"></i>';
                                        }
                                        echo $message['attachment']['name'] . '</a>';
                                    }
                                    echo  esc_html($message['message']) . '</div>';
                                    echo '<div class="ci-msg-options">
                                            <button class="ci-options-toggle card-dot-btn lh-1"><i class="fas fa-ellipsis-v"></i></button>
                                            <ul class="ci-options-menu">
                                                <li>
                                                    <a class="ci-options-item ci-msg-delete" href="javascript:void(0);" data-message="' . $message['id'] . '"><i class="fas fa-trash"></i> Delete </a>
                                                </li>
                                            </ul>
                                        </div>';
                                    echo '</div>';
                                    echo '</div>'; // End chat-item
                                }
                            } else {
                                echo '<p class="ci-no-message">Send a new message to start the chat.</p>'; // Message if no results
                            }
                            ?>
                        </div> <!-- End chat-list -->
                        <form id="ci-message-form" autocomplete="off">
                            <div class="write-your-message position-relative mt-3">
                                <textarea class="input-message d-block w-100 text-black fs-14" placeholder="Type your message here" name="message" autocomplete="off"></textarea>
                            </div>
                            <div class="buttons-list mt-2">
                                <div class="ci-file-uploader">
                                    <label for="ci-attachment"><i class="fas fa-link"></i></label>
                                    <input type="file" name="ci_attachment" id="ci-attachment">
                                </div>
                                <button type="submit" class="border-0 active">
                                    <i class="fas fa-paper-plane"></i> <!-- Font Awesome Send Icon -->
                                </button>
                                <input type="hidden" name="action" value="caveni_io_send_message">
                                <input type="hidden" name="caveni_nonce" value="<?php echo wp_create_nonce('message_send_nonce_action') ?>">
                                <input type="hidden" name="group_id" value="<?php echo $recentUser->group_id; ?>">
                                <input type="hidden" name="receiver" value="<?php echo $recentUser->user_id; ?>">
                                <input type="hidden" name="sender" value="<?php echo $current_user_id; ?>">
                            </div>
                        </form>
                        <div class="ci-loader" style="display: none;">
                            <img src="<?php echo CAVENI_IO_URL . '/public/images/loader.svg'; ?>" alt="loader">
                        </div>
                    </div> <!-- End card-body -->
                </div>

            </div>
            <div class="col-xl-4">
                <div class="ci-search-wrap">
                    <div class="ci-search-input">
                        <input type="text" name="ci-search" id="ci-search" class="form-control ci-search" placeholder="Search Messages">
                        <span class="ci-searching" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </div>
                    <div class="ci-search-result"></div>
                </div>
                <div class="tabs">
                    <ul class="tab-list">
                        <li class="tab ci-tabs active" data-tab="chat">CHATS</li>
                        <li class="tab ci-tabs" data-tab="clients">CLIENTS</li>
                    </ul>

                    <div class="tab-content">
                        <div id="chat" class="tab-pane active">
                            <div class="chat-users-list">
                                <?php
                                if (!empty($recentChats)) {
                                    foreach ($recentChats as $x => $chat) {
                                        $active = '';
                                        if ($x == 0) $active = 'active';
                                ?>
                                        <div class="single-user-item position-relative recents ci-load-user <?php echo $active; ?>" data-user="<?php echo $chat['sender_id']; ?>" data-name="<?php echo $chat['name']; ?>" data-group_id="<?php echo $chat['group_id']; ?>" data-type="chat">
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $chat['profileImg']; ?>" width="44" height="44" class="rounded-circle" alt="user">
                                                <div class="ms-12">
                                                    <span class="title">
                                                        <?php echo $chat['name']; ?>
                                                        <span class="ci-time-ago"><?php echo $chat['time']; ?></span>
                                                    </span>
                                                    <span class="d-block text-black ci-short-msg"> <?php echo $chat['message']; ?> </span>
                                                </div>
                                            </div>
                                            <?php if ($chat['unreads']) { ?>
                                                <span class="rounded-circle ci-unread-msg show" data-count="<?php echo $chat['unreads']; ?>"><?php echo $chat['unreads']; ?></span>
                                            <?php } else { ?>
                                                <span class="rounded-circle ci-unread-msg" data-count="0"></span>
                                            <?php } ?>

                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div id="clients" class="tab-pane">

                            <?php
                            if (!empty($clients)) {

                                foreach ($clients as $client_id => $client) { ?>


                                    <div class="single-user-item position-relative ci-load-user" data-user="<?php echo $client_id; ?>" data-name="<?php echo $client['name']; ?>" data-group_id="<?php echo $client['group_id']; ?>" data-type="client">
                                        <div class="d-flex align-items-center">
                                            <img src=" <?php echo $client['profileImg']; ?>" width="44" height="44" class="rounded-circle" alt="user">
                                            <div class="ms-12">
                                                <span class="title d-block text-black fs-md-15 fs-lg-16 fw-medium">
                                                    <?php echo $client['name']; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                            <?php


                                }
                            } ?>

                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs and panes
                tabs.forEach(t => t.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));

                // Add active class to the clicked tab and corresponding pane
                this.classList.add('active');
                const activePane = document.getElementById(this.dataset.tab);
                activePane.classList.add('active');
            });
        });
    });
</script>