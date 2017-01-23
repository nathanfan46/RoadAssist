<style scoped>
    .action-link {
        cursor: pointer;
    }

    .m-b-none {
        margin-bottom: 0;
    }

    .services h4 {
        color: white;
    }

    .map-container {
        height: 430px;
        position: relative;
        width: 100%;
    }

    #gmap {
        position: relative;
        height: 430px;
        width: 100%;
    }

    .map-container .mask {
        opacity: 1;
        background-color:rgba(0,0,0,0.7);
        height: 430px;
        position: absolute;
        width: 100%;
        top: 0;
        left: 0;
        display: table;
        z-index: 10;
    }

    .mask > h2 {
        vertical-align: middle;
        display: table-cell;
    }



</style>
<template>
    <div>
        <div class="row">
            <div class="col-lg-6">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Ticket Number..." :value="ticket.hashid" v-model="ticket.hashid">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="button" @click="tracking()">CHECK</button>
                    </span>
                </div>
            </div>
        </div>
        <br>
        <div class="form-group map-container">
            <div id="gmap"></div>

            <div class="mask" v-if="ticket.showmessage">
                <h2><span class="label label-primary">{{ticket.message}}</span></h2>
            </div>
        </div>
        <br>
        <div class="alert alert-success" v-if="JSON.stringify(ticket.contactinfo) !== JSON.stringify({})">
            <strong>Your ticket is taken!</strong> Please contact: {{ticket.contactinfo.Name}}, {{ticket.contactinfo.Phone}}, {{ticket.contactinfo.Email}}.  
        </div>

        <div class="alert alert-info" v-if="JSON.stringify(ticket.technicianinfo) !== JSON.stringify({})">
            <strong>Your ticket is Assigned!</strong> Your Technician: {{ticket.technicianinfo.FirstName}}, {{ticket.technicianinfo.Phone}}, {{ticket.technicianinfo.Email}}
        </div>

        <div class="form-group services" v-if="ticket.cancellable">
            <button type="button" class="btn btn-primary btn-block" @click="cancelrequest()">CANCEL</button>
        </div>
    </div>

</template>

<script>
    export default {
        data() {
            return {
                location: {
                    latitude: '',
                    longitude: '',
                    imgsrc: '',
                    message: '',
                    found: false
                },
                ticket: {
                    hashid: this.$route.params.hashid,
                    id: '',
                    message: 'finding...',
                    detail: {},
                    contactinfo: {},
                    technicianinfo: {},
                    showmessage: true,
                    cancellable: false
                }
            };
        },

        mounted() {
            if(this.location.latitude === '' || this.location.longitude === '') {
                this.geoFindMe();
            }

            this.tracking();

        },

        methods: {
            tracking() {
                // console.log('refetching...');
                this.$router.push({ path: '/roadassist/tracking/' + this.ticket.hashid });
                this.fetchTicket();

                var self = this;
                setTimeout(function(){
                    self.tracking();
                }, 5000);

            },

            fetchTicket() {
                if(this.ticket.hashid) {
                    this.$http.get('/api/tirepos', {
                        headers : {
                            'X-Proxy-Url' : 'api/roadassist/hashtoid/' + this.ticket.hashid
                        }
                    })
                    .then(response => {

                        if(response.body.status) {
                            this.ticket.id = response.body.record;

                            this.$http.get('/api/tirepos', {
                                headers : {
                                    'X-Proxy-Url' : 'api/roadassist/radetails/' + this.ticket.id
                                }
                            })
                            .then(response => {

                                if(response.body.status && response.body.record) {
                                    this.ticket.detail = response.body.record;
                                }
                                
                                this.ticket.message = (this.ticket.detail.Status == null) ? this.ticket.detail.RAStatus : this.ticket.detail.Status;

                                if(this.ticket.detail.RAStatus.toLowerCase() == 'pending') {
                                    this.ticket.message = 'Waiting';
                                } else if(this.ticket.detail.Status.toLowerCase() == 'taken' || this.ticket.detail.Status.toLowerCase() == 'assigned') {
                                    this.ticket.showmessage = false; //Hide message box
                                }

                                this.checkStatus();



                            })

                        } else {
                            this.ticket.message = response.body.error;
                        }

                    })
                    .catch(response => {
                        console.log(response);
                    });
                }
            },

            checkStatus() {

                if(this.ticket.detail == {}) {
                    this.ticket.showmessage = true;
                    this.ticket.cancellable = false;
                    return;
                }

                if(this.ticket.detail.RAStatus.toLowerCase() == 'pending') {
                    this.ticket.showmessage = true;
                    this.ticket.cancellable = true;
                } else if(this.ticket.detail.Status.toLowerCase() == 'taken') {
                    this.ticket.showmessage = false;
                    this.ticket.cancellable = true;
                    this.ticket.contactinfo = this.ticket.detail.ContactInfo;
                } else if(this.ticket.detail.Status.toLowerCase() == 'assigned') {
                    this.ticket.showmessage = false;
                    this.ticket.cancellable = true;
                    this.ticket.contactinfo = this.ticket.detail.ContactInfo;
                    this.ticket.technicianinfo = this.ticket.detail.TechnicianInfo;
                } else {
                    this.ticket.contactinfo = {};
                    this.ticket.technicianinfo = {};
                    this.ticket.showmessage = true;
                    this.ticket.cancellable = false;
                }
            },

    
            fetchAddress: function(address) {
                var map = new google.maps.Map(document.getElementById('gmap'), {
                    center: address,
                    scrollwheel: false,
                    zoom: 14
                });

                var marker = new google.maps.Marker({
                    position: address,
                    map: map,
                    title: 'Help!'
                });

            },

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

                this.location.imgsrc = "http://maps.googleapis.com/maps/api/staticmap?center=" + this.location.latitude + "," + this.location.longitude + "&zoom=14&size=600x400&markers=color:red|label:A|" + this.location.latitude + "," + this.location.longitude + "&sensor=false";

                this.location.found = true;

                // console.log({lat: this.location.latitude, lng: this.location.longitude});

                this.fetchAddress({lat: this.location.latitude, lng: this.location.longitude});
            },

            geoerror() {
                var geoinfo = document.getElementById("geoinfo");

                this.location.message = "Unable to retrieve your location"; 
            },

            cancelrequest() {
                if(this.ticket.id) {
                    var request = {
                        ticketid: this.ticket.id,
                        status: 'cancelled'
                    }

                    this.$http.post('/api/tirepos', request, {
                        headers : {
                            'X-Proxy-Url' : 'api/roadassist/updateStatus'
                        }
                    })
                    .then(response => {

                        if(response.body.status) {
                            this.tracking();
                        } else {
                            this.ticket.message = response.body.error;
                        }

                    })
                    .catch(response => {
                        console.log(response);
                    });
                }
            }
        }
        
    }
</script>
