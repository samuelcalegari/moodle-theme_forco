<?php
    $avatar = isset($_GET['avatar']) ? $_GET['avatar'] :  '';
    $username = isset($_GET['username']) ? $_GET['username'] :  '';
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        html, body { height: 100% }
        body {
            margin: 0;
        }

        #webchat {
            height: 100%;
            width: 100%;
        }
    </style>
</head>
<body>
<div id="webchat" role="main"></div>
<script src="https://cdn.botframework.com/botframework-webchat/latest/webchat.js"></script>
<script>

    (async function() {

        const res = await fetch('https://directline.botframework.com/v3/directline/tokens/generate', {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + '23q0tabd1is.CVjojX8xOP5ZQhQpKh4fBXVa5b_XbEP2eVdz23So7nc'}
        });

        const { token } = await res.json();


        const styleOptions = {
            bubbleBackground: '#fff',
            bubbleTextColor: '#333',
            bubbleFromUserBackground: '#1b78ce',
            bubbleFromUserTextColor: '#fff',
            botAvatarImage: 'https://forco.univ-perp.fr/theme/forco/bot/bot.png',
            userAvatarImage: '<?php echo ($avatar)?>',
            notificationText: '#999',
            timestampColor: '#999',
            backgroundColor: '#eee',
            botAvatarBackgroundColor: '#fff',
            userAvatarBackgroundColor: '#fff',
        };

        const store = window.WebChat.createStore({}, ({ dispatch }) => next => action => {
            //console.log(action);
            if (action.type === 'DIRECT_LINE/CONNECT_FULFILLED') {
                dispatch({
                    type: 'WEB_CHAT/SEND_EVENT',
                    payload: {
                        name: 'webchat/join',
                        value: { language: window.navigator.language, moodleid : '<?php echo ($username)?>' }
                    }
                });
            }

            return next(action);
        });

        window.WebChat.renderWebChat(
            {
                directLine: window.WebChat.createDirectLine({
                    token: token
                }),
                store,
                styleOptions
            },
            document.getElementById('webchat')
        );


    })().catch(err => console.error(err));

</script>
</body>
</html>
