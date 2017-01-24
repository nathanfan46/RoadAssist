<style scoped>
    .action-link {
        cursor: pointer;
    }

    .m-b-none {
        margin-bottom: 0;
    }

    .request h4 {
        color: white;
    }

    .request > .form-group {
        text-align: left;
    }

    .request label, span {
        color: white;
    }

    div.map-container {
        display: block;
        line-height: 0;
    }
    .map-container img
    {
        max-width: 100%;
        opacity: 100;
    }


</style>
<template>
    <div class="form-group request">
        <h4>Your Information</h4>
        <div class="form-group">
            <label for="name" class="col-form-label">Name</label>
            <div>
                <input type="text" class="form-control" id="name" :value="user.FirstName" v-model="user.FirstName">
            </div>
        </div>
        <div class="form-group">
            <label for="phone" class="col-form-label">Phone</label>
            <div>
                <input type="text" class="form-control" id="phone" :value="user.Phone" v-model="user.Phone">
            </div>
        </div>

        <div class="form-group">
            <label for="email" class="col-form-label">Email</label>
            <div>
                <input type="email" class="form-control" id="email" :value="user.Email" v-model="user.Email">
            </div>
        </div>

        <div class="form-group">
            <label for="location" class="col-form-label">Location</label>
            <div>
                <input type="button" class="btn btn-info btn-xs" id="location" value="Find me" @click="geoFindMe()">
            </div>
            <div class="map-wrapper centered">     
                <span>{{location.message}}</span>
                <div id="geoinfo" class="map-container">
                    <img :src="location.imgsrc" v-if="location.found"/>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="locationnotes" class="col-form-label">Location Notes</label>
            <div>
                <textarea class="form-control" id="locationnotes" rows="3" :value="location.notes" v-model="location.notes"></textarea>
            </div>
        </div>

        <div class="form-group services">
            <button type="button" class="btn btn-primary btn-block" @click="sendrequest()">SUBMIT</button>
        </div>
    </div>

</template>

<script>
    export default {
        data() {
            return {
                user: this.$root.$data.sharedState.user,
                location: {
                    latitude: '',
                    longitude: '',
                    imgsrc: '',
                    message: '',
                    notes: '',
                    found: false
                },
                problem: {
                    code: this.$route.params.code,
                    type: '',
                    raserviceid: ''
                }
            };
        },
        mounted() {
            // console.log('Component mounted.')
            // console.log(this.$root.$data.sharedState);

            if(this.problem.code == 'towing') {
                this.problem.type = 'Towing';
                this.problem.raserviceid = 1;
            } else if(this.problem.code == 'lockout') {
                this.problem.type = 'Lockout';
                this.problem.raserviceid = 2;
            } else if(this.problem.code == 'jumpstart') {
                this.problem.type = 'Jump Start';
                this.problem.raserviceid = 3;
            } else if(this.problem.code == 'fuel') {
                this.problem.type = 'Fuel';
                this.problem.raserviceid = 4;
            } else if(this.problem.code == 'flattire') {
                this.problem.type = 'Flat Tire';
                this.problem.raserviceid = 5;
            } else if(this.problem.code == 'winch') {
                this.problem.type = 'Winch';
                this.problem.raserviceid = 6;
            }
        },

        methods: {
            geoFindMe() {
                var geoinfo = document.getElementById("geoinfo");
                if (!navigator.geolocation){
                    geoinfo.innerHTML = "<p>Geolocation is not supported by your browser</p>";
                    return;
                }

                this.location.message = "Locating…";
                this.location.found = false;

                navigator.geolocation.getCurrentPosition(this.geoSuccess, this.geoerror);
            },

            geoSuccess(position) {
                this.location.latitude = position.coords.latitude;
                this.location.longitude = position.coords.longitude;

                this.location.message = this.location.latitude + "," + this.location.longitude; 

                this.location.imgsrc = "http://maps.googleapis.com/maps/api/staticmap?center=" + this.location.latitude + "," + this.location.longitude + "&zoom=14&size=600x400&markers=color:red|label:A|" + this.location.latitude + "," + this.location.longitude + "&sensor=false&key=AIzaSyDiUuDlNj73HAMVk4KB2tswmVlLlOSAEjs";

                this.location.found = true;
            },

            geoerror() {
                var geoinfo = document.getElementById("geoinfo");

                this.location.message = "Unable to retrieve your location"; 
            },

            sendrequest() {
                var timestamp = new Date().getTime();

                var attachment = {
                    id: 76,
                    object: 'user',
                    filename: 'ProblemDescription_' + timestamp + ".pdf",
                    base64: this.encode("Problem: " + this.problem.type + " <br>")
                };

                console.log('sending request...');

                // this.$http.headers.common['X-Proxy-Url'] = 'api/roadassist/new';

                this.$http.post('/api/tirepos', attachment, {
                    headers : {
                        'X-Proxy-Url' : 'apiv1/attachment'
                    }
                })
                .then(response => {
                    console.log(response);

                    if(response.body.status) {
                        // var attachmentid = response.id;

                        var roadassist = {
                            attachmentid: response.body.id,
                            latitude: this.location.latitude,
                            longitude: this.location.longitude,
                            message: "Request for " + this.problem.type,
                            userid: 76,
                            phone: this.user.Phone,
                            firstname: this.user.FirstName,
                            lastname: 'N/A',
                            email: this.user.Email,
                            locationnotes: this.location.notes,
                            additionalinformation: 'From Web',
                            raserviceid: this.problem.raserviceid,
                            problem: this.problem.type
                        }

                        this.$http.post('/api/tirepos', roadassist, {
                            headers : {
                                'X-Proxy-Url' : 'api/roadassist/new'
                            }
                        })
                        .then(response => {
                            if(response.body.status) {
                                var id = response.body.id;
                                if(id != 0) {
                                    this.$http.get('/api/tirepos', {
                                        headers : {
                                            'X-Proxy-Url' : 'api/roadassist/idtohash/' + id
                                        }
                                    })
                                    .then(response => {

                                        if(response.body.status) {
                                            var hashid = response.body.record;
                                            this.$router.push({ path: '/roadassist/tracking/' + hashid  });
                                            
                                        } else {
                                            console.log(response.body.error);
                                        }

                                    })
                                    .catch(response => {
                                        console.log(response);
                                    });
                                }
                            }
                            console.log(response);
                        });
                    }

                })
                .catch(response => {
                    console.log(response);
                });
            },

            encode(notes) {
                var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}};

                return Base64.encode(notes);

            },

            goto(dest) {
                this.$router.push({ path: dest })
            },


        }
        
    }
</script>
