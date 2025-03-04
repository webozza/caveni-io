<div class="cover-image sptb section-bg ci-client-screen ci-message-screen">
    <div class="main-container">
        <div class="row">
            <div class="col-xl-8">
                <div class="card mb-3 border-0 rounded-0 bg-white chat-card">
                    <div class="card-head box-shadow bg-white d-flex align-items-center justify-content-between p-3">
                        <span class="chater-info">
                            <img src="<?php echo $userProfile; ?>" class="rounded-circle user chater-avtar" width="44" height="44" alt="user" />
                            <span data-name="<?php echo $admin_name; ?>"><?php echo $admin_name; ?></span>
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <div class="chat-list">

                            <?php
                            // Check if any messages were found
                            if ($messages) {
                                $messages = array_reverse($messages);
                                // pre($messages, 1);
                                $lastDate = '';
                                foreach ($messages as $message) {
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
                                    echo '</div>';
                                    echo '</div>'; // End chat-item
                                }
                            } else {
                                echo '<p class="ci-no-message">Send a new message to start the chat.</p>'; // Message if no results
                            }
                            ?>
                            <!-- Input Message Area -->
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
                                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                                <input type="hidden" name="receiver" value="<?php echo $adminStaff; ?>">
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
                        <input type="text" name="ci-search" id="ci-search-message" class="form-control ci-search" placeholder="Search Messages">
                        <span class="ci-searching" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </div>
                    <div class="ci-search-result"></div>
                </div>
                <div class="tabs">
                    <div id="chat" class="tab-pane active">
                        <div class="chat-admin-list">
                            <div class="single-user-item position-relative recents ci-admin-chat" data-user="<?php echo $adminStaff; ?>" data-name="<?php echo $admin_name; ?>" data-group_id="<?php echo $group_id; ?>" data-type="chat" data-client="<?php echo $client_company; ?>" data-clientavtar="<?php echo $clientProfile; ?>">
                                <div class="d-flex align-items-center ci-admin-chat-inner">
                                    <img src="<?php echo $userProfile; ?>" width="44" height="44" class="rounded-circle" alt="user">
                                    <div class="ms-12">
                                        <span class="title">
                                            <?php echo $admin_name; ?>
                                            <span class="ci-time-ago"><?php if ($recentChats) {
                                                                            echo $recentChats->created_at;
                                                                        }
                                                                        ?></span>
                                        </span>
                                        <span class="d-block text-black ci-short-msg"> <?php if ($recentChats) {
                                                                                            echo $recentChats->message;
                                                                                        }
                                                                                        ?> </span>
                                    </div>
                                </div>
                                <span class="rounded-circle ci-unread-msg" data-count="0"></span>

                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>