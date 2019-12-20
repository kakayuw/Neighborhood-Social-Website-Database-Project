<template>
<el-container>
<el-form ref="form" :model="ProfileForm" label-width="150px" size="large">
  <el-form-item>
    <el-button type="primary" @click="editmode=true" :disabled="editmode">Edit</el-button>
    <el-button v-if="editmode" @click="submitchange()">Save</el-button>
  </el-form-item>
  <el-form-item label="My Name" :rules="[
      { required: true, message: 'Your NAME is required'}    ]">
    <el-input v-model="ProfileForm.name" :disabled="!editmode"></el-input>
  </el-form-item>
  <el-form-item
    label="My Phone Number"
    :rules="[
      { required: true, message: 'phone number is required'},
      { type: 'number', message: 'phone must be a number'}
    ]">
  <el-input type="age"  v-model="ProfileForm.phone" autocomplete="off" placeholder="12312341234" :disabled="!editmode"></el-input>
  </el-form-item>
  <el-form-item label="My Self-introduction">
    <el-input
      type="textarea"
      :rows="3"
      placeholder="I' m xxx! Happy to see you here!"
      v-model="ProfileForm.desc"
      :disabled="!editmode">
    </el-input>
  </el-form-item>
  <el-form-item label="Choose Address from">
    <!-- <el-switch style="display: block" v-model="ProfileForm.usecurrent" active-color="#409EFF" inactive-color="#E6A23C" active-text="Current Location" inactive-text="Map"> -->
    <!-- </el-switch> -->
    <el-radio-group v-model="ProfileForm.usecurrent" :disabled="!editmode">
      <el-radio-button :label="false" @click="initMap()">Map</el-radio-button>
      <el-radio-button :label="true"  @click="getCurrentLocation()">Current</el-radio-button>
    </el-radio-group>
  </el-form-item>
  <el-form-item size="Address" label="Address">
    <h4 id="address">{{ProfileForm.address}}</h4>
  </el-form-item>
  <!-- BEGIN MAP -->
  <el-form-item   v-if="!ProfileForm.usecurrent" label="Map">
    <div style="height:250px; width:400px;">
      <div id="map" style="height:250px;width:400px;"></div>
    </div>
  </el-form-item>
  <el-form-item   v-if="!ProfileForm.usecurrent" label="Lock">
    <el-button type="primary" @click="lockMap()">lock</el-button>
  </el-form-item>
  <!-- END MAP -->
</el-form>
</el-container>
</template>

<script>
  import JQuery from 'jquery'
  import Vue from 'vue';

  export default {
    data() {
      return {
        ProfileForm: {
          name: '',
          phone: '',
          desc: '',
          lat: 0,
          lng: 0,
          address: '',
          desc: '',
          showmap: '',
          usecurrent: true,
          activemap: false,
          marker: '',
          supporter: false,
          map: '',
        },
        currentpos: {lat:0, lng:0},
        editmode: false,
      };
    },
    beforeCreate () {
      self = this;
      $.ajax({
          url: '/dbproj/public/getmyinfo',
          data: { type: "profile"},
          type: "GET",
          dataType: "json",
          success:function (response) {
            var pf = response.profile;
            self.ProfileForm.name = pf.name;
            self.ProfileForm.phone = pf.phone;
            self.ProfileForm.desc = pf.description;
            self.ProfileForm.lat = parseFloat(pf.lat);
            self.ProfileForm.lng = parseFloat(pf.lng);
            self.ProfileForm.address = pf.address;
          },
      });
    },
    mounted () {
      if (navigator.geolocation) {
        var self = this;
        navigator.geolocation.getCurrentPosition(function(position) {
          var pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          self.currentpos = pos;
        }, function() {
          console.log("cannot load")
        });
      } else {
        // Browser doesn't support Geolocation
        alert("Browser doesn't support!");
      }
    },
    updated() {
      console.log("wtf?", this.ProfileForm.usecurrent, this.supporter)
      if (this.ProfileForm.usecurrent) {
        this.getCurrentPosition()
      }
      if (!this.ProfileForm.usecurrent && !this.supporter) {
        this.initMap()
      } 
    },
    methods: {
      onSubmit() {
        console.log('submit!');
      },
      initMap(geolocation) {
        console.log("oh you init map")
        var myLatlng = new google.maps.LatLng(-25.363882,131.044922);
        var mapOptions = {
          zoom: 4,
          center: myLatlng
        }
        var mapppppp = new google.maps.Map(document.getElementById("map"), mapOptions);
        // Place a draggable marker on the map
        var marker = new google.maps.Marker({
            position: myLatlng,
            map: mapppppp,
            draggable:true,
            title:"Drag me!"
        });
        this.marker = marker;
        this.map = mapppppp;
        // if (geolocation) {
          // var contentString = '<div id="content">'+
          //     '<div id="siteNotice">'+
          //     '</div>'+
          //     '<h1 id="firstHeading" class="firstHeading">Uluru</h1>'+
          //     '<div id="bodyContent">'+
          //     '<p>Address:'++'</p>'+
          //     '<p>Location:'++'</p>'+
          //     '<p>Latitude:'++'</p>'+
          //     '<p>Longitude:'++'</p>'+
          //     '</div>'+
          //     '</div>';
          // infowindow = new google.maps.InfoWindow({
          //   content: contentString
          // });
          // var marker = new google.maps.Marker({
          //   position: uluru,
          //   map: map,
          //   title: 'Uluru (Ayers Rock)'
          // });
          // marker.addListener('click', function() {
          //   infowindow.open(map, marker);
          // });
        // }
      },
      lockMap(ll) {
        this.supporter = true;
        // Use reverse geocoding to get address info
        var geocoder = new google.maps.Geocoder;
        var latlng = !ll? {lat:this.marker.position.lat(), lng:this.marker.position.lng()} : ll;
        this.marker.draggable = !this.marker.draggable;
        var self = this;
        geocoder.geocode({'location': latlng}, function(results, status) {
          if (status === 'OK') {
            if (results[0]) {
              console.log(results[0], results[0].formatted_address);
              self.ProfileForm.address = results[0].formatted_address;
              self.ProfileForm.lat = self.marker.position.lat();
              self.ProfileForm.lng = self.marker.position.lng();
            } else {
              console.log('No results found');
            }
          } else {
            console.log('Geocoder failed due to: ' + status);
          }
        });
      },
      getCurrentPosition () {
        this.supporter = false;
        var geocoder = new google.maps.Geocoder;
        var self = this;
        console.log("current:", this.currentpos)
        geocoder.geocode({'location': this.currentpos}, function(results, status) {
          if (status === 'OK' && results[0]) {
            self.ProfileForm.address = results[0].formatted_address;
          } else {
            console.log("Cannot get current location yet!")
          }
        });
      },
      //Submit the whole form
      submitchange() {
        this.editmode = false;
        var self = this;
        $.ajax({
        url: '/dbproj/public/updatemyinfo',
        data: {
          name: self.ProfileForm.name,
          phone:  self.ProfileForm.phone,
          desc: self.ProfileForm.desc,
          lat:  self.currentpos.lat,
          lng: self.currentpos.lng,
          address:  self.ProfileForm.address,
        },
        type: "GET",
        dataType: "json",
        success:function (response) {
          self.$message('Changes have been submitted');
        },
      });
      }
    },
}  

  
</script>