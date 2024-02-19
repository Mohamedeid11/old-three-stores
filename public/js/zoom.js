(function(){

	console.log('checkSystemRequirements');
	console.log(JSON.stringify(ZoomMtg.checkSystemRequirements()));

// it's option if you want to chenge the jssdk dependency link resources.
// ZoomMtg.setZoomJSLib('https://dmogdx0jrul3u.cloudfront.net/1.5.0/lib', '/av'); // CDN version default 
// ZoomMtg.setZoomJSLib('http://localhost:9999/node_modules/zoomus-jssdk/dist/lib', '/av'); // Local version default

    ZoomMtg.preLoadWasm();

    ZoomMtg.prepareJssdk();
    
    var API_KEY = '8J3Mmn7jQdmAdChyFD5UNw';

    /**
     * NEVER PUT YOUR ACTUAL API SECRET IN CLIENT SIDE CODE, THIS IS JUST FOR QUICK PROTOTYPING
     * The below generateSignature should be done server side as not to expose your api secret in public
     * You can find an eaxmple in here: https://marketplace.zoom.us/docs/sdk/native-sdks/Web-Client-SDK/tutorial/generate-signature
     */
    var API_SECRET = 'ywwSYCfflXF2dKRbbqyDFa7fxX0ru7BZt2xu';


    document.getElementById('join_meeting').addEventListener('click', function(e){
        e.preventDefault();

        if(!this.form.checkValidity()){
            alert("Enter Name and Meeting Number");
            return false;
        }

        var meetConfig = {
            apiKey: API_KEY,
            apiSecret: API_SECRET,
            meetingNumber: parseInt(document.getElementById('meeting_number').value),
            userName: document.getElementById('display_name').value,
            passWord: "",
            leaveUrl: "https://zoom.us",
            role: 0
        };


        var signature = ZoomMtg.generateSignature({
            meetingNumber: meetConfig.meetingNumber,
            apiKey: meetConfig.apiKey,
            apiSecret: meetConfig.apiSecret,
            role: meetConfig.role,
            success: function(res){
                console.log(res.result);
            }
        });

        ZoomMtg.init({
            leaveUrl: 'http://www.zoom.us',
            isSupportAV: "TRUE",
            success: function () {
                ZoomMtg.join(
                    {
                        topic: "TEST",
                        type: "1",
                        start_time: "2019-08-06 11:00:00",
                        duration: "7200",
                        timezone: "Africa/Cairo",
                        password: "",
                        agenda: "",
                        recurrence: {
                          type: "integer",
                          repeat_interval: "integer",
                          weekly_days: "integer",
                          monthly_day: "integer",
                          monthly_week: "integer",
                          monthly_week_day: "integer",
                          end_times: "integer",
                          end_date_time: "2019-08-06 15:00:00"
                        },
                        settings: {
                          host_video: "TRUE",
                          participant_video: "TRUE",
                          cn_meeting: "TRUE",
                          in_meeting: "boolean",
                          join_before_host: "TRUE",
                          mute_upon_entry: "TRUE",
                          watermark: "TRUE",
                          use_pmi: "TRUE",
                          approval_type: "integer",
                          registration_type: "integer",
                          audio: "string",
                          auto_recording: "string",
                          enforce_login: "boolean",
                          enforce_login_domains: "string",
                          alternative_hosts: "string"
                        }
                      }
                );
            },
            error: function(res) {
                console.log(res);
            }
        });

    });

})();