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
                var is_debug = true;
                try{
                    javascript:window.external.init(serverIp, serverPort, initMsg, is_debug);
                }catch(err){
                    alert(err);
                }
                location.href = CashierSiteUrl;
			}

    	});
    });

});


function set_param_web(onoff,papernum,delay,p_names,hotkey,all_prints){
    $('#params').html(onoff+'-papernum:'+papernum+'-delay:'+delay+'-p_names:'+p_names+'--hotkey:'+hotkey+'--all_prints:'+all_prints);

    save_string_tolocal('onoff', onoff);
    save_string_tolocal('papernum', papernum);
    save_string_tolocal('delay', delay);
    save_string_tolocal('p_names', p_names);
    save_string_tolocal('hotkey', hotkey);
    save_string_tolocal('all_prints', all_prints);
}


function loaded(){
    //set_param_web('true',0,0,'58mm Series Printer',0,'Microsoft XPS Document Writer|Microsoft Print to PDF|HP DeskJet 1110 series|GP-5890X|Foxit Reader PDF Printer|Fax|58mm Series Printer');

    javascript:window.external.login_loaded();
}