$(function(){
    if(window.localStorage)
        window.localStorage.clear();

    $('#btnLogin').bind('click', function(){
    	var user_name = $('#user_name').val();
    	var pwd = hex_md5($('#pwd').val());

    	sendPostData({username:user_name, pwd:pwd, client_type:4}, ApiUrl+"cashier/login", function(result){

    		if (result.code == 'SUCCESS') {
                save_json_tolocal(result.data.key, result.data);
                save_string_tolocal('number9streetkey', result.data.key);
                save_user_data_to_local('token', result.data.token);

                var serverIp = result.data.msg_server_ip;
                var serverPort = result.data.msg_server_port;
                var initMsg = '{\"mrid\":\"mrid'+result.data.admin_id+'\"}';
                try{
                    javascript:window.external.init(serverIp, serverPort, initMsg);
                }catch(err){
                    //alert(err);
                }
                location.href = CashierSiteUrl;
			}

    	});
    });

});