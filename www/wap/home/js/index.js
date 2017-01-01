$(function() {

    

    function getDataByType(type,data){
        for(var i =0 ;i < data.content.length;i++){
            if(data.content[i].type == type){
                return {data:data.content[i].data};
            }
        }
    }



});
