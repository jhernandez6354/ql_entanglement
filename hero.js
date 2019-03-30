// Convert the token into a player ID.
$(document).ready(function(){
    $("button").click(function(data){
    var playerid =null
    var token = document.getElementById("token").value;
    var cors_api_url = 'https://cors-anywhere.herokuapp.com/';
    let reqHeader = new Headers();
    reqHeader.append('token', token);
    reqHeader.append("Origin", ".");
    reqHeader.append("Content-Type", "application/x-www-form-urlencoded");
    reqHeader.append("Access-Control-Allow-Origin", "*");
    let initObject = {
        method: 'GET',
        headers: reqHeader,
    };
    
    if (validation()) // Calling validation function
    {
        data.preventDefault();
        async function fetch_token() {
            try {
                await fetch(cors_api_url + `http://149.56.27.225/client/init/`,initObject)
                .then(function(response) {
                  return response.json();
                })
                .then(function(myJson) {
                    result = JSON.stringify(myJson);
                    playerid = JSON.parse(result)['data']['hero']['id']; //This will be used in future requests
                    if (playerid){
                        document.getElementById('player_name').innerHTML = "Hero: " + JSON.parse(result)['data']['hero']['name'];
                        document.getElementById('player_level').innerHTML = "Level: " + JSON.parse(result)['data']['hero']['level'];
                    } else {
                        alert("Unable to find hero.\nPlease refer to the home page to identify how to pull your token.");
                    }
                });
            } catch(error) {
                console.log(error); // TypeError: failed to fetch
            }
            return playerid;
        }

        async function get_Gear() {
            try {
                await fetch(cors_api_url + `http://149.56.27.225/user/getprofile/` + '/?hero_id=' + playerid ,initObject)
                .then(await function(response) {
                  return response.json();
                })
                .then(function(myJson) {
                    result = JSON.stringify(myJson);
                        jQuery.ajax({
                        type: "POST",
                        url: 'GetHero.php',
                        dataType: 'json',
                        data: {functionname: 'heroGear', arguments: JSON.parse(result)['data']['profile_items_list']},
                    
                        success: function (obj, textstatus) {
                            if( !('error' in obj) ) {
                                yourVariable = obj.result;
                                document.getElementById('e_helm').innerHTML = "<br>Helmet: " + yourVariable.helm.image + "<div class=\"tooltip\">" + yourVariable.helm.name + "<span class=\"tootiptext\">"+ " Upgrade: " + yourVariable.helm.upgrade + "&#13 Boost: " + yourVariable.helm.boost + "</span></div><br>";
                                document.getElementById('e_chest').innerHTML = "Armor: " + yourVariable.chest.image+ "<div class=\"tooltip\">" + yourVariable.chest.name + "<span class=\"tootiptext\">"+ " Upgrade: " + yourVariable.chest.upgrade + "&#13 Boost: " + yourVariable.chest.boost + "</span></div><br>";
                                document.getElementById('e_hand').innerHTML = "Gloves: " + yourVariable.hand.image+ "<div class=\"tooltip\">" + yourVariable.hand.name + "<span class=\"tootiptext\">"+ " Upgrade: " + yourVariable.hand.upgrade + "&#13 Boost: " + yourVariable.hand.boost + "</span></div><br>";
                                document.getElementById('e_feet').innerHTML = "Boots: " + yourVariable.feet.image+ "<div class=\"tooltip\">" + yourVariable.feet.name + "<span class=\"tootiptext\">"+ " Upgrade: " + yourVariable.feet.upgrade + "&#13 Boost: " + yourVariable.feet.boost + "</span></div><br>";
                                document.getElementById('e_neck').innerHTML = "Necklace: " + yourVariable.neck.image+ "<div class=\"tooltip\">" + yourVariable.neck.name + "<span class=\"tootiptext\">"+ " Upgrade: " + yourVariable.neck.upgrade + "&#13 Boost: " + yourVariable.neck.boost + "</span></div><br>";
                                document.getElementById('e_ring').innerHTML = "Ring: " + yourVariable.ring.image+ "<div class=\"tooltip\">" + yourVariable.ring.name + "<span class=\"tootiptext\">"+ " Upgrade: " + yourVariable.ring.upgrade + "&#13 Boost: " + yourVariable.ring.boost + "</span></div><br>";
                                document.getElementById('e_idol').innerHTML = "Talisman: " + yourVariable.idol.image+ "<div class=\"tooltip\">" + yourVariable.idol.name + "<span class=\"tootiptext\">"+ " Upgrade: " + yourVariable.idol.upgrade + "&#13 Boost: " + yourVariable.idol.boost + "</span></div><br>";
                                document.getElementById('e_mh').innerHTML = "Main Hand: " + yourVariable.mh.image+ "<div class=\"tooltip\">" + yourVariable.mh.name + "<span class=\"tootiptext\">"+ " Upgrade: " + yourVariable.mh.upgrade + "&#13 Boost: " + yourVariable.mh.boost + "</span></div><br>";
                                document.getElementById('e_oh').innerHTML = "Off Hand: " + yourVariable.oh.image + "<div class=\"tooltip\">" + yourVariable.oh.name + "<span class=\"tootiptext\">"+ " Upgrade: " + yourVariable.oh.upgrade + "&#13 Boost: " + yourVariable.oh.boost + "</span></div><br>";
                            }
                            else {
                                console.log(obj.error);
                            }
                        }
                    });
                });
            } catch(error) {
                console.log(error); // TypeError: failed to fetch
            }
        }
        var check = function() {
            if (playerid){ //Make sure the player id is valid before pulling the rest. 
                get_Gear();
            } else {
                setTimeout(check, 1000);
            }
        }
        fetch_token();
        check();
    }
    // Field validation Function.
    function validation() {
        var token = document.getElementById("token").value;
        //var tokenReg = /^([w-.]+@([w-]+.)+[w-]{2,4})?$/;
        if (token === '') {
            alert("Please enter a token before clicking the button.");
            return false;
        //} else if (!(token).match(tokenReg)) {
            //alert("Invalid Token...");
            //return false;
        } else {
            return true;
        }
    }
    });
});