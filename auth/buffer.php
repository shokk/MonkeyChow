<?
        require_once('../init.php');
        require_once('../includes/Oauth.php');
        require_once('../ncludes/Buffer.php');

        $client_id = BUFFER_APP_ID;
        $client_secret = BUFFER_APP_SECRET;
        $buffer_callback = BUFFER_CALLBACK;
        
        $buffer = new BufferApp($client_id, $client_secret, $buffer_callback);
                
        if (!$buffer->ok) {
                echo '<a href="' . $buffer->get_login_url() . '">Connect to Buffer!</a>';
        } else {
                $profiles = $buffer->go('/profiles');
                        
                if (is_array($profiles)) {
                        foreach ($profiles as $profile) {
                                $buffer->go('/updates/create', array('text' => 'My first status update from bufferapp-php worked!', 'profile_ids[]' => $profile->id));
                        }
                }
        }
?>
